<?php
namespace App\Http\Controllers;

use App\Models\BusinessUnitMaster;
use App\Models\CompanyMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class BusinessUnitMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $tab = $request->input('tab', 'permanent');
            $query = BusinessUnitMaster::with('company');

            if ($search) {
                $query->where('unit', 'like', "%{$search}%")
                    ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }

            $query->where('unit_status', $tab === 'temporary' ? 0 : 1);
            $units = $query->paginate($perPage);

            return Inertia::render('BusinessUnitMasters/Index', [
                'auth' => auth()->user(),
                'units' => $units,
                'filters' => ['search' => $search, 'per_page' => $perPage, 'tab' => $tab],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Business Unit Masters: ' . $e->getMessage());
            return back()->with('error', 'Failed to load business units.');
        }
    }

    public function create()
    {
        try {
            $companies = CompanyMaster::all();
            return Inertia::render('BusinessUnitMasters/Form', [
                'auth' => auth()->user(),
                'companies' => $companies
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Business Unit Master create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'unit' => 'required',
                'company_id' => 'required|exists:company_masters,id',
                'unit_status' => 'required|in:0,1',
            ]);

            BusinessUnitMaster::create($request->only('unit', 'company_id', 'unit_status'));

            return redirect()->route('business-unit-masters.index')->with('success', 'Business Unit created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Business Unit Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to create business unit: ' . $e->getMessage());
        }
    }

    public function show(BusinessUnitMaster $businessUnitMaster)
    {
        try {
            return Inertia::render('BusinessUnitMasters/Show', [
                'auth' => auth()->user(),
                'unit' => $businessUnitMaster->load('company'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Business Unit Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to show business unit.');
        }
    }

    public function edit(BusinessUnitMaster $businessUnitMaster)
    {
        try {
            return Inertia::render('BusinessUnitMasters/Form', [
                'auth' => auth()->user(),
                'unit' => $businessUnitMaster->load('company'),
                'companies' => CompanyMaster::all(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Business Unit Master edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, BusinessUnitMaster $businessUnitMaster)
    {
        try {
            $validated = $request->validate([
                'unit' => 'required',
                'company_id' => 'required|exists:company_masters,id',
                'unit_status' => 'required|in:0,1',
            ]);

            $businessUnitMaster->update($validated);

            return redirect()->route('business-unit-masters.index')->with('success', 'Business Unit updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update Business Unit Master: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update business unit: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(BusinessUnitMaster $businessUnitMaster)
    {
        try {
            $businessUnitMaster->delete();

            return redirect()->route('business-unit-masters.index')->with('success', 'Business Unit deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Business Unit Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete business unit: ' . $e->getMessage());
        }
    }
    public function downloadExcelTemplate()
    {
        try {
            $allColumns = ['unit', 'company_id', 'unit_status'];
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

            // Add data validation for unit_status (Column C, starting from C2)
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
            $filename = 'business_unit_master_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Business Unit Master template: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
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
            $unitIndex = array_search('unit', $headers);
            $companyNameIndex = array_search('company_id', $headers);
            $statusIndex = array_search('unit_status', $headers);

            if ($unitIndex === false || $companyNameIndex === false || $statusIndex === false) {
                throw new \Exception("Required columns (unit, company_id, unit_status) not found.");
            }

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $unit = trim($row[$unitIndex] ?? '');
                $companyName = trim($row[$companyNameIndex] ?? '');
                $status = trim($row[$statusIndex] ?? '');

                if (empty($unit) || empty($companyName) || empty($status)) {
                    \Log::warning("Skipping row $index: Missing unit, company, or status.");
                    continue;
                }

                // Map status to 0 or 1
                $unitStatus = null;
                if (strtolower($status) === 'temporary' || $status === '0') {
                    $unitStatus = 0;
                } elseif (strtolower($status) === 'permanent' || $status === '1') {
                    $unitStatus = 1;
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

                BusinessUnitMaster::create([
                    'unit' => $unit,
                    'company_id' => $company->id,
                    'unit_status' => $unitStatus,
                ]);
                $importedCount++;
            }

            return redirect()->route('business-unit-masters.index')->with('success', "Excel imported successfully. {$importedCount} business units added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}