<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobSeeker;
use App\Models\CompanyMaster;
use App\Models\StatusMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Services\EmailService;

class AdminDashboardController extends Controller
{
    protected function getDashboardData(Request $request)
    {
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $selectedYear = (int) $request->query('year', $currentYear);

        $availableYears = range($currentYear - 5, $currentYear + 1);

        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $currentYear;
        }

        $currentMonth = $currentDate->month;
        $months = [];
        if ($selectedYear == $currentYear) {
            for ($m = 1; $m <= $currentMonth; $m++) {
                $months[] = sprintf('%02d-%d', $m, $selectedYear);
            }
        } elseif ($selectedYear > $currentYear && $currentDate->greaterThanOrEqualTo(Carbon::create($selectedYear, 1, 1))) {
            $months[] = sprintf('01-%d', $selectedYear);
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $months[] = sprintf('%02d-%d', $m, $selectedYear);
            }
        }

        // Fetch company-to-region mapping dynamically
        $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();

        $dashboardData = [];
        foreach ($companyToCountry as $companyId => $region) {
            $currency = $region === 'APAC' ? 'AUD' : ($region === 'EU-UK' ? 'GBP' : 'INR');
            $dashboardData[$companyId] = [
                'region' => $region,
                'currency' => $currency,
                'permData' => $this->getPermanentData($companyId, $selectedYear, $months),
                'backoutData' => $this->getBackoutData($companyId, $selectedYear),
                'terminationData' => $this->getTerminationData($companyId, $selectedYear, $months),
                'poExpiryData' => $this->getPoExpiryData($companyId, $selectedYear),
                'contractData' => $this->getContractData($companyId, $selectedYear),
            ];
        }

        return [
            'auth' => auth()->user(),
            'dashboardData' => $dashboardData,
            'companyToCountry' => $companyToCountry,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'currentYear' => $currentYear,
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
        }

        return Inertia::render('Admin/Dashboard', $this->getDashboardData($request));
    }

    protected function generateExcel($companyId, $selectedYear)
    {
        // Fetch company-to-region mapping dynamically
        $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();

        if (!array_key_exists($companyId, $companyToCountry)) {
            throw new \Exception('Invalid company ID');
        }

        $region = $companyToCountry[$companyId];
        $currency = $region === 'APAC' ? 'AUD' : ($region === 'EU-UK' ? 'GBP' : 'INR');
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        $availableYears = range($currentYear - 5, $currentYear + 1);

        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $currentYear;
        }

        $months = [];
        if ($selectedYear == $currentYear) {
            for ($m = 1; $m <= $currentMonth; $m++) {
                $months[] = sprintf('%02d-%d', $m, $selectedYear);
            }
        } elseif ($selectedYear > $currentYear && $currentDate->greaterThanOrEqualTo(Carbon::create($selectedYear, 1, 1))) {
            $months[] = sprintf('01-%d', $selectedYear);
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $months[] = sprintf('%02d-%d', $m, $selectedYear);
            }
        }

        $data = [
            'region' => $region,
            'currency' => $currency,
            'permData' => $this->getPermanentData($companyId, $selectedYear, $months),
            'backoutData' => $this->getBackoutData($companyId, $selectedYear),
            'terminationData' => $this->getTerminationData($companyId, $selectedYear, $months),
            'poExpiryData' => $this->getPoExpiryData($companyId, $selectedYear),
            'contractData' => $this->getContractData($companyId, $selectedYear),
        ];

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Hire Report
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("{$region} Hire Report {$selectedYear}");
        $sheet->setShowGridlines(false);

        $row1 = 1;
        $row5 = 1;

        // Table 1: Perm Data (Columns A-H)
        $sheet->setCellValue("A$row1", "Perm Data $selectedYear ($currency)");
        $sheet->mergeCells("A$row1:H$row1");
        $sheet->getStyle("A$row1")->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("A$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row1")->getFill()->setFillType(Fill::FILL_NONE);
        $row1++;
        $sheet->setCellValue("A$row1", '');
        $row1++;
        // Table 1 headers
        $sheet->setCellValue("A$row1", 'Months');
        $sheet->setCellValue("B$row1", 'Selected');
        $sheet->setCellValue("C$row1", 'Backout');
        $sheet->setCellValue("D$row1", 'Terminated');
        $sheet->setCellValue("E$row1", 'Offered');
        $sheet->setCellValue("F$row1", 'Joined');
        $sheet->setCellValue("G$row1", 'FTE Conv Fees');
        $sheet->setCellValue("H$row1", 'Total');
        $sheet->getStyle("A$row1:H$row1")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("A$row1:H$row1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
        $sheet->getStyle("A$row1:H$row1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'H') as $col) {
            $sheet->getStyle("{$col}$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row1++;
        $startRow1 = $row1;
        foreach ($data['permData'] as $rowData) {
            $sheet->setCellValue("A$row1", $rowData['month']);
            $sheet->setCellValue("B$row1", $rowData['Selected'] !== null ? number_format($rowData['Selected'], 2) : '');
            $sheet->setCellValue("C$row1", $rowData['Backout'] !== null ? number_format($rowData['Backout'], 2) : '');
            $sheet->setCellValue("D$row1", $rowData['Terminated'] !== null ? number_format($rowData['Terminated'], 2) : '');
            $sheet->setCellValue("E$row1", $rowData['Offered'] !== null ? number_format($rowData['Offered'], 2) : '');
            $sheet->setCellValue("F$row1", $rowData['Joined'] !== null ? number_format($rowData['Joined'], 2) : '');
            $sheet->setCellValue("G$row1", $rowData['FTEConversionFees'] !== null ? number_format($rowData['FTEConversionFees'], 2) : '');
            $sheet->setCellValue("H$row1", isset($rowData['Total']) && $rowData['Total'] !== null ? number_format($rowData['Total'], 2) : '');
            $sheet->getStyle("A$row1:H$row1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (isset($rowData['month']) && $rowData['month'] === 'Total') {
                \Log::info('Applying gray style to Perm Data Total row', ['row' => $row1]);
                $sheet->getStyle("A$row1:H$row1")->getFont()->setBold(true);
                $sheet->getStyle("A$row1:H$row1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
            }
            foreach (range('B', 'H') as $col) {
                $sheet->getStyle("{$col}$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row1++;
        }
        // Total row for Perm Data

        $row1 += 2;

        // Table 5: Contracting JOS (Columns K-P)
        $colKRow1 = 11;
        $sheet->setCellValueByColumnAndRow($colKRow1, $row5, ($region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly')) . " Contracting (Joined/Offer/Selected) $selectedYear ($currency)");
        $sheet->mergeCellsByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5);
        $sheet->getStyleByColumnAndRow($colKRow1, $row5)->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyleByColumnAndRow($colKRow1, $row5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyleByColumnAndRow($colKRow1, $row5)->getFill()->setFillType(Fill::FILL_NONE);
        $row5++;
        $sheet->setCellValueByColumnAndRow($colKRow1, $row5, '');
        $row5++;
        // Table 5 headers
        $sheet->setCellValueByColumnAndRow($colKRow1, $row5, 'Status');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 1, $row5, 'Client');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 2, $row5, 'HC');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 3, $row5, 'BR');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 4, $row5, 'PR');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 5, $row5, 'Final_GP');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 6, $row5, 'GP %');
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range($colKRow1 + 2, $colKRow1 + 6) as $col) {
            $sheet->getStyleByColumnAndRow($col, $row5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row5++;
        $startRow5 = $row5;
        foreach ($data['contractData'] as $rowData) {
            $status = $rowData['status'];
            $richText = new RichText();
            $isBoldStatus = in_array($status, ['Joined', 'Offered', 'Selected']);
            $richText->createTextRun($status)->getFont()->setBold($isBoldStatus);
            $sheet->setCellValueByColumnAndRow($colKRow1, $row5, $richText);
            $sheet->setCellValueByColumnAndRow($colKRow1 + 1, $row5, $rowData['client']);
            $sheet->setCellValueByColumnAndRow($colKRow1 + 2, $row5, $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValueByColumnAndRow($colKRow1 + 3, $row5, $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValueByColumnAndRow($colKRow1 + 4, $row5, $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValueByColumnAndRow($colKRow1 + 5, $row5, $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValueByColumnAndRow($colKRow1 + 6, $row5, $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (isset($rowData['status']) && $rowData['status'] === 'Grand Total') {
                \Log::info('Applying gray style to Contract Data Grand Total row', ['row' => $row5]);
                $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
            }
            foreach (range($colKRow1 + 2, $colKRow1 + 6) as $col) {
                $sheet->getStyleByColumnAndRow($col, $row5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row5++;
        }
        // Total row for Contracting JOS

        $row5 += 2;

        // Table 2: Backout Data (Columns A-F)
        $row2 = max($row1, $row5);
        $sheet->setCellValue("A$row2", ($region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly')) . " Contracting Backouts $selectedYear ($currency)");
        $sheet->mergeCells("A$row2:F$row2");
        $sheet->getStyle("A$row2")->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("A$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row2")->getFill()->setFillType(Fill::FILL_NONE);
        $row2++;
        $sheet->setCellValue("A$row2", '');
        $row2++;
        // Table 2 headers
        $sheet->setCellValue("A$row2", 'Months');
        $sheet->setCellValue("B$row2", 'HC');
        $sheet->setCellValue("C$row2", 'BR');
        $sheet->setCellValue("D$row2", 'PR');
        $sheet->setCellValue("E$row2", 'Final_GP');
        $sheet->setCellValue("F$row2", 'GP %');
        $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
        $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'F') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row2++;
        $startRow2 = $row2;
        foreach ($data['backoutData'] as $rowData) {
            $sheet->setCellValue("A$row2", $rowData['month']);
            $sheet->setCellValue("B$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("C$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("D$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("E$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (isset($rowData['month']) && $rowData['month'] === 'Total') {
                \Log::info('Applying gray style to Backout Data Total row', ['row' => $row2]);
                $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true);
                $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
            }
            foreach (range('B', 'F') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for Backout Data

        $row2 += 2;

        // Table 3: Termination Data (Columns A-F)
        $sheet->setCellValue("A$row2", ($region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly')) . " Contracting Termination $selectedYear ($currency)");
        $sheet->mergeCells("A$row2:F$row2");
        $sheet->getStyle("A$row2")->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("A$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row2")->getFill()->setFillType(Fill::FILL_NONE);
        $row2++;
        $sheet->setCellValue("A$row2", '');
        $row2++;
        // Table 3 headers
        $sheet->setCellValue("A$row2", 'Months');
        $sheet->setCellValue("B$row2", 'HC');
        $sheet->setCellValue("C$row2", 'BR');
        $sheet->setCellValue("D$row2", 'PR');
        $sheet->setCellValue("E$row2", 'Final_GP');
        $sheet->setCellValue("F$row2", 'GP %');
        $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
        $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'F') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row2++;
        $startRow3 = $row2;
        foreach ($data['terminationData'] as $rowData) {
            $sheet->setCellValue("A$row2", $rowData['month']);
            $sheet->setCellValue("B$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("C$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("D$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("E$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (isset($rowData['month']) && $rowData['month'] === 'Total') {
                \Log::info('Applying gray style to Termination Data Total row', ['row' => $row2]);
                $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true);
                $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
            }
            foreach (range('B', 'F') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for Termination Data

        $row2 += 2;

        // Table 4: PO Expiry (Columns A-H)
        $sheet->setCellValue("A$row2", "PO Not Available/Expiry Summary $selectedYear ($currency)");
        $sheet->mergeCells("A$row2:H$row2");
        $sheet->getStyle("A$row2")->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("A$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row2")->getFill()->setFillType(Fill::FILL_NONE);
        $row2++;
        $sheet->setCellValue("A$row2", '');
        $row2++;
        // Table 4 headers
        $sheet->setCellValue("A$row2", 'Po End Year');
        $sheet->setCellValue("B$row2", 'Client');
        $sheet->setCellValue("C$row2", 'Po End Month');
        $sheet->setCellValue("D$row2", 'HC');
        $sheet->setCellValue("E$row2", 'BR');
        $sheet->setCellValue("F$row2", 'PR');
        $sheet->setCellValue("G$row2", 'Final_GP');
        $sheet->setCellValue("H$row2", 'GP %');
        $sheet->getStyle("A$row2:H$row2")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("A$row2:H$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
        $sheet->getStyle("A$row2:H$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('D', 'H') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row2++;
        $startRow4 = $row2;
        foreach ($data['poExpiryData'] as $rowData) {
            $sheet->setCellValue("A$row2", $rowData['poEndYear']);
            $sheet->setCellValue("B$row2", $rowData['client']);
            $sheet->setCellValue("C$row2", $rowData['poEndMonth'] ?? '');
            $sheet->setCellValue("D$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("E$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("G$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("H$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:H$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (isset($rowData['poEndYear']) && $rowData['poEndYear'] === 'Total') {
                \Log::info('Applying gray style to PO Expiry Data Total row', ['row' => $row2]);
                $sheet->getStyle("A$row2:H$row2")->getFont()->setBold(true);
                $sheet->getStyle("A$row2:H$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
            }
            foreach (range('D', 'H') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for PO Expiry

        // Sheet 2: Perm Data
        $permSheet = $spreadsheet->createSheet();
        $permSheet->setTitle('Perm Data');
        $permSheet->setShowGridlines(false);
        $permRow = 1;

        $permSheet->setCellValue("A$permRow", "Permanent Job Seekers $selectedYear");
        $permSheet->mergeCells("A$permRow:AZ$permRow");
        $permSheet->getStyle("A$permRow")->getFont()->setBold(true)->setSize(14);
        $permSheet->getStyle("A$permRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $permRow++;
        $permSheet->setCellValue("A$permRow", '');
        $permRow++;

        $fieldConfig = $this->getFieldConfig();
        $permHeaders = $fieldConfig[$region]['permanent'] ?? [];
        $column = 'A';
        foreach ($permHeaders as $header) {
            $permSheet->setCellValue("{$column}$permRow", $header['label']);
            $permSheet->getStyle("{$column}$permRow")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
            $permSheet->getStyle("{$column}$permRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
            $permSheet->getStyle("{$column}$permRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            // Set auto-width for the column
            $permSheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $permRow++;

        $permJobSeekers = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'permanent')
            ->whereYear('created_at', $selectedYear)
            ->with([
                'company' => fn($query) => $query->select('id', 'region'),
                'location' => fn($query) => $query->select('id', 'name'),
                'businessUnit' => fn($query) => $query->select('id', 'unit'),
                'assistantManager' => fn($query) => $query->select('id', 'name'),
                'deputyManager' => fn($query) => $query->select('id', 'name'),
                'teamLeader' => fn($query) => $query->select('id', 'name'),
                'recruiter' => fn($query) => $query->select('id', 'name'),
                'client' => fn($query) => $query->select('id', 'client_name'),
                'status' => fn($query) => $query->select('id', 'status')
            ])
            ->get();

        foreach ($permJobSeekers as $jobSeeker) {
            $column = 'A';
            foreach ($permHeaders as $header) {
                $fieldName = $header['name'];
                $value = '';

                switch ($fieldName) {
                    case 'company_id':
                        $value = $jobSeeker->company->region ?? '';
                        break;
                    case 'location_id':
                        $value = $jobSeeker->location->name ?? '';
                        break;
                    case 'business_unit_id':
                        $value = $jobSeeker->businessUnit->unit ?? '';
                        break;
                    case 'am_id':
                        $value = $jobSeeker->assistantManager->name ?? '';
                        break;
                    case 'dm_id':
                        $value = $jobSeeker->deputyManager->name ?? '';
                        break;
                    case 'tl_id':
                        $value = $jobSeeker->teamLeader->name ?? '';
                        break;
                    case 'recruiter_id':
                        $value = $jobSeeker->recruiter->name ?? '';
                        break;
                    case 'client_id':
                        $value = $jobSeeker->client->client_name ?? '';
                        break;
                    case 'status_id':
                        $value = $jobSeeker->status->status ?? '';
                        break;
                    case 'selection_date':
                    case 'offer_date':
                    case 'join_date':
                    case 'qly_date':
                    case 'backout_term_date':
                    case 'po_end_date':
                        $value = $jobSeeker->$fieldName ? Carbon::parse($jobSeeker->$fieldName)->format('Y-m-d') : '';
                        break;
                    case 'join_month':
                    case 'select_month':
                    case 'backout_term_month':
                    case 'po_end_month':
                        if ($jobSeeker->$fieldName) {
                            try {
                                // Parse MM-YYYY format
                                $date = Carbon::createFromFormat('m-Y', $jobSeeker->$fieldName);
                                if ($date && $date->isValid()) {
                                    $value = $date->format('F Y'); // e.g., May 2025
                                } else {
                                    \Log::warning("Invalid date format for $fieldName", [
                                        'job_seeker_id' => $jobSeeker->id,
                                        'value' => $jobSeeker->$fieldName,
                                    ]);
                                    $value = '';
                                }
                            } catch (\Exception $e) {
                                \Log::warning("Error parsing $fieldName", [
                                    'job_seeker_id' => $jobSeeker->id,
                                    'value' => $jobSeeker->$fieldName,
                                    'error' => $e->getMessage(),
                                ]);
                                $value = '';
                            }
                        }
                        break;
                    default:
                        $value = $jobSeeker->$fieldName ?? '';
                        break;
                }

                $permSheet->setCellValue("{$column}$permRow", $value);
                $permSheet->getStyle("{$column}$permRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $column++;
            }
            $permRow++;
        }

        // Sheet 3: Contractual Data
        $contractSheet = $spreadsheet->createSheet();
        $contractSheet->setTitle('Contractual Data');
        $contractSheet->setShowGridlines(false);
        $contractRow = 1;

        $contractSheet->setCellValue("A$contractRow", "Temporary Job Seekers $selectedYear");
        $contractSheet->mergeCells("A$contractRow:AZ$contractRow");
        $contractSheet->getStyle("A$contractRow")->getFont()->setBold(true)->setSize(14);
        $contractSheet->getStyle("A$contractRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $contractRow++;
        $contractSheet->setCellValue("A$contractRow", '');
        $contractRow++;

        $tempHeaders = $fieldConfig[$region]['temporary'] ?? [];
        $column = 'A';
        foreach ($tempHeaders as $header) {
            $contractSheet->setCellValue("{$column}$contractRow", $header['label']);
            $contractSheet->getStyle("{$column}$contractRow")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
            $contractSheet->getStyle("{$column}$contractRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F26522');
            $contractSheet->getStyle("{$column}$contractRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            // Set auto-width for the column
            $contractSheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $contractRow++;

        $tempJobSeekers = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'temporary')
            ->whereYear('created_at', $selectedYear)
            ->with([
                'company' => fn($query) => $query->select('id', 'region'),
                'location' => fn($query) => $query->select('id', 'name'),
                'businessUnit' => fn($query) => $query->select('id', 'unit'),
                'assistantManager' => fn($query) => $query->select('id', 'name'),
                'deputyManager' => fn($query) => $query->select('id', 'name'),
                'teamLeader' => fn($query) => $query->select('id', 'name'),
                'recruiter' => fn($query) => $query->select('id', 'name'),
                'client' => fn($query) => $query->select('id', 'client_name'),
                'status' => fn($query) => $query->select('id', 'status')
            ])
            ->get();

        foreach ($tempJobSeekers as $jobSeeker) {
            $column = 'A';
            foreach ($tempHeaders as $header) {
                $fieldName = $header['name'];
                $value = '';

                switch ($fieldName) {
                    case 'company_id':
                        $value = $jobSeeker->company->region ?? '';
                        break;
                    case 'location_id':
                        $value = $jobSeeker->location->name ?? '';
                        break;
                    case 'business_unit_id':
                        $value = $jobSeeker->businessUnit->unit ?? '';
                        break;
                    case 'am_id':
                        $value = $jobSeeker->assistantManager->name ?? '';
                        break;
                    case 'dm_id':
                        $value = $jobSeeker->deputyManager->name ?? '';
                        break;
                    case 'tl_id':
                        $value = $jobSeeker->teamLeader->name ?? '';
                        break;
                    case 'recruiter_id':
                        $value = $jobSeeker->recruiter->name ?? '';
                        break;
                    case 'client_id':
                        $value = $jobSeeker->client->client_name ?? '';
                        break;
                    case 'status_id':
                        $value = $jobSeeker->status->status ?? '';
                        break;
                    case 'selection_date':
                    case 'offer_date':
                    case 'join_date':
                    case 'qly_date':
                    case 'backout_term_date':
                    case 'po_end_date':
                        $value = $jobSeeker->$fieldName ? Carbon::parse($jobSeeker->$fieldName)->format('Y-m-d') : '';
                        break;
                    case 'join_month':
                    case 'select_month':
                    case 'backout_term_month':
                    case 'po_end_month':
                        if ($jobSeeker->$fieldName) {
                            try {
                                // Parse MM-YYYY format
                                $date = Carbon::createFromFormat('m-Y', $jobSeeker->$fieldName);
                                if ($date && $date->isValid()) {
                                    $value = $date->format('F Y'); // e.g., May 2025
                                } else {
                                    \Log::warning("Invalid date format for $fieldName", [
                                        'job_seeker_id' => $jobSeeker->id,
                                        'value' => $jobSeeker->$fieldName,
                                    ]);
                                    $value = '';
                                }
                            } catch (\Exception $e) {
                                \Log::warning("Error parsing $fieldName", [
                                    'job_seeker_id' => $jobSeeker->id,
                                    'value' => $jobSeeker->$fieldName,
                                    'error' => $e->getMessage(),
                                ]);
                                $value = '';
                            }
                        }
                        break;
                    default:
                        $value = $jobSeeker->$fieldName ?? '';
                        break;
                }

                $contractSheet->setCellValue("{$column}$contractRow", $value);
                $contractSheet->getStyle("{$column}$contractRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $column++;
            }
            $contractRow++;
        }
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (range('A', 'C') as $col) {
            $permSheet->getColumnDimension($col)->setAutoSize(true);
            $contractSheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $companyId = (int) $request->query('company_id');
        $selectedYear = (int) $request->query('year', Carbon::now()->year);

        // Fetch company-to-region mapping dynamically
        $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();
        $region = $companyToCountry[$companyId] ?? 'Unknown';
        $region = preg_replace('/[^A-Za-z0-9\-]/', '_', $region);

        $spreadsheet = $this->generateExcel($companyId, $selectedYear);

        $writer = new Xlsx($spreadsheet);
        $filename = "{$region}_Hire_Report_{$selectedYear}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function sendHelloEmail(Request $request)
    {
        Log::info('sendHelloEmail called', [
            'request_data' => $request->all(),
            'company_id' => $request->company_id,
            'year' => $request->year,
            'user_id' => Auth::id(),
            'mail_config' => [
                'mailer' => config('mail.mailer'),
                'host' => config('mail.host'),
                'port' => config('mail.port'),
                'username' => config('mail.username'),
                'from_address' => config('mail.from.address'),
                'encryption' => config('mail.encryption'),
            ],
        ]);

        $user = Auth::user();
        if ($user->role !== 'admin') {
            Log::warning('Unauthorized access to sendHelloEmail', ['user_id' => $user->id]);
            return redirect()->back()->with('flash', ['error' => 'Unauthorized']);

        }

        try {
            $request->validate([
                'company_id' => 'required|exists:company_masters,id',
                'year' => 'nullable|integer|min:1900|max:2100',
            ], [
                'company_id.required' => 'Company ID is required.',
                'company_id.exists' => 'Invalid company ID.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in sendHelloEmail', [
                'errors' => $e->errors(),
                'company_id' => $request->company_id,
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->with('error', ['error' => $e->errors()['company_id'][0] ?? 'Invalid company ID.']);

        }

        try {
            // Fetch company-to-region mapping dynamically
            $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();

            // Fetch company for emails
            $company = CompanyMaster::findOrFail($request->company_id);
            $toEmails = is_array($company->to_emails) ? $company->to_emails : json_decode($company->to_emails ?? '[]', true);
            $ccEmails = is_array($company->cc_emails) ? $company->cc_emails : json_decode($company->cc_emails ?? '[]', true);

            Log::info('Company emails fetched', [
                'company_id' => $company->id,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
            ]);

            if (empty($toEmails)) {
                Log::warning('No To email addresses found for company', ['company_id' => $company->id]);
                return redirect()->back()->with('error', ['error' => 'No To email addresses found for this company.']);

            }

            // Validate email addresses
            $validToEmails = array_filter($toEmails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            $validCcEmails = array_filter($ccEmails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            if (empty($validToEmails)) {
                Log::warning('No valid To email addresses found', ['company_id' => $company->id]);
                return redirect()->back()->with('error', ['error' => 'No valid To email addresses found for this company.']);

            }

            // Calculate months (same logic as in generateExcel)
            $currentDate = Carbon::now();
            $currentYear = $currentDate->year;
            $currentMonth = $currentDate->month;
            $selectedYear = $request->year ?? $currentYear;
            $availableYears = range($currentYear - 5, $currentYear + 1);

            if (!in_array($selectedYear, $availableYears)) {
                $selectedYear = $currentYear;
            }

            $months = [];
            if ($selectedYear == $currentYear) {
                for ($m = 1; $m <= $currentMonth; $m++) {
                    $months[] = sprintf('%02d-%d', $m, $selectedYear);
                }
            } elseif ($selectedYear > $currentYear && $currentDate->greaterThanOrEqualTo(Carbon::create($selectedYear, 1, 1))) {
                $months[] = sprintf('01-%d', $selectedYear);
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $months[] = sprintf('%02d-%d', $m, $selectedYear);
                }
            }

            // Fetch data for email body
            $data = [
                'region' => $companyToCountry[$company->id] ?? 'Unknown',
                'currency' => $companyToCountry[$company->id] === 'APAC' ? 'AUD' : ($companyToCountry[$company->id] === 'EU-UK' ? 'GBP' : 'INR'),
                'permData' => $this->getPermanentData($company->id, $selectedYear, $months),
                'backoutData' => $this->getBackoutData($company->id, $selectedYear),
                'terminationData' => $this->getTerminationData($company->id, $selectedYear, $months),
                'poExpiryData' => $this->getPoExpiryData($company->id, $selectedYear),
                'contractData' => $this->getContractData($company->id, $selectedYear),
            ];

            // Fetch job seeker remarks
            $tempRemarks = JobSeeker::where('company_id', $company->id)
                ->where('job_seeker_type', 'temporary')
                ->whereDate('created_at', Carbon::today())
                ->pluck('remark2')
                ->map(fn($remark) => $remark ?? 'N/A')
                ->toArray();

            $permRemarks = JobSeeker::where('company_id', $company->id)
                ->where('job_seeker_type', 'permanent')
                ->whereDate('created_at', Carbon::today())
                ->pluck('remark2')
                ->map(fn($remark) => $remark ?? 'N/A')
                ->toArray();

            // Generate and store Excel file
            $currentDate = date('Y-m-d');
            $companyName = $companyToCountry[$company->id] ?? 'Unknown';
            $companyName = preg_replace('/[^A-Za-z0-9\-]/', '_', $companyName);
            $folderPath = "public/{$selectedYear}/{$currentDate}/{$companyName}";
            $fileName = "{$companyName}_Hire_Report_{$selectedYear}.xlsx";
            $filePath = "{$folderPath}/{$fileName}";
            $absoluteFolderPath = storage_path("app/{$folderPath}");
            $absoluteFilePath = storage_path("app/{$filePath}");

            Log::info('Attempting to create directory', [
                'folderPath' => $folderPath,
                'absoluteFolderPath' => $absoluteFolderPath,
            ]);

            // Manually create directory structure
            if (!is_dir($absoluteFolderPath)) {
                $created = mkdir($absoluteFolderPath, 0755, true);
                if (!$created) {
                    Log::error('Failed to create directory manually', [
                        'folderPath' => $folderPath,
                        'absoluteFolderPath' => $absoluteFolderPath,
                    ]);
                    throw new \Exception('Failed to create directory: ' . $absoluteFolderPath);
                }
                Log::info('Directory created manually', ['absoluteFolderPath' => $absoluteFolderPath]);
            } else {
                Log::info('Directory already exists', ['absoluteFolderPath' => $absoluteFolderPath]);
            }

            // Ensure directory is writable
            if (!is_writable($absoluteFolderPath)) {
                $chmod = chmod($absoluteFolderPath, 0755);
                if (!$chmod) {
                    Log::error('Failed to set directory permissions', [
                        'folderPath' => $folderPath,
                        'absoluteFolderPath' => $absoluteFolderPath,
                    ]);
                    throw new \Exception('Directory is not writable: ' . $absoluteFolderPath);
                }
                Log::info('Directory permissions set to 0755', ['absoluteFolderPath' => $absoluteFolderPath]);
            }

            // Generate Excel file
            $spreadsheet = $this->generateExcel($company->id, $selectedYear);
            $writer = new Xlsx($spreadsheet);
            Log::info('Attempting to save Excel file', ['filePath' => $absoluteFilePath]);
            $writer->save($absoluteFilePath);

            // Verify file exists
            if (!file_exists($absoluteFilePath)) {
                Log::error('Excel file was not created', ['filePath' => $absoluteFilePath]);
                throw new \Exception('Failed to create Excel file: ' . $absoluteFilePath);
            }

            Log::info('Excel file saved successfully', ['filePath' => $absoluteFilePath]);
            $region = $companyToCountry[$company->id] ?? 'Unknown';
            $dynamicSubject = "{$region} - Hire Report";
            // Send email
            Mail::send('emails.company_data', [
                'data' => $data,
                'tempRemarks' => $tempRemarks,
                'permRemarks' => $permRemarks,
                'currentDate' => Carbon::today()->format('j M Y'),
                'region' => $region,
                'year' => $selectedYear,
            ], function ($message) use ($validToEmails, $validCcEmails, $filePath, $dynamicSubject) {
                $message->to($validToEmails)
                    ->subject($dynamicSubject);
                if (!empty($validCcEmails)) {
                    $message->cc($validCcEmails);
                }
                $message->attach(storage_path("app/{$filePath}"), [
                    'as' => basename($filePath),
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            });

            Log::info('Email sent successfully for company', [
                'company_id' => $company->id,
                'to_emails' => $validToEmails,
                'cc_emails' => $validCcEmails,
            ]);
            return redirect()->back()->with('success', 'Email sent successfully.');

        } catch (\Exception $e) {
            Log::error('Exception in sendHelloEmail', [
                'error' => $e->getMessage(),
                'company_id' => $request->company_id,
                'trace' => $e->getTraceAsString(),
                'mail_config' => [
                    'mailer' => config('mail.mailer'),
                    'host' => config('mail.host'),
                    'port' => config('mail.port'),
                    'username' => config('mail.username'),
                    'from_address' => config('mail.from.address'),
                    'encryption' => config('mail.encryption'),
                ],
            ]);
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
    private function getPermanentData($companyId, $selectedYear, $months)
    {
        Log::info('Fetching Permanent Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $selectedStatusId = StatusMaster::where('status', 'Selected')->value('id');
        $backoutStatusId = StatusMaster::where('status', 'Backout')->value('id');
        $terminatedStatusId = StatusMaster::where('status', 'Termination')->value('id');
        $offeredStatusId = StatusMaster::where('status', 'Offered')->value('id');
        $joinedStatusId = StatusMaster::where('status', 'Joined')->value('id');
        $fteStatusId = StatusMaster::where('status', 'FTE Conversion Fee')->value('id');

        $permRows = [];
        foreach ($months as $month) {
            $row = [
                'month' => $month,
                'Selected' => 0,
                'Backout' => 0,
                'Terminated' => 0,
                'Offered' => 0,
                'Joined' => 0,
                'FTEConversionFees' => 0,
                'Total' => 0,
            ];
            if ($month === (string) $selectedYear) {
                $row = [
                    'month' => $month,
                    'Selected' => null,
                    'Backout' => null,
                    'Terminated' => null,
                    'Offered' => null,
                    'Joined' => null,
                    'FTEConversionFees' => null,
                    'Total' => null,
                ];
            } else {
                // Selected
                $row['Selected'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $selectedStatusId)
                    ->whereNotNull('selection_date')
                    ->whereRaw("DATE_FORMAT(selection_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                // Backout
                $row['Backout'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $backoutStatusId)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                // Terminated
                $row['Terminated'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $terminatedStatusId)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                // Offered
                $row['Offered'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $offeredStatusId)
                    ->whereNotNull('offer_date')
                    ->whereRaw("DATE_FORMAT(offer_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                // Joined
                $row['Joined'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $joinedStatusId)
                    ->whereNotNull('join_date')
                    ->whereRaw("DATE_FORMAT(join_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                // FTE Conversion Fees
                $row['FTEConversionFees'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $fteStatusId)
                    ->whereNotNull('join_date')
                    ->whereRaw("DATE_FORMAT(join_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Total'] = array_sum(array_slice($row, 1, 6));
            }
            $permRows[] = $row;
        }

        $permTotal = [
            'month' => 'Total',
            'Selected' => array_sum(array_column($permRows, 'Selected') ?? [0]),
            'Backout' => array_sum(array_column($permRows, 'Backout') ?? [0]),
            'Terminated' => array_sum(array_column($permRows, 'Terminated') ?? [0]),
            'Offered' => array_sum(array_column($permRows, 'Offered') ?? [0]),
            'Joined' => array_sum(array_column($permRows, 'Joined') ?? [0]),
            'FTEConversionFees' => array_sum(array_column($permRows, 'FTEConversionFees') ?? [0]),
            'Total' => array_sum(array_column($permRows, 'Total') ?? [0]),
        ];
        $permRows[] = $permTotal;

        Log::info('Permanent Data Rows', ['rows' => $permRows]);

        return $permRows;
    }

    private function getBackoutData($companyId, $selectedYear)
    {
        Log::info('Fetching Backout Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $backoutStatusId = DB::table('status_masters')->where('status', 'Backout')->value('id');

        if (!$backoutStatusId) {
            Log::warning('No Backout status found in status_master');
            return [
                ['month' => (string) $selectedYear, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null],
                ['month' => 'Candidate BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
                ['month' => 'Client BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
                ['month' => 'Total', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
            ];
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = sprintf('%02d-%d', $m, $selectedYear);
        }
        $months[] = (string) $selectedYear; // For yearly summary row

        $backoutRows = [
            ['month' => (string) $selectedYear, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null],
            ['month' => 'Candidate BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
            ['month' => 'Client BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
            ['month' => 'Total', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
        ];

        $candidateGpSum = 0;
        $clientGpSum = 0;
        $candidateCount = 0;
        $clientCount = 0;

        foreach ($months as $month) {
            if ($month === (string) $selectedYear) {
                continue;
            }

            foreach (['Candidate BO', 'Client BO'] as $boType) {
                $query = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Temporary')
                    ->where('status_id', $backoutStatusId)
                    ->where('form_status', 'Approved')
                    ->where('bo_type', $boType)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month]);

                $jobs = $query->get();
                $hc = $jobs->count();
                $br = $jobs->sum('final_billing_value') ?? $jobs->sum('bill_rate') ?? 0;
                $pr = $jobs->sum('pay_rate') ?? 0;
                $finalGp = $jobs->sum('final_gp') ?? 0;
                $gpPercentSum = $jobs->sum('percentage_gp') ?? 0;

                if ($boType === 'Candidate BO') {
                    $backoutRows[1]['HC'] += $hc;
                    $backoutRows[1]['BR'] += $br;
                    $backoutRows[1]['PR'] += $pr;
                    $backoutRows[1]['Final_GP'] += $finalGp;
                    $candidateGpSum += $gpPercentSum;
                    $candidateCount += $hc;
                } else {
                    $backoutRows[2]['HC'] += $hc;
                    $backoutRows[2]['BR'] += $br;
                    $backoutRows[2]['PR'] += $pr;
                    $backoutRows[2]['Final_GP'] += $finalGp;
                    $clientGpSum += $gpPercentSum;
                    $clientCount += $hc;
                }
            }
        }

        $backoutRows[1]['GP%'] = $candidateCount > 0 ? $candidateGpSum / $candidateCount : 0;
        $backoutRows[2]['GP%'] = $clientCount > 0 ? $clientGpSum / $clientCount : 0;
        $backoutRows[3]['HC'] = $backoutRows[1]['HC'] + $backoutRows[2]['HC'];
        $backoutRows[3]['BR'] = $backoutRows[1]['BR'] + $backoutRows[2]['BR'];
        $backoutRows[3]['PR'] = $backoutRows[1]['PR'] + $backoutRows[2]['PR'];
        $backoutRows[3]['Final_GP'] = $backoutRows[1]['Final_GP'] + $backoutRows[2]['Final_GP'];
        $backoutRows[3]['GP%'] = ($candidateCount + $clientCount) > 0 ? ($candidateGpSum + $clientGpSum) / ($candidateCount + $clientCount) : 0;

        Log::info('Backout Rows Generated', ['backoutRows' => $backoutRows]);

        return $backoutRows;
    }

    private function getTerminationData($companyId, $selectedYear, $months)
    {
        Log::info('Fetching Termination Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $terminationStatusId = DB::table('status_masters')->where('status', 'Termination')->value('id');

        if (!$terminationStatusId) {
            Log::warning('No Termination status found in status_master');
            $terminationRows = [];
            foreach ($months as $month) {
                $row = ['month' => $month, 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
                if ($month === (string) $selectedYear) {
                    $row = ['month' => $month, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null];
                }
                $terminationRows[] = $row;
            }
            $terminationRows[] = ['month' => 'Total', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
            return $terminationRows;
        }

        $terminationRows = [];
        foreach ($months as $month) {
            $row = ['month' => $month, 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
            if ($month === (string) $selectedYear) {
                $row = ['month' => $month, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null];
            } else {
                $query = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Temporary')
                    ->where('status_id', $terminationStatusId)
                    ->where('form_status', 'Approved')
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month]);

                $jobs = $query->get();
                $hc = $jobs->count();
                $br = $jobs->sum('bill_rate') ?? 0;
                $pr = $jobs->sum('pay_rate') ?? 0;
                $finalGp = $jobs->sum('final_gp') ?? 0;
                $gpPercent = $hc > 0 ? ($jobs->sum('percentage_gp') / $hc) : 0;

                $row = ['month' => $month, 'HC' => $hc, 'BR' => $br, 'PR' => $pr, 'Final_GP' => $finalGp, 'GP%' => $gpPercent];
            }
            $terminationRows[] = $row;
        }

        $totalHC = array_sum(array_column($terminationRows, 'HC') ?? [0]);
        $totalBR = array_sum(array_column($terminationRows, 'BR') ?? [0]);
        $totalPR = array_sum(array_column($terminationRows, 'PR') ?? [0]);
        $totalFinalGP = array_sum(array_column($terminationRows, 'Final_GP') ?? [0]);
        $totalGPPercent = $totalHC > 0 ? array_sum(array_column($terminationRows, 'GP%') ?? [0]) / count(array_filter($terminationRows, fn($row) => $row['HC'] > 0)) : 0;
        $terminationRows[] = ['month' => 'Total', 'HC' => $totalHC, 'BR' => $totalBR, 'PR' => $totalPR, 'Final_GP' => $totalFinalGP, 'GP%' => $totalGPPercent];

        Log::info('Termination Rows Generated', ['terminationRows' => $terminationRows]);

        return $terminationRows;
    }

    private function getPoExpiryData($companyId, $selectedYear)
    {
        Log::info('Fetching PO Expiry Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $joinedStatusId = StatusMaster::where('status', 'Joined')->value('id');

        if (!$joinedStatusId) {
            Log::warning('No Joined status found in status_master for PO Expiry');
            return [
                ['poEndYear' => 'No Data', 'client' => '', 'poEndMonth' => '', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0]
            ];
        }

        $currentDate = Carbon::now();
        $endOfSelectedYear = Carbon::create($selectedYear, 12, 31)->endOfDay();

        $poExpiryData = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Approved')
            ->where('status_id', $joinedStatusId)
            ->whereNotNull('join_date')
            ->whereYear('join_date', '<=', $selectedYear)
            ->where(function ($query) use ($endOfSelectedYear) {
                $query->whereNull('backout_term_date')
                    ->orWhere('backout_term_date', '>', $endOfSelectedYear);
            })
            ->whereNotNull('po_end_date')
            ->where('po_end_date', '<=', $currentDate)
            ->where('po_end_year', $selectedYear)
            ->get()
            ->groupBy(['po_end_year', 'client_id']);

        Log::info('PO Expiry Query Results', [
            'company_id' => $companyId,
            'selected_year' => $selectedYear,
            'total_records' => $poExpiryData->flatten()->count(),
            'grouped_keys' => $poExpiryData->keys()->toArray(),
        ]);


        if ($poExpiryData->isEmpty()) {
            return [
                ['poEndYear' => $selectedYear, 'client' => '', 'poEndMonth' => '', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
                ['poEndYear' => 'Total', 'client' => '', 'poEndMonth' => '', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0]
            ];
        }

        $poExpiryRows = [];
        foreach ($poExpiryData as $year => $clients) {

            $poExpiryRows[] = [
                'poEndYear' => $year,
                'client' => '',
                'poEndMonth' => '',
                'HC' => null,
                'BR' => null,
                'PR' => null,
                'Final_GP' => null,
                'GP%' => null,
            ];

            $yearlyHc = 0;
            $yearlyGpPercent = 0;
            $yearClientRows = [];

            foreach ($clients as $clientId => $jobs) {
                $clientName = $jobs->first()->client ? $jobs->first()->client->client_name : 'Unknown';
                $hc = $jobs->count();
                $br = $jobs->sum('bill_rate') ?? 0;
                $pr = $jobs->sum('pay_rate') ?? 0;
                $finalGp = $jobs->sum('final_gp') ?? 0;
                $gpPercent = $hc > 0 ? ($jobs->sum('percentage_gp') / $hc) : 0;  // Average GP%

                $row = [
                    'poEndYear' => '',
                    'client' => $clientName,
                    'poEndMonth' => $jobs->first()->po_end_month ?? '',  // First job's PO month
                    'HC' => $hc,
                    'BR' => $br,
                    'PR' => $pr,
                    'Final_GP' => $finalGp,
                    'GP%' => $gpPercent,
                ];
                $poExpiryRows[] = $row;
                $yearClientRows[] = $row;
                $yearlyHc += $hc;
                $yearlyGpPercent += $gpPercent * $hc;
            }


            $yearlyGpPercent = $yearlyHc > 0 ? ($yearlyGpPercent / $yearlyHc) : 0;
            $poExpiryRows[] = [
                'poEndYear' => "$year Total",
                'client' => '',
                'poEndMonth' => '',
                'HC' => $yearlyHc,
                'BR' => array_sum(array_column($yearClientRows, 'BR')),
                'PR' => array_sum(array_column($yearClientRows, 'PR')),
                'Final_GP' => array_sum(array_column($yearClientRows, 'Final_GP')),
                'GP%' => $yearlyGpPercent,
            ];
        }


        $clientRows = array_filter($poExpiryRows, function ($row) {
            return !empty($row['client']);
        });

        $grandTotalHC = array_sum(array_column($clientRows, 'HC') ?? [0]);
        $grandTotalBR = array_sum(array_column($clientRows, 'BR') ?? [0]);
        $grandTotalPR = array_sum(array_column($clientRows, 'PR') ?? [0]);
        $grandTotalFinalGP = array_sum(array_column($clientRows, 'Final_GP') ?? [0]);
        $grandTotalGPPercent = $grandTotalBR > 0 ? ($grandTotalFinalGP / $grandTotalBR * 100) : 0;

        $poExpiryRows[] = [
            'poEndYear' => 'Total',
            'client' => '',
            'poEndMonth' => '',
            'HC' => $grandTotalHC,
            'BR' => $grandTotalBR,
            'PR' => $grandTotalPR,
            'Final_GP' => $grandTotalFinalGP,
            'GP%' => $grandTotalGPPercent,
        ];

        Log::info('PO Expiry Rows Generated', [
            'poExpiryRows' => $poExpiryRows,
            'grand_total_hc' => $grandTotalHC,
            'grand_total_br' => $grandTotalBR,
        ]);

        return $poExpiryRows;
    }

    private function getContractData($companyId, $selectedYear)
    {
        Log::info('Fetching Contract Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $joinedStatusId = StatusMaster::where('status', 'Joined')->value('id');
        $offeredStatusId = StatusMaster::where('status', 'Offered')->value('id');
        $selectedStatusId = StatusMaster::where('status', 'Selected')->value('id');

        $jobSeekersQuery = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Approved');

        $statusData = $jobSeekersQuery->get()->filter(function ($job) use ($selectedYear, $joinedStatusId) {
            if ($job->status_id == $joinedStatusId && $job->join_date) {

                return Carbon::parse($job->join_date)->year <= $selectedYear &&
                    (is_null($job->backout_term_date) || Carbon::parse($job->backout_term_date)->year > $selectedYear);
            }
            if ($job->status_id == StatusMaster::where('status', 'Offered')->value('id') && $job->offer_date) {
                return Carbon::parse($job->offer_date)->year == $selectedYear;
            }
            if ($job->status_id == StatusMaster::where('status', 'Selected')->value('id') && $job->selection_date) {
                return Carbon::parse($job->selection_date)->year == $selectedYear;
            }
            return false;
        })->groupBy('status_id');



        $contractRows = [];
        $statuses = [
            'Joined' => $joinedStatusId,
            'Offered' => $offeredStatusId,
            'Selected' => $selectedStatusId,
        ];

        foreach ($statuses as $status => $statusId) {
            $jobs = $statusData->get($statusId, collect());
            $hc = $jobs->count();
            $br = $jobs->sum('bill_rate') ?? 0;
            $pr = $jobs->sum('pay_rate') ?? 0;
            $finalGp = $jobs->sum('final_gp') ?? 0;
            $gpPercent = $hc > 0 ? ($jobs->sum('percentage_gp') / $hc) : 0;

            $totalRow = [
                'status' => $status,
                'client' => '',
                'HC' => $hc,
                'BR' => $br,
                'PR' => $pr,
                'Final_GP' => $finalGp,
                'GP%' => $gpPercent,
            ];
            $contractRows[] = $totalRow;

            $clientData = $jobs->groupBy('client_id');
            foreach ($clientData as $clientId => $clientJobs) {
                $clientHc = $clientJobs->count();
                $clientBr = $clientJobs->sum('bill_rate') ?? 0;
                $clientPr = $clientJobs->sum('pay_rate') ?? 0;
                $clientFinalGp = $clientJobs->sum('final_gp') ?? 0;
                $clientGpPercent = $clientHc > 0 ? ($clientJobs->sum('percentage_gp') / $clientHc) : 0;
                $clientName = $clientJobs->first()->client->client_name ?? 'Unknown';

                $contractRows[] = [
                    'status' => '',
                    'client' => $clientName,
                    'HC' => $clientHc,
                    'BR' => $clientBr,
                    'PR' => $clientPr,
                    'Final_GP' => $clientFinalGp,
                    'GP%' => $clientGpPercent,
                ];
            }
        }

        $statusRows = array_filter($contractRows, fn($row) => !empty($row['status']) && $row['status'] !== 'Grand Total');
        $validStatusRows = array_filter($statusRows, fn($row) => $row['HC'] > 0);
        $grandTotalHC = array_sum(array_column($validStatusRows, 'HC'));
        $grandTotalBR = array_sum(array_column($validStatusRows, 'BR'));
        $grandTotalPR = array_sum(array_column($validStatusRows, 'PR'));
        $grandTotalFinalGP = array_sum(array_column($validStatusRows, 'Final_GP'));
        $grandTotalGPPercent = count($validStatusRows) > 0 ? (array_sum(array_column($validStatusRows, 'GP%')) / count($validStatusRows)) : 0;

        $contractRows[] = [
            'status' => 'Grand Total',
            'client' => '',
            'HC' => $grandTotalHC,
            'BR' => $grandTotalBR,
            'PR' => $grandTotalPR,
            'Final_GP' => $grandTotalFinalGP,
            'GP%' => $grandTotalGPPercent,
        ];

        return $contractRows;
    }

    private function getFieldConfig()
    {
        return [
            'India' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'business_unit_id', 'label' => 'Business Unit'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'dm_id', 'label' => 'Delivery Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Consultant Code'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'client_reporting_manager', 'label' => 'Client Reporting Manager'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout/Term Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout/Term Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout/Term Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'otc', 'label' => 'OTC'],
                    ['name' => 'otc_split', 'label' => 'OTC Split'],
                    ['name' => 'msp_fees', 'label' => 'MSP Fees'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'business_unit_id', 'label' => 'Business Unit'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'dm_id', 'label' => 'Delivery Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
            ],
            'APAC' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Consultant Code'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout/Term Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout/Term Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout/Term Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'msp_fees', 'label' => 'MSP Fees'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
            ],
            'EU-UK' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout/End Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout/End Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout/End Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'pay_rate_usd', 'label' => 'Pay Rate (USD)'],
                    ['name' => 'bill_rate_usd', 'label' => 'Bill Rate (USD)'],
                    ['name' => 'basic_pay_rate', 'label' => 'Basic Pay Rate'],
                    ['name' => 'benefits', 'label' => 'Benefits'],
                    ['name' => 'gp_hour', 'label' => 'GP/Hour'],
                    ['name' => 'gp_hour_usd', 'label' => 'GP/Hour (USD)'],
                    ['name' => 'percentage_gp', 'label' => 'GP %'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'client_reporting_manager', 'label' => 'Manager'],
                    ['name' => 'consultant_code', 'label' => 'Placement Code (Ceipal)'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                ],
            ],
            'Aegis' => [
                'temporary' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'hire_type', 'label' => 'Hire Type'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_code', 'label' => 'Placement Code (Ceipal)'],
                    ['name' => 'consultant_name', 'label' => 'Consultant Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'sap_id', 'label' => 'SAP ID'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backoutout Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backoutout Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backoutout Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'pay_rate', 'label' => 'Pay Rate'],
                    ['name' => 'pay_rate_1', 'label' => 'Pay Rate 1'],
                    ['name' => 'bill_rate', 'label' => 'Bill Rate'],
                    ['name' => 'gp_month', 'label' => 'GP/Month'],
                    ['name' => 'loaded_cost', 'label' => 'Loaded Cost'],
                    ['name' => 'final_gp', 'label' => 'Final GP'],
                    ['name' => 'percentage_gp', 'label' => '% GP'],
                    ['name' => 'end_client', 'label' => 'End Client'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'domain', 'label' => 'Domain'],
                ],
                'permanent' => [
                    ['name' => 'company_id', 'label' => 'Country'],
                    ['name' => 'location_id', 'label' => 'Location'],
                    ['name' => 'am_id', 'label' => 'Account Manager'],
                    ['name' => 'tl_id', 'label' => 'Team Leader'],
                    ['name' => 'recruiter_id', 'label' => 'Recruiter'],
                    ['name' => 'consultant_name', 'label' => 'Candidate Name'],
                    ['name' => 'skill', 'label' => 'Skills'],
                    ['name' => 'status_id', 'label' => 'Status'],
                    ['name' => 'client_id', 'label' => 'Client'],
                    ['name' => 'poc', 'label' => 'POC'],
                    ['name' => 'quarter', 'label' => 'Quarter'],
                    ['name' => 'selection_date', 'label' => 'Selection Date'],
                    ['name' => 'select_month', 'label' => 'Select Month'],
                    ['name' => 'offer_date', 'label' => 'Offer Date'],
                    ['name' => 'join_date', 'label' => 'Join Date'],
                    ['name' => 'join_month', 'label' => 'Join Month'],
                    ['name' => 'join_year', 'label' => 'Join Year'],
                    ['name' => 'qly_date', 'label' => 'Qualify Date'],
                    ['name' => 'backout_term_date', 'label' => 'Backout Out Date'],
                    ['name' => 'backout_term_month', 'label' => 'Backout Out Month'],
                    ['name' => 'backout_term_year', 'label' => 'Backout Out Year'],
                    ['name' => 'reason_of_attrition', 'label' => 'Reason of Attrition'],
                    ['name' => 'type_of_attrition', 'label' => 'Type of Attrition'],
                    ['name' => 'reason_of_rejection', 'label' => 'Reason of Rejection'],
                    ['name' => 'bo_type', 'label' => 'BO Type'],
                    ['name' => 'po_end_date', 'label' => 'PO End Date'],
                    ['name' => 'po_end_month', 'label' => 'PO End Month'],
                    ['name' => 'po_end_year', 'label' => 'PO End Year'],
                    ['name' => 'ctc_offered', 'label' => 'CTC Offered'],
                    ['name' => 'billing_value', 'label' => 'Billing Value'],
                    ['name' => 'loaded_gp', 'label' => 'Loaded GP'],
                    ['name' => 'final_billing_value', 'label' => 'Final Billing Value'],
                    ['name' => 'actual_billing_value', 'label' => 'Actual Billing Value'],
                    ['name' => 'invoice_no', 'label' => 'Invoice No'],
                    ['name' => 'lob', 'label' => 'LOB'],
                    ['name' => 'remark1', 'label' => 'Remark'],
                    ['name' => 'remark2', 'label' => 'Remark 2'],
                    ['name' => 'sources', 'label' => 'Sources'],
                    ['name' => 'domain', 'label' => 'Domain'],
                ],
            ],
        ];
    }

}