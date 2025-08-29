<?php
namespace App\Http\Controllers;

use App\Models\BranchMaster;
use App\Models\CompanyMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class BranchMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $tab = $request->input('tab', 'permanent');
            $query = BranchMaster::with('company');

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }

            $query->where('branch_status', $tab === 'temporary' ? 0 : 1);
            $branches = $query->paginate($perPage);

            return Inertia::render('BranchMasters/Index', [
                'auth' => auth()->user(),
                'branches' => $branches,
                'filters' => ['search' => $search, 'per_page' => $perPage, 'tab' => $tab],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Branch Masters: ' . $e->getMessage());
            return back()->with('error', 'Failed to load branches.');
        }
    }

    public function create()
    {
        try {
            $companies = CompanyMaster::all();
            return Inertia::render('BranchMasters/Form', [
                'auth' => auth()->user(),
                'companies' => $companies
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Branch Master create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'company_id' => 'required|exists:company_masters,id',
                'branch_status' => 'required|in:0,1',
            ]);

            BranchMaster::create($request->only('name', 'company_id', 'branch_status'));

            return redirect()->route('branch-masters.index')->with('success', 'Branch created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Branch Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to create branch: ' . $e->getMessage());
        }
    }

    public function show(BranchMaster $branchMaster)
    {
        try {
            return Inertia::render('BranchMasters/Show', [
                'auth' => auth()->user(),
                'branch' => $branchMaster->load('company'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Branch Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to show branch.');
        }
    }

    public function edit(BranchMaster $branchMaster)
    {
        try {
            return Inertia::render('BranchMasters/Form', [
                'auth' => auth()->user(),
                'branch' => $branchMaster->load('company'),
                'companies' => CompanyMaster::all(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Branch Master edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, BranchMaster $branchMaster)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'company_id' => 'required|exists:company_masters,id',
                'branch_status' => 'required|in:0,1',
            ]);

            $branchMaster->update($validated);

            return redirect()->route('branch-masters.index')->with('success', 'Branch updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update Branch Master: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update branch: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(BranchMaster $branchMaster)
    {
        try {
            $branchMaster->delete();
            return redirect()->route('branch-masters.index')->with('success', 'Branch deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Branch Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete branch: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $allColumns = ['name', 'company_id', 'branch_status'];
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

            // Add data validation for company_id (Column B, starting from B2)
            $companyValidation = new DataValidation();
            $companyValidation->setType(DataValidation::TYPE_LIST);
            $companyValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $companyValidation->setAllowBlank(false);
            $companyValidation->setShowDropDown(true);
            $companyValidation->setFormula1('"' . $companyList . '"');
            $companyValidation->setErrorTitle('Invalid Company');
            $companyValidation->setError('Please select a valid company.');
            $sheet->setDataValidation('B2:B1048576', $companyValidation); // Entire column B from B2

            // Add data validation for branch_status (Column C, starting from C2)
            $statusValidation = new DataValidation();
            $statusValidation->setType(DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $statusValidation->setAllowBlank(false);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1('"Temporary,Permanent"');
            $statusValidation->setErrorTitle('Invalid Status');
            $statusValidation->setError('Please select Temporary or Permanent.');
            $sheet->setDataValidation('C2:C1048576', $statusValidation); // Entire column C from C2

            $writer = new Xlsx($spreadsheet);
            $filename = 'branch_master_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Branch Master template: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
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
            $nameIndex = array_search('name', $headers);
            $companyNameIndex = array_search('company_id', $headers);
            $statusIndex = array_search('branch_status', $headers);

            if ($nameIndex === false || $companyNameIndex === false || $statusIndex === false) {
                throw new \Exception("Required columns (name, company_id, branch_status) not found.");
            }

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $name = trim($row[$nameIndex] ?? '');
                $companyName = trim($row[$companyNameIndex] ?? '');
                $status = trim($row[$statusIndex] ?? '');

                if (empty($name) || empty($companyName) || empty($status)) {
                    \Log::warning("Skipping row $index: Missing name, company, or status.");
                    continue;
                }

                // Map status to 0 or 1
                $branchStatus = null;
                if (strtolower($status) === 'temporary' || $status === '0') {
                    $branchStatus = 0;
                } elseif (strtolower($status) === 'permanent' || $status === '1') {
                    $branchStatus = 1;
                } else {
                    \Log::warning("Invalid status in row $index: $status");
                    continue;
                }

                // Map company name to ID
                $company = CompanyMaster::where('name', $companyName)->first();
                if (!$company) {
                    \Log::warning("Company not found in row $index: $companyName");
                    continue;
                }

                BranchMaster::create([
                    'name' => $name,
                    'company_id' => $company->id,
                    'branch_status' => $branchStatus,
                ]);
                $importedCount++;
            }

            return redirect()->route('branch-masters.index')->with('success', "Excel imported successfully. {$importedCount} branches added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}