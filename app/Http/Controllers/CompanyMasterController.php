<?php

namespace App\Http\Controllers;

use App\Models\CompanyMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Redirect;

class CompanyMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $query = CompanyMaster::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            }

            $companies = $query->paginate($perPage);

            return Inertia::render('CompanyMasters/Index', [
                'auth' => auth()->user(),
                'companies' => $companies,
                'filters' => ['search' => $search, 'per_page' => $perPage],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Company Masters: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load companies.');
        }
    }

    public function create()
    {
        try {
            return Inertia::render('CompanyMasters/Form', [
                'auth' => auth()->user(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Company Master create form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        \Log::info('Store Company Master Request:', $request->all());

        try {
            $request->validate([
                'name' => 'required|unique:company_masters,name',
                'region' => 'nullable|string|in:APAC,India,Aegis,EU-UK',
                'to_emails' => 'nullable|array',
                'to_emails.*' => 'email',
                'cc_emails' => 'nullable|array',
                'cc_emails.*' => 'email',
            ]);

            $toEmails = $this->normalizeEmails($request->to_emails);
            $ccEmails = $this->normalizeEmails($request->cc_emails);

            $company = CompanyMaster::create([
                'name' => $request->name,
                'region' => $request->region,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
            ]);

            \Log::info('CompanyMaster created:', $company->toArray());

            return redirect()->route('company-masters.index')->with('success', 'Company created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->errors())->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Company Master: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    public function show(CompanyMaster $companyMaster)
    {
        try {
            return Inertia::render('CompanyMasters/Show', [
                'auth' => auth()->user(),
                'company' => [
                    'id' => $companyMaster->id,
                    'name' => $companyMaster->name,
                    'region' => $companyMaster->region,
                    'to_emails' => $companyMaster->to_emails ?? [],
                    'cc_emails' => $companyMaster->cc_emails ?? [],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Company Master: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to show company.');
        }
    }

    public function edit(CompanyMaster $companyMaster)
    {
        try {
            return Inertia::render('CompanyMasters/Form', [
                'auth' => auth()->user(),
                'company' => [
                    'id' => $companyMaster->id,
                    'name' => $companyMaster->name,
                    'region' => $companyMaster->region,
                    'to_emails' => $companyMaster->to_emails ?? [],
                    'cc_emails' => $companyMaster->cc_emails ?? [],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Company Master edit form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, CompanyMaster $companyMaster)
    {
        \Log::info('Update Company Master Request:', $request->all());

        try {
            $request->validate([
                'name' => 'required|unique:company_masters,name,' . $companyMaster->id,
                'region' => 'nullable|string|in:APAC,India,Aegis,EU-UK',
                'to_emails' => 'nullable|array',
                'to_emails.*' => 'email',
                'cc_emails' => 'nullable|array',
                'cc_emails.*' => 'email',
            ]);

            $toEmails = $this->normalizeEmails($request->to_emails);
            $ccEmails = $this->normalizeEmails($request->cc_emails);

            $companyMaster->update([
                'name' => $request->name,
                'region' => $request->region,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
            ]);

            \Log::info('CompanyMaster updated:', $companyMaster->toArray());

            return redirect()->route('company-masters.index')->with('success', 'Company updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->errors())->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to update Company Master: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }

    public function destroy(CompanyMaster $companyMaster)
    {
        try {
            $companyMaster->delete();

            return redirect()->route('company-masters.index')->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Company Master: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $columns = ['name', 'region', 'to_emails', 'cc_emails'];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($columns as $index => $columnName) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter . '1', $columnName);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'company_master_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Company Master template: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }

    public function importFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $name = $row[0];
                $region = $row[1];
                $to_emails = $this->normalizeEmails($row[2]);
                $cc_emails = $this->normalizeEmails($row[3]);

                if (!$name) {
                    continue;
                }

                CompanyMaster::create([
                    'name' => $name,
                    'region' => in_array($region, ['APAC', 'India', 'Aegis', 'EU-UK']) ? $region : null,
                    'to_emails' => $to_emails,
                    'cc_emails' => $cc_emails,
                ]);
            }

            return redirect()->route('company-masters.index')->with('success', 'Excel imported successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->errors())->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }

    /**
     * Normalize email input to an array of valid emails.
     *
     * @param string|array|null $emails
     * @return array
     */
    private function normalizeEmails($emails)
    {
        if (empty($emails)) {
            return [];
        }

        $emailArray = is_array($emails) ? $emails : array_filter(array_map('trim', explode(',', $emails)));

        $validEmails = array_filter($emailArray, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return array_values($validEmails);
    }
}