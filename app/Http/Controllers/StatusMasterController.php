<?php

namespace App\Http\Controllers;

use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Schema;

class StatusMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $query = StatusMaster::query();

            if ($search) {
                $query->where('status', 'like', "%{$search}%");
            }

            $statuses = $query->paginate($perPage);

            return Inertia::render('StatusMasters/Index', [
                'auth' => auth()->user(),
                'statuses' => $statuses,
                'filters' => ['search' => $search, 'per_page' => $perPage],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Status Masters: ' . $e->getMessage());
            return back()->with('error', 'Failed to load statuses.');
        }
    }

    public function create()
    {
        try {
            return Inertia::render('StatusMasters/Form', [
                'auth' => auth()->user(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Status Master create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate(['status' => 'required|unique:status_masters,status']);

            StatusMaster::create($request->only('status'));

            return redirect()->route('status-masters.index')->with('success', 'Status created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Status Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to create status: ' . $e->getMessage());
        }
    }

    public function show(StatusMaster $statusMaster)
    {
        try {
            return Inertia::render('StatusMasters/Show', [
                'auth' => auth()->user(),
                'status' => $statusMaster,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Status Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to show status.');
        }
    }

    public function edit(StatusMaster $statusMaster)
    {
        try {
            return Inertia::render('StatusMasters/Form', [
                'auth' => auth()->user(),
                'status' => $statusMaster,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Status Master edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, StatusMaster $statusMaster)
    {
        try {
            \Log::info('Update request received', ['method' => $request->method(), 'input' => $request->all()]);

            $validated = $request->validate([
                'status' => 'required|unique:status_masters,status,' . $statusMaster->id,
            ]);

            $statusMaster->update($validated);

            return redirect()->route('status-masters.index')->with('success', 'Status updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update Status Master: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(StatusMaster $statusMaster)
    {
        try {
            $statusMaster->delete();

            return redirect()->route('status-masters.index')->with('success', 'Status deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Status Master: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete status: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $allColumns = Schema::getColumnListing('status_masters');
            $columns = array_filter($allColumns, function ($col) {
                return !in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at']);
            });
            $columns = array_values($columns);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($columns as $index => $columnName) {
                $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter . '1', $columnName);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'status_master_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Status Master template: ' . $e->getMessage());
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
            $statusIndex = array_search('status', $headers);

            if ($statusIndex === false) {
                throw new \Exception("Required column (status) not found in the Excel file.");
            }

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0)
                    continue;

                $status = $row[$statusIndex] ?? null;

                if (empty($status))
                    continue;

                StatusMaster::create(['status' => $status]);

                $importedCount++;
            }

            return redirect()->route('status-masters.index')->with('success', "Excel imported successfully. {$importedCount} status added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}