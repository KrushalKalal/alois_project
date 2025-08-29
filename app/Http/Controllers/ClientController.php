<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanyMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $tab = $request->input('tab', 'permanent');
            $query = Client::query()->with('company');

            if ($search) {
                $query->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_code', 'like', "%{$search}%");
            }
            $query->where('client_status', $tab === 'temporary' ? 0 : 1);

            $clients = $query->paginate($perPage);

            return Inertia::render('Clients/Index', [
                'auth' => auth()->user(),
                'clients' => $clients,
                'filters' => ['search' => $search, 'per_page' => $perPage, 'tab' => $tab],
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Clients: ' . $e->getMessage());
            return back()->with('error', 'Failed to load clients.');
        }
    }

    public function create()
    {
        try {
            return Inertia::render('Clients/Form', [
                'auth' => auth()->user(),
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Client create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_code' => 'nullable|unique:clients,client_code',
                'client_name' => 'required',
                'company_id' => 'nullable|exists:company_masters,id',
                'client_status' => 'required|in:0,1',
                'loaded_cost' => 'required|integer|min:1',
                'qualify_days' => 'required|integer|min:1',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'status' => 'nullable|in:active,inactive',
            ]);

            Client::create($request->only([
                'client_code',
                'client_name',
                'company_id',
                'client_status',
                'loaded_cost',
                'qualify_days',
                'phone',
                'email',
                'status',
            ]));

            return redirect()->route('clients.index')->with('success', 'Client created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Client: ' . $e->getMessage());
            return back()->with('error', 'Failed to create client: ' . $e->getMessage());
        }
    }

    public function show(Client $client)
    {
        try {
            $client->load('company');
            return Inertia::render('Clients/Show', [
                'auth' => auth()->user(),
                'client' => $client,
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Client: ' . $e->getMessage());
            return back()->with('error', 'Failed to show client.');
        }
    }

    public function edit(Client $client)
    {
        try {
            $client->load('company');
            return Inertia::render('Clients/Form', [
                'auth' => auth()->user(),
                'client' => $client,
                'companies' => CompanyMaster::pluck('name', 'id')->toArray(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Client edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Client $client)
    {
        try {
            $request->validate([
                'client_code' => 'nullable|unique:clients,client_code,' . $client->id,
                'client_name' => 'required',
                'company_id' => 'nullable|exists:company_masters,id',
                'client_status' => 'required|in:0,1',
                'loaded_cost' => 'required|integer|min:1',
                'qualify_days' => 'required|integer|min:1',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'status' => 'nullable|in:active,inactive',
            ]);

            $client->update($request->only([
                'client_code',
                'client_name',
                'company_id',
                'client_status',
                'loaded_cost',
                'qualify_days',
                'phone',
                'email',
                'status',
            ]));

            return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to update Client: ' . $e->getMessage());
            return back()->with('error', 'Failed to update client: ' . $e->getMessage());
        }
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Client: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete client: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $allColumns = ['client_code', 'client_name', 'company_id', 'client_status', 'loaded_cost', 'qualify_days', 'phone', 'email', 'status'];
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
                \Log::warning('Company list exceeds Excel formula limit (255 characters). Truncating to first 50 companies.');
                $sanitizedCompanies = array_slice($sanitizedCompanies, 0, 50);
                $companyList = implode(',', $sanitizedCompanies);
            }

            // Add data validation for company_id (Column C, starting from C2)
            $companyValidation = new DataValidation();
            $companyValidation->setType(DataValidation::TYPE_LIST);
            $companyValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $companyValidation->setAllowBlank(true);
            $companyValidation->setShowDropDown(true);
            $companyValidation->setFormula1('"' . $companyList . '"');
            $companyValidation->setErrorTitle('Invalid Company');
            $companyValidation->setError('Please select a valid company or leave blank.');
            $sheet->setDataValidation('C2:C1048576', $companyValidation); // Entire column C from C2

            // Add data validation for client_status (Column D, starting from D2)
            $statusValidation = new DataValidation();
            $statusValidation->setType(DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $statusValidation->setAllowBlank(false);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1('"Temporary,Permanent"');
            $statusValidation->setErrorTitle('Invalid Client Status');
            $statusValidation->setError('Please select Temporary or Permanent.');
            $sheet->setDataValidation('D2:D1048576', $statusValidation); // Entire column D from D2

            // Add data validation for status (Column I, starting from I2)
            $activeInactiveValidation = new DataValidation();
            $activeInactiveValidation->setType(DataValidation::TYPE_LIST);
            $activeInactiveValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $activeInactiveValidation->setAllowBlank(true);
            $activeInactiveValidation->setShowDropDown(true);
            $activeInactiveValidation->setFormula1('"active,inactive"');
            $activeInactiveValidation->setErrorTitle('Invalid Status');
            $activeInactiveValidation->setError('Please select active or inactive or leave blank.');
            $sheet->setDataValidation('I2:I1048576', $activeInactiveValidation); // Entire column I from I2

            // Add data validation for phone (Column G, starting from G2)
            $phoneValidation = new DataValidation();
            $phoneValidation->setType(DataValidation::TYPE_CUSTOM);
            $phoneValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $phoneValidation->setAllowBlank(true);
            $phoneValidation->setFormula1('LEN(G2)<=20');
            $phoneValidation->setErrorTitle('Invalid Phone');
            $phoneValidation->setError('Phone number must be 20 characters or less or blank.');
            $sheet->setDataValidation('G2:G1048576', $phoneValidation); // Entire column G from G2

            // Add data validation for email (Column H, starting from H2)
            $emailValidation = new DataValidation();
            $emailValidation->setType(DataValidation::TYPE_CUSTOM);
            $emailValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $emailValidation->setAllowBlank(true);
            $emailValidation->setFormula1('OR(ISBLANK(H2),ISNUMBER(FIND("@",H2)))');
            $emailValidation->setErrorTitle('Invalid Email');
            $emailValidation->setError('Please enter a valid email address or leave blank.');
            $sheet->setDataValidation('H2:H1048576', $emailValidation); // Entire column H from H2

            $writer = new Xlsx($spreadsheet);
            $filename = 'client_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Client template: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
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
            \Log::info('Raw headers: ' . json_encode($headers));

            $requiredFields = ['client_name', 'client_status', 'loaded_cost', 'qualify_days'];
            $optionalFields = ['client_code', 'company_id', 'phone', 'email', 'status'];
            $fieldIndices = [];
            foreach ($requiredFields as $field) {
                $index = array_search(strtolower($field), array_map('strtolower', $headers));
                if ($index === false) {
                    throw new \Exception("Required column '$field' not found in the Excel file.");
                }
                $fieldIndices[$field] = $index;
            }
            foreach ($optionalFields as $field) {
                $index = array_search(strtolower($field), array_map('strtolower', $headers));
                if ($index !== false) {
                    $fieldIndices[$field] = $index;
                }
            }

            \Log::info('Field indices: ' . json_encode($fieldIndices));

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $data = [];
                foreach ($fieldIndices as $field => $colIndex) {
                    $value = $row[$colIndex] ?? '';
                    $data[$field] = trim((string) $value) === '-' ? null : trim((string) $value);
                }

                \Log::info("Raw row $index data: " . json_encode($data));

                if (
                    $data['client_name'] === null || $data['client_name'] === '' ||
                    $data['client_status'] === null || $data['client_status'] === '' ||
                    $data['loaded_cost'] === null || $data['loaded_cost'] === '' ||
                    $data['qualify_days'] === null || $data['qualify_days'] === ''
                ) {
                    \Log::warning("Skipping row $index: Missing required fields - " . json_encode($data));
                    continue;
                }

                // Map client_status to 0 or 1
                $clientStatus = null;
                if (strtolower($data['client_status']) === 'temporary' || $data['client_status'] === '0') {
                    $clientStatus = 0;
                } elseif (strtolower($data['client_status']) === 'permanent' || $data['client_status'] === '1') {
                    $clientStatus = 1;
                } else {
                    \Log::warning("Invalid client_status in row $index: {$data['client_status']}");
                    continue;
                }
                $data['client_status'] = $clientStatus;

                // Map company name to ID (if provided)
                if (!empty($data['company_id'])) {
                    $company = CompanyMaster::where('name', $data['company_id'])->first();
                    if (!$company) {
                        \Log::warning("Company not found in row $index: {$data['company_id']}");
                        $data['company_id'] = null;
                    } else {
                        $data['company_id'] = $company->id;
                    }
                }

                // Validate loaded_cost as integer (allow 0)
                if (!is_numeric($data['loaded_cost'])) {
                    \Log::warning("Invalid loaded_cost format in row $index: {$data['loaded_cost']}");
                    continue;
                }
                $data['loaded_cost'] = (int) $data['loaded_cost'];

                // Validate qualify_days as integer (allow 0)
                if (!is_numeric($data['qualify_days'])) {
                    \Log::warning("Invalid qualify_days in row $index: {$data['qualify_days']}");
                    continue;
                }
                $data['qualify_days'] = (int) $data['qualify_days'];

                // Validate status
                if (!empty($data['status']) && !in_array(strtolower($data['status']), ['active', 'inactive'])) {
                    \Log::warning("Invalid status in row $index: {$data['status']}");
                    continue;
                }
                $data['status'] = !empty($data['status']) ? strtolower($data['status']) : 'active';

                // Validate phone
                if (!empty($data['phone']) && strlen($data['phone']) > 20) {
                    \Log::warning("Invalid phone format in row $index: {$data['phone']}");
                    continue;
                }

                // Validate email
                if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    \Log::warning("Invalid email format in row $index: {$data['email']}");
                    continue;
                }

                // Validate client_code uniqueness
                if (!empty($data['client_code']) && Client::where('client_code', $data['client_code'])->exists()) {
                    \Log::warning("Duplicate client_code in row $index: {$data['client_code']}");
                    continue;
                }

                Client::create($data);
                $importedCount++;
            }

            if ($importedCount === 0) {
                \Log::warning("No valid data rows found in the Excel file.");
                return redirect()->route('clients.index')->with('warning', 'Excel imported successfully, but no valid data rows were found.');
            }

            return redirect()->route('clients.index')->with('success', "Excel imported successfully. {$importedCount} clients added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}
