<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\CompanyMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $query = Employee::with(['user', 'company', 'checker']);

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('emp_id', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            }

            $employees = $query->paginate($perPage);

            return Inertia::render('Employees/Index', [
                'auth' => auth()->user(),
                'employees' => $employees,
                'filters' => ['search' => $search, 'per_page' => $perPage],
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch Employees: ' . $e->getMessage());
            return back()->with('error', 'Failed to load employees.');
        }
    }

    public function create()
    {
        try {
            return Inertia::render('Employees/Form', [
                'auth' => auth()->user(),
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
                'checkers' => Employee::whereIn('role', ['checker', 'po_checker', 'finance_checker', 'backout_checker'])
                    ->select('id', 'name', 'emp_id', 'company_id', 'role')
                    ->get()
                    ->mapWithKeys(function ($employee) {
                        return [
                            $employee->id => [
                                'label' => "{$employee->name} ({$employee->emp_id})",
                                'company_id' => $employee->company_id,
                                'role' => $employee->role,
                            ]
                        ];
                    })
                    ->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load Employee create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store request data:', $request->all()); // Debug input

            $request->validate([
                'emp_id' => 'required|unique:employees,emp_id',
                'name' => 'required|string|max:255',
                'company_id' => 'required|exists:company_masters,id',
                'email' => 'nullable|email|unique:users,email|unique:employees,email',
                'phone' => 'nullable|unique:employees,phone',
                'role' => 'nullable|in:maker,checker,po_maker,po_checker,finance_maker,finance_checker,backout_maker,backout_checker',
                'checker_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($request) {
                        $role = $request->input('role');
                        $isSelfChecker = $request->input('is_self_checker');
                        if (
                            in_array($role, ['maker', 'po_maker', 'finance_maker', 'backout_maker']) &&
                            !$isSelfChecker &&
                            !$value
                        ) {
                            $fail('Checker is required for maker roles unless self-checker is selected.');
                        }
                        if ($value && !$isSelfChecker && in_array($role, ['maker', 'po_maker', 'finance_maker', 'backout_maker'])) {
                            $checker = Employee::where('id', $value)->first();
                            if (!$checker) {
                                $fail('Selected checker does not exist.');
                            }
                            if ($role === 'maker' && $checker->company_id != $request->input('company_id')) {
                                $fail('Checker must belong to the same company for maker role.');
                            }
                            if ($role === 'po_maker' && $checker->role !== 'po_checker') {
                                $fail('Checker must have po_checker role for po_maker.');
                            }
                            if ($role === 'finance_maker' && $checker->role !== 'finance_checker') {
                                $fail('Checker must have finance_checker role for finance_maker.');
                            }
                            if ($role === 'backout_maker' && $checker->role !== 'backout_checker') {
                                $fail('Checker must have backout_checker role for backout_maker.');
                            }
                        }
                    },
                ],
                'is_self_checker' => 'nullable|boolean',
                'designation' => 'required|in:AM,DM,TL,Recruiter',
                'status' => 'nullable|in:active,inactive',
            ]);

            $employee = DB::transaction(function () use ($request) {
                $data = $request->only([
                    'emp_id',
                    'name',
                    'company_id',
                    'email',
                    'phone',
                    'role',
                    'checker_id',
                    'is_self_checker',
                    'designation',
                    'status',
                ]);

                // Ensure is_self_checker is false if not provided
                $data['is_self_checker'] = $data['is_self_checker'] ?? false;

                // Only create a User if email is provided
                $data['user_id'] = null;
                if (!empty($data['email'])) {
                    $password = "alois@{$data['emp_id']}";
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role' => 'employee',
                        'password' => Hash::make($password),
                    ]);
                    $data['user_id'] = $user->id;
                }

                if ($data['is_self_checker']) {
                    $data['checker_id'] = null; // Set to null initially, updated after creation
                }
                $employee = Employee::create($data);

                if ($data['is_self_checker']) {
                    $employee->update(['checker_id' => $employee->id]);
                }

                return $employee;
            });

            return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed: ' . implode(', ', array_values($e->errors())[0]));
        } catch (\Exception $e) {
            Log::error('Failed to create Employee: ' . $e->getMessage());
            return back()->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        try {
            $employee->load(['user', 'company', 'checker']);
            return Inertia::render('Employees/Show', [
                'auth' => auth()->user(),
                'employee' => $employee,
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
                'checkers' => Employee::whereIn('role', ['checker', 'po_checker', 'finance_checker', 'backout_checker'])
                    ->select('id', 'name', 'emp_id')
                    ->get()
                    ->mapWithKeys(function ($employee) {
                        return [$employee->id => "{$employee->name} ({$employee->emp_id})"];
                    })
                    ->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to show Employee: ' . $e->getMessage());
            return back()->with('error', 'Failed to show employee.');
        }
    }

    public function edit(Employee $employee)
    {
        try {
            $employee->load(['user', 'company', 'checker']);
            return Inertia::render('Employees/Form', [
                'auth' => auth()->user(),
                'employee' => $employee,
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
                'checkers' => $employee->is_self_checker
                    ? [
                        $employee->id => [
                            'label' => "{$employee->name} ({$employee->emp_id})",
                            'company_id' => $employee->company_id,
                            'role' => $employee->role,
                        ]
                    ]
                    : Employee::whereIn('role', ['checker', 'po_checker', 'finance_checker', 'backout_checker'])
                        ->select('id', 'name', 'emp_id', 'company_id', 'role')
                        ->get()
                        ->mapWithKeys(function ($emp) {
                            return [
                                $emp->id => [
                                    'label' => "{$emp->name} ({$emp->emp_id})",
                                    'company_id' => $emp->company_id,
                                    'role' => $emp->role,
                                ]
                            ];
                        })
                        ->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load Employee edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            $request->validate([
                'emp_id' => 'required|unique:employees,emp_id,' . $employee->id,
                'name' => 'required|string|max:255',
                'company_id' => 'required|exists:company_masters,id',
                'email' => 'nullable|email|unique:employees,email,' . $employee->id . '|unique:users,email,' . ($employee->user_id ?? 0),
                'phone' => 'nullable|unique:employees,phone,' . $employee->id,
                'role' => 'nullable|in:maker,checker,po_maker,po_checker,finance_maker,finance_checker,backout_maker,backout_checker',
                'checker_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($request) {
                        $role = $request->input('role');
                        $isSelfChecker = $request->input('is_self_checker');
                        if (
                            in_array($role, ['maker', 'po_maker', 'finance_maker', 'backout_maker']) &&
                            !$isSelfChecker &&
                            !$value
                        ) {
                            $fail('Checker is required for maker roles unless self-checker is selected.');
                        }
                        if ($value && !$isSelfChecker && in_array($role, ['maker', 'po_maker', 'finance_maker', 'backout_maker'])) {
                            $checker = Employee::where('id', $value)->first();
                            if (!$checker) {
                                $fail('Selected checker does not exist.');
                            }
                            if ($role === 'maker' && $checker->company_id != $request->input('company_id')) {
                                $fail('Checker must belong to the same company for maker role.');
                            }
                            if ($role === 'po_maker' && $checker->role !== 'po_checker') {
                                $fail('Checker must have po_checker role for po_maker.');
                            }
                            if ($role === 'finance_maker' && $checker->role !== 'finance_checker') {
                                $fail('Checker must have finance_checker role for finance_maker.');
                            }
                            if ($role === 'backout_maker' && $checker->role !== 'backout_checker') {
                                $fail('Checker must have backout_checker role for backout_maker.');
                            }
                        }
                    },
                ],
                'is_self_checker' => 'nullable|boolean',
                'designation' => 'required|in:AM,DM,TL,Recruiter',
                'status' => 'nullable|in:active,inactive',
                'old_password' => 'nullable|string',
                'new_password' => 'nullable|string|min:8|required_with:old_password',
            ]);

            DB::transaction(function () use ($request, $employee) {
                $data = $request->only([
                    'emp_id',
                    'name',
                    'company_id',
                    'email',
                    'phone',
                    'role',
                    'checker_id',
                    'is_self_checker',
                    'designation',
                    'status',
                ]);

                // Ensure is_self_checker is false if not provided
                $data['is_self_checker'] = $data['is_self_checker'] ?? false;

                if ($data['is_self_checker']) {
                    $data['checker_id'] = $employee->id;
                }

                $employee->update($data);

                // Handle user record
                $user = $employee->user;
                if (!empty($data['email'])) {
                    if ($user) {
                        // Update existing user
                        $userUpdateData = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                        ];

                        if ($employee->isDirty('emp_id')) {
                            $userUpdateData['password'] = Hash::make("alois@{$data['emp_id']}");
                        } elseif ($request->filled('old_password') && $request->filled('new_password')) {
                            if (!Hash::check($request->input('old_password'), $user->password)) {
                                throw new \Exception('Old password is incorrect');
                            }
                            $userUpdateData['password'] = Hash::make($request->input('new_password'));
                        }

                        $user->update($userUpdateData);
                    } else {
                        // Create new user if none exists
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'role' => 'employee',
                            'password' => Hash::make("alois@{$data['emp_id']}"),
                        ]);
                        $employee->update(['user_id' => $user->id]);
                    }
                } elseif ($user) {
                    // Delete user if email is removed
                    $user->delete();
                    $employee->update(['user_id' => null]);
                }
            });

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in update: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed: ' . implode(', ', array_values($e->errors())[0]));
        } catch (\Exception $e) {
            Log::error('Failed to update Employee: ' . $e->getMessage());
            return back()->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            DB::transaction(function () use ($employee) {
                if ($employee->user) {
                    $employee->user->delete();
                }
                $employee->delete();
            });

            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete Employee: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $allColumns = ['emp_id', 'name', 'company_id', 'email', 'phone', 'role', 'checker_emp_id', 'designation', 'status'];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set column headers
            foreach ($allColumns as $index => $columnName) {
                $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter . '1', $columnName);
            }

            // Get company names for dropdown
            $companies = CompanyMaster::pluck('name')->toArray();
            if (empty($companies)) {
                throw new \Exception('No companies found in the database for the dropdown.');
            }

            // Sanitize company names to avoid breaking dropdown
            $sanitizedCompanies = array_map(function ($name) {
                return str_replace([',', '"'], ['-', ''], trim($name));
            }, $companies);

            // Create comma-separated list for company_id dropdown
            $companyList = implode(',', $sanitizedCompanies);
            if (strlen($companyList) > 255) {
                Log::warning('Company list exceeds Excel formula limit (255 characters). Truncating to first 50 companies.');
                $sanitizedCompanies = array_slice($sanitizedCompanies, 0, 50);
                $companyList = implode(',', $sanitizedCompanies);
            }

            // Add data validation for company_id (Column C, starting from C2)
            $companyValidation = new DataValidation();
            $companyValidation->setType(DataValidation::TYPE_LIST);
            $companyValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $companyValidation->setAllowBlank(false);
            $companyValidation->setShowDropDown(true);
            $companyValidation->setFormula1('"' . $companyList . '"');
            $companyValidation->setErrorTitle('Invalid Company');
            $companyValidation->setError('Please select a valid company.');
            $sheet->setDataValidation('C2:C1048576', $companyValidation); // Entire column C from C2

            // Add data validation for email (Column D, starting from D2)
            $emailValidation = new DataValidation();
            $emailValidation->setType(DataValidation::TYPE_CUSTOM);
            $emailValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $emailValidation->setAllowBlank(true);
            $emailValidation->setShowInputMessage(true);
            $emailValidation->setPromptTitle('Email');
            $emailValidation->setPrompt('Enter a valid email address or leave blank');
            $emailValidation->setErrorTitle('Invalid Email');
            $emailValidation->setError('Please enter a valid email address or leave blank.');
            $sheet->setDataValidation('D2:D1048576', $emailValidation); // Entire column D from D2

            // Add data validation for phone (Column E, starting from E2)
            $phoneValidation = new DataValidation();
            $phoneValidation->setType(DataValidation::TYPE_CUSTOM);
            $phoneValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $phoneValidation->setAllowBlank(true);
            $phoneValidation->setShowInputMessage(true);
            $phoneValidation->setPromptTitle('Phone');
            $phoneValidation->setPrompt('Enter a valid phone number or leave blank');
            $phoneValidation->setErrorTitle('Invalid Phone');
            $phoneValidation->setError('Please enter a valid phone number or leave blank.');
            $sheet->setDataValidation('E2:E1048576', $phoneValidation);

            // Add data validation for role (Column F, starting from F2)
            $roleValidation = new DataValidation();
            $roleValidation->setType(DataValidation::TYPE_LIST);
            $roleValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $roleValidation->setAllowBlank(true);
            $roleValidation->setShowDropDown(true);
            $roleValidation->setFormula1('"maker,checker,po_maker,po_checker,finance_maker,finance_checker,backout_maker,backout_checker"');
            $roleValidation->setErrorTitle('Invalid Role');
            $roleValidation->setError('Please select a valid role or leave blank.');
            $sheet->setDataValidation('F2:F1048576', $roleValidation); // Entire column F from F2

            // Get checker emp_ids for dropdown
            $checkers = Employee::whereIn('role', ['checker', 'po_checker', 'finance_checker', 'backout_checker'])
                ->pluck('name', 'emp_id')
                ->toArray();
            $sanitizedCheckers = array_map(function ($name) {
                return str_replace([',', '"'], ['-', ''], trim($name));
            }, $checkers);
            $checkerList = implode(',', array_values($sanitizedCheckers));
            if (strlen($checkerList) > 255) {
                Log::warning('Checker name list exceeds Excel formula limit (255 characters). Truncating to first 50 names.');
                $checkerList = implode(',', array_slice(array_values($sanitizedCheckers), 0, 50));
            }

            // Add data validation for checker_emp_id (Column G, starting from G2)
            $checkerValidation = new DataValidation();
            $checkerValidation->setType(DataValidation::TYPE_LIST);
            $checkerValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $checkerValidation->setAllowBlank(true);
            $checkerValidation->setShowDropDown(true);
            $checkerValidation->setFormula1('"' . $checkerList . '"');
            $checkerValidation->setErrorTitle('Invalid Checker');
            $checkerValidation->setError('Please select a valid checker emp_id or leave blank.');
            $sheet->setDataValidation('G2:G1048576', $checkerValidation); // Entire column G from G2

            // Add data validation for designation (Column H, starting from H2)
            $designationValidation = new DataValidation();
            $designationValidation->setType(DataValidation::TYPE_LIST);
            $designationValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $designationValidation->setAllowBlank(false);
            $designationValidation->setShowDropDown(true);
            $designationValidation->setFormula1('"AM,DM,TL,Recruiter"');
            $designationValidation->setErrorTitle('Invalid Designation');
            $designationValidation->setError('Please select a valid designation.');
            $sheet->setDataValidation('H2:H1048576', $designationValidation); // Entire column H from H2

            // Add data validation for status (Column I, starting from I2)
            $statusValidation = new DataValidation();
            $statusValidation->setType(DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $statusValidation->setAllowBlank(true);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1('"active,inactive"');
            $statusValidation->setErrorTitle('Invalid Status');
            $statusValidation->setError('Please select active or inactive or leave blank.');
            $sheet->setDataValidation('I2:I1048576', $statusValidation); // Entire column I from I2

            $writer = new Xlsx($spreadsheet);
            $filename = 'employee_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to download Employee template: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    public function importFromExcel(Request $request)
    {
        try {
            $request->validate(['file' => 'required|mimes:xlsx,xls']);

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $headers = $rows[0] ?? [];
            $requiredFields = ['emp_id', 'name', 'company_id', 'email', 'phone', 'designation'];
            $optionalFields = ['role', 'checker_emp_id', 'status'];
            $fieldIndices = [];
            foreach ($requiredFields as $field) {
                $index = array_search($field, $headers);
                if ($index === false) {
                    throw new \Exception("Required column '$field' not found in the Excel file.");
                }
                $fieldIndices[$field] = $index;
            }
            foreach ($optionalFields as $field) {
                $index = array_search($field, $headers);
                if ($index !== false) {
                    $fieldIndices[$field] = $index;
                }
            }

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $data = [];
                foreach ($fieldIndices as $field => $colIndex) {
                    $value = trim($row[$colIndex] ?? '');

                    // Convert empty strings to null
                    $data[$field] = $value !== '' ? $value : null;
                }

                if (
                    empty($data['emp_id']) || empty($data['name']) || empty($data['company_id']) ||
                    empty($data['designation'])
                ) {
                    Log::warning("Skipping row $index: Missing required fields.");
                    continue;
                }

                // Map company name to ID
                $company = CompanyMaster::where('name', $data['company_id'])->first();
                if (!$company) {
                    Log::warning("Company not found in row $index: {$data['company_id']}");
                    continue;
                }
                $data['company_id'] = $company->id;

                // Validate role if provided
                if (!empty($data['role']) && !in_array($data['role'], ['maker', 'checker', 'po_maker', 'po_checker', 'finance_maker', 'finance_checker', 'backout_maker', 'backout_checker'])) {
                    Log::warning("Invalid role in row $index: {$data['role']}");
                    continue;
                }

                // Validate checker_emp_id
                if (!empty($data['checker_emp_id'])) {
                    $checker = Employee::where('name', $data['checker_emp_id'])
                        ->whereIn('role', ['checker', 'po_checker', 'finance_checker', 'backout_checker'])
                        ->first();
                    if (!$checker) {
                        Log::warning("Invalid checker_emp_id in row $index: {$data['checker_emp_id']}");
                        continue;
                    }
                    $data['checker_id'] = $checker->id; // Map emp_id to checker_id
                } elseif (in_array($data['role'], ['maker', 'po_maker', 'finance_maker', 'backout_maker']) && empty($data['checker_emp_id'])) {
                    Log::warning("Checker emp_id required for maker role in row $index.");
                    continue;
                } else {
                    $data['checker_id'] = null;
                }

                // Validate designation
                if (!in_array($data['designation'], ['AM', 'DM', 'TL', 'Recruiter'])) {
                    Log::warning("Invalid designation in row $index: {$data['designation']}");
                    continue;
                }

                // Validate status
                if (!empty($data['status']) && !in_array(strtolower($data['status']), ['active', 'inactive'])) {
                    Log::warning("Invalid status in row $index: {$data['status']}");
                    continue;
                }
                $data['status'] = !empty($data['status']) ? strtolower($data['status']) : 'active';

                DB::transaction(function () use ($data, &$importedCount) {
                    $data['user_id'] = null;
                    if (!empty($data['email'])) {
                        $password = "alois@{$data['emp_id']}";
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'role' => 'employee',
                            'password' => Hash::make($password),
                        ]);
                        $data['user_id'] = $user->id;
                    }

                    unset($data['checker_emp_id']); // Remove checker_emp_id as it's mapped to checker_id
                    Employee::create($data);
                    $importedCount++;
                });
            }

            return redirect()->route('employees.index')->with('success', "Excel imported successfully. {$importedCount} employees added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            Log::error('Failed to import Excel: ' . $e->getMessage());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}