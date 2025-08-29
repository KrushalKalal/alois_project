<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ConsultantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $query = Consultant::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            }

            $consultants = $query->paginate($perPage);

            return Inertia::render('Consultants/Index', [
                'auth' => auth()->user(),
                'consultants' => $consultants,
                'filters' => ['search' => $search, 'per_page' => $perPage],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Consultants: ' . $e->getMessage());
            return back()->with('error', 'Failed to load consultants.');
        }
    }

    public function create()
    {
        try {
            return Inertia::render('Consultants/Form', [
                'auth' => auth()->user(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Consultant create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|unique:consultants,code',
                'name' => 'required',
                'phone1' => 'required|unique:consultants,phone1',
                'phone2' => 'nullable|unique:consultants,phone2',
                'email1' => 'required|email|unique:consultants,email1',
                'email2' => 'nullable|email|unique:consultants,email2',
                'status' => 'required|in:active,inactive',
                'aadhaar' => 'nullable|file|mimes:pdf,jpg,png',
                'pan' => 'nullable|file|mimes:pdf,jpg,png',
                'po_copy' => 'nullable|file|mimes:pdf,jpg,png',
                'extra_doc' => 'nullable|file|mimes:pdf,jpg,png',
            ]);

            $data = $request->only([
                'code',
                'name',
                'address',
                'state',
                'city',
                'country',
                'phone1',
                'phone2',
                'email1',
                'email2',
                'status',
            ]);

            $consultant = Consultant::create($data);

            foreach (['aadhaar', 'pan', 'po_copy', 'extra_doc'] as $file) {
                if ($request->hasFile($file)) {
                    $path = $request->file($file)->store("consultants/{$consultant->code}/images", 'public');
                    $consultant->update([$file => $path]);
                }
            }

            return redirect()->route('consultants.index')->with('success', 'Consultant created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed.');
        } catch (\Exception $e) {
            \Log::error('Failed to create Consultant: ' . $e->getMessage());
            return back()->with('error', 'Failed to create consultant: ' . $e->getMessage());
        }
    }

    public function show(Consultant $consultant)
    {
        try {
            return Inertia::render('Consultants/Show', [
                'auth' => auth()->user(),
                'consultant' => $consultant,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to show Consultant: ' . $e->getMessage());
            return back()->with('error', 'Failed to show consultant.');
        }
    }

    public function edit(Consultant $consultant)
    {
        try {
            return Inertia::render('Consultants/Form', [
                'auth' => auth()->user(),
                'consultant' => $consultant,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load Consultant edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Consultant $consultant)
    {
        try {
            \Log::info('Update request received', ['method' => $request->method(), 'input' => $request->all()]);

            $validated = $request->validate([
                'code' => 'required|unique:consultants,code,' . $consultant->id,
                'name' => 'required',
                'phone1' => 'required|unique:consultants,phone1,' . $consultant->id,
                'phone2' => 'nullable|unique:consultants,phone2,' . $consultant->id,
                'email1' => 'required|email|unique:consultants,email1,' . $consultant->id,
                'email2' => 'nullable|email|unique:consultants,email2,' . $consultant->id,
                'status' => 'required|in:active,inactive',
                'aadhaar' => 'sometimes|file|mimes:pdf,jpg,png',
                'pan' => 'sometimes|file|mimes:pdf,jpg,png',
                'po_copy' => 'sometimes|file|mimes:pdf,jpg,png',
                'extra_doc' => 'sometimes|file|mimes:pdf,jpg,png',
            ]);

            $consultant->update($request->only([
                'code',
                'name',
                'address',
                'state',
                'city',
                'country',
                'phone1',
                'phone2',
                'email1',
                'email2',
                'status',
            ]));

            foreach (['aadhaar', 'pan', 'po_copy', 'extra_doc'] as $file) {
                if ($request->hasFile($file)) {
                    if ($consultant->$file) {
                        Storage::disk('public')->delete($consultant->$file);
                    }
                    $path = $request->file($file)->store("consultants/{$consultant->code}/images", 'public');
                    $consultant->update([$file => $path]);
                }
            }

            return redirect()->route('consultants.index')->with('success', 'Consultant updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to update Consultant: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update consultant: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Consultant $consultant)
    {
        try {
            foreach (['aadhaar', 'pan', 'po_copy', 'extra_doc'] as $file) {
                if ($consultant->$file) {
                    Storage::disk('public')->delete($consultant->$file);
                }
            }
            $consultant->delete();

            return redirect()->route('consultants.index')->with('success', 'Consultant deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete Consultant: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete consultant: ' . $e->getMessage());
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $allColumns = Schema::getColumnListing('consultants');
            $columns = array_filter($allColumns, function ($col) {
                return !in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at', 'aadhaar', 'pan', 'po_copy', 'extra_doc']);
            });
            $columns = array_values($columns);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($columns as $index => $data) {
                $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue($columnLetter . '1', $data);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'consultant_template.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to download Consultant template: ' . $e->getMessage());
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
            $requiredFields = ['code', 'name', 'phone1', 'email1', 'status'];
            $fieldIndices = [];
            foreach ($requiredFields as $field) {
                $index = array_search($field, $headers);
                if ($index === false) {
                    throw new \Exception("Required column '$field' not found in the Excel file.");
                }
                $fieldIndices[$field] = $index;
            }

            $optionalFields = ['address', 'state', 'city', 'country', 'phone2', 'email2'];
            foreach ($optionalFields as $field) {
                $index = array_search($field, $headers);
                if ($index !== false) {
                    $fieldIndices[$field] = $index;
                }
            }

            $importedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0)
                    continue;

                $data = [];
                foreach ($fieldIndices as $field => $colIndex) {
                    $data[$field] = $row[$colIndex] ?? null;
                }

                if (empty($data['code']) || empty($data['name']) || empty($data['phone1']) || empty($data['email1']) || empty($data['status'])) {
                    continue;
                }

                Consultant::create($data);
                $importedCount++;
            }

            return redirect()->route('consultants.index')->with('success', "Excel imported successfully. {$importedCount} consultants added.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Invalid file format.');
        } catch (\Exception $e) {
            \Log::error('Failed to import Excel: ' . $e->getMessage());
            return back()->with('error', 'Failed to import Excel: ' . $e->getMessage());
        }
    }
}