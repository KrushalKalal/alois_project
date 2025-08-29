<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobSeeker;
use App\Models\CompanyMaster;
use App\Models\StatusMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $role = $employee ? $employee->role : null;

        $validRoles = ['maker', 'checker', 'po_maker', 'po_checker', 'backout_maker', 'backout_checker', 'finance_maker', 'finance_checker'];
        if (!$employee || !in_array($role, $validRoles)) {
            return Inertia::render('Error', ['status' => 403, 'message' => 'Unauthorized']);
        }

        // Dynamic company-to-country mapping
        $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();

        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $selectedYear = (int) $request->query('year', $currentYear);

        // Dynamic year range: currentYear - 5 to currentYear + 1
        $availableYears = range($currentYear - 5, $currentYear + 1);

        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $currentYear;
        }

        // Dynamic months based on selected year
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

        $dashboardData = [];
        $companyIds = in_array($role, ['po_maker', 'po_checker', 'backout_maker', 'backout_checker', 'finance_maker', 'finance_checker'])
            ? array_keys($companyToCountry)
            : [$employee->company_id];

        foreach ($companyIds as $companyId) {
            $region = $companyToCountry[$companyId] ?? 'Unknown';
            $currency = $region === 'APAC' ? 'AUD' : ($region === 'EU-UK' ? 'GBP' : 'INR');
            $companyData = [
                'region' => $region,
                'currency' => $currency,
                'permData' => [],
                'backoutData' => [],
                'terminationData' => [],
                'poExpiryData' => [],
                'contractData' => [],
                'financeData' => [],
            ];

            if (in_array($role, ['maker', 'checker'])) {
                $companyData['permData'] = $this->getPermanentData($companyId, $selectedYear, $months);
                $companyData['backoutData'] = $this->getBackoutData($companyId, $selectedYear);
                $companyData['terminationData'] = $this->getTerminationData($companyId, $selectedYear, $months);
                $companyData['poExpiryData'] = $this->getPoExpiryData($companyId, $selectedYear);
                $companyData['contractData'] = $this->getContractData($companyId, $selectedYear);
            } elseif (in_array($role, ['po_maker', 'po_checker'])) {
                $companyData['poExpiryData'] = $this->getPoExpiryData($companyId, $selectedYear);
            } elseif (in_array($role, ['backout_maker', 'backout_checker'])) {
                $companyData['backoutData'] = $this->getBackoutData($companyId, $selectedYear);
                $companyData['terminationData'] = $this->getTerminationData($companyId, $selectedYear, $months);
            } elseif (in_array($role, ['finance_maker', 'finance_checker'])) {
                $companyData['financeData'] = $this->getFinanceData($companyId, $selectedYear);
            }

            $dashboardData[$companyId] = $companyData;
        }

        if (in_array($role, ['maker', 'checker'])) {
            $dashboardData = reset($dashboardData);
        }

        return Inertia::render('Employee/Dashboard', [
            'auth' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role,
                'employee' => $employee ? [
                    'id' => $employee->id,
                    'user_id' => $employee->user_id,
                    'emp_id' => $employee->emp_id,
                    'name' => $employee->name,
                    'company_id' => $employee->company_id,
                    'role' => $employee->role,
                ] : null,
            ],
            'dashboardData' => $dashboardData,
            'companyToCountry' => $companyToCountry,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'currentYear' => $currentYear,
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $role = $employee ? $employee->role : null;

        if (!$employee || !in_array($role, ['maker', 'checker'])) {
            abort(403, 'Unauthorized: Export is only available for maker and checker roles.');
        }

        $companyId = $employee->company_id;
        $selectedYear = (int) $request->query('year', Carbon::now()->year);

        // Dynamic company-to-country mapping
        $companyToCountry = CompanyMaster::pluck('region', 'id')->toArray();

        if (!array_key_exists($companyId, $companyToCountry)) {
            abort(400, 'Invalid company ID');
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
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("$region Dashboard $selectedYear");

        // Disable grid lines for the entire sheet
        $sheet->setShowGridlines(false);

        // Starting positions
        $row1 = 1; // Table 1 starts here
        $row5 = 1; // Table 5 starts alongside Table 1

        // Table 1: Perm Data (Columns A-H)
        $sheet->setCellValue("A$row1", "Perm Data $selectedYear ($currency)");
        $sheet->mergeCells("A$row1:H$row1");
        $sheet->getStyle("A$row1")->getFont()->setBold(true)->setSize(14)->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("A$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row1")->getFill()->setFillType(Fill::FILL_NONE);
        $row1++;
        $sheet->setCellValue("A$row1", ''); // Blank row after title
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
            if ($rowData['month'] === 'Total')
                continue; // Skip total row for data
            $sheet->setCellValue("A$row1", $rowData['month']);
            $sheet->setCellValue("B$row1", $rowData['Selected'] !== null ? number_format($rowData['Selected'], 2) : '');
            $sheet->setCellValue("C$row1", $rowData['Backout'] !== null ? number_format($rowData['Backout'], 2) : '');
            $sheet->setCellValue("D$row1", $rowData['Terminated'] !== null ? number_format($rowData['Terminated'], 2) : '');
            $sheet->setCellValue("E$row1", $rowData['Offered'] !== null ? number_format($rowData['Offered'], 2) : '');
            $sheet->setCellValue("F$row1", $rowData['Joined'] !== null ? number_format($rowData['Joined'], 2) : '');
            $sheet->setCellValue("G$row1", $rowData['FTEConversionFees'] !== null ? number_format($rowData['FTEConversionFees'], 2) : '');
            $sheet->setCellValue("H$row1", $rowData['Total'] !== null ? number_format($rowData['Total'], 2) : '');
            $sheet->getStyle("A$row1:H$row1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            foreach (range('B', 'H') as $col) {
                $sheet->getStyle("{$col}$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row1++;
        }
        // Total row for Perm Data
        $totals = [
            'Selected' => 0,
            'Backout' => 0,
            'Terminated' => 0,
            'Offered' => 0,
            'Joined' => 0,
            'FTEConversionFees' => 0,
            'Total' => 0
        ];
        foreach ($data['permData'] as $rowData) {
            if ($rowData['month'] === 'Total')
                continue;
            foreach ($totals as $key => &$total) {
                $total += $rowData[$key] ?? 0;
            }
        }
        $sheet->setCellValue("A$row1", 'Total');
        $sheet->setCellValue("B$row1", $totals['Selected'] ? number_format($totals['Selected'], 2) : '');
        $sheet->setCellValue("C$row1", $totals['Backout'] ? number_format($totals['Backout'], 2) : '');
        $sheet->setCellValue("D$row1", $totals['Terminated'] ? number_format($totals['Terminated'], 2) : '');
        $sheet->setCellValue("E$row1", $totals['Offered'] ? number_format($totals['Offered'], 2) : '');
        $sheet->setCellValue("F$row1", $totals['Joined'] ? number_format($totals['Joined'], 2) : '');
        $sheet->setCellValue("G$row1", $totals['FTEConversionFees'] ? number_format($totals['FTEConversionFees'], 2) : '');
        $sheet->setCellValue("H$row1", $totals['Total'] ? number_format($totals['Total'], 2) : '');
        $sheet->getStyle("A$row1:H$row1")->getFont()->setBold(true);
        $sheet->getStyle("A$row1:H$row1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyle("A$row1:H$row1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'H') as $col) {
            $sheet->getStyle("{$col}$row1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
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
            if ($rowData['status'] === 'Grand Total')
                continue; // Skip grand total row
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
            foreach (range($colKRow1 + 2, $colKRow1 + 6) as $col) {
                $sheet->getStyleByColumnAndRow($col, $row5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row5++;
        }
        // Total row for Contracting JOS
        $totals = ['HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
        $countForAvg = 0;
        foreach ($data['contractData'] as $rowData) {
            if ($rowData['status'] === 'Grand Total')
                continue;
            $totals['HC'] += $rowData['HC'] ?? 0;
            $totals['BR'] += $rowData['BR'] ?? 0;
            $totals['PR'] += $rowData['PR'] ?? 0;
            $totals['Final_GP'] += $rowData['Final_GP'] ?? 0;
            if ($rowData['HC'] > 0) {
                $totals['GP%'] += $rowData['GP%'] ?? 0;
                $countForAvg++;
            }
        }
        $totals['GP%'] = $countForAvg > 0 ? $totals['GP%'] / $countForAvg : 0;
        $sheet->setCellValueByColumnAndRow($colKRow1, $row5, 'Total');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 1, $row5, '');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 2, $row5, $totals['HC'] ? $totals['HC'] : '');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 3, $row5, $totals['BR'] ? number_format($totals['BR'], 2) : '');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 4, $row5, $totals['PR'] ? number_format($totals['PR'], 2) : '');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 5, $row5, $totals['Final_GP'] ? number_format($totals['Final_GP'], 2) : '');
        $sheet->setCellValueByColumnAndRow($colKRow1 + 6, $row5, $totals['GP%'] ? number_format($totals['GP%'], 2) : '');
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFont()->setBold(true);
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyleByColumnAndRow($colKRow1, $row5, $colKRow1 + 6, $row5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range($colKRow1 + 2, $colKRow1 + 6) as $col) {
            $sheet->getStyleByColumnAndRow($col, $row5)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
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
            if ($rowData['month'] === 'Total')
                continue; // Skip total row
            $sheet->setCellValue("A$row2", $rowData['month']);
            $sheet->setCellValue("B$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("C$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("D$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("E$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            foreach (range('B', 'F') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for Backout Data
        $totals = ['HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
        $countForAvg = 0;
        foreach ($data['backoutData'] as $rowData) {
            if ($rowData['month'] === 'Total')
                continue;
            $totals['HC'] += $rowData['HC'] ?? 0;
            $totals['BR'] += $rowData['BR'] ?? 0;
            $totals['PR'] += $rowData['PR'] ?? 0;
            $totals['Final_GP'] += $rowData['Final_GP'] ?? 0;
            if ($rowData['HC'] > 0) {
                $totals['GP%'] += $rowData['GP%'] ?? 0;
                $countForAvg++;
            }
        }
        $totals['GP%'] = $countForAvg > 0 ? $totals['GP%'] / $countForAvg : 0;
        $sheet->setCellValue("A$row2", 'Total');
        $sheet->setCellValue("B$row2", $totals['HC'] ? $totals['HC'] : '');
        $sheet->setCellValue("C$row2", $totals['BR'] ? number_format($totals['BR'], 2) : '');
        $sheet->setCellValue("D$row2", $totals['PR'] ? number_format($totals['PR'], 2) : '');
        $sheet->setCellValue("E$row2", $totals['Final_GP'] ? number_format($totals['Final_GP'], 2) : '');
        $sheet->setCellValue("F$row2", $totals['GP%'] ? number_format($totals['GP%'], 2) : '');
        $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true);
        $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'F') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
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
            if ($rowData['month'] === 'Total')
                continue; // Skip total row
            $sheet->setCellValue("A$row2", $rowData['month']);
            $sheet->setCellValue("B$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("C$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("D$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("E$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            foreach (range('B', 'F') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for Termination Data
        $totals = ['HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
        $countForAvg = 0;
        foreach ($data['terminationData'] as $rowData) {
            if ($rowData['month'] === 'Total')
                continue;
            $totals['HC'] += $rowData['HC'] ?? 0;
            $totals['BR'] += $rowData['BR'] ?? 0;
            $totals['PR'] += $rowData['PR'] ?? 0;
            $totals['Final_GP'] += $rowData['Final_GP'] ?? 0;
            if ($rowData['HC'] > 0) {
                $totals['GP%'] += $rowData['GP%'] ?? 0;
                $countForAvg++;
            }
        }
        $totals['GP%'] = $countForAvg > 0 ? $totals['GP%'] / $countForAvg : 0;
        $sheet->setCellValue("A$row2", 'Total');
        $sheet->setCellValue("B$row2", $totals['HC'] ? $totals['HC'] : '');
        $sheet->setCellValue("C$row2", $totals['BR'] ? number_format($totals['BR'], 2) : '');
        $sheet->setCellValue("D$row2", $totals['PR'] ? number_format($totals['PR'], 2) : '');
        $sheet->setCellValue("E$row2", $totals['Final_GP'] ? number_format($totals['Final_GP'], 2) : '');
        $sheet->setCellValue("F$row2", $totals['GP%'] ? number_format($totals['GP%'], 2) : '');
        $sheet->getStyle("A$row2:F$row2")->getFont()->setBold(true);
        $sheet->getStyle("A$row2:F$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyle("A$row2:F$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('B', 'F') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
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
            if ($rowData['poEndYear'] === 'Total')
                continue; // Skip total row
            $sheet->setCellValue("A$row2", $rowData['poEndYear']);
            $sheet->setCellValue("B$row2", $rowData['client']);
            $sheet->setCellValue("C$row2", $rowData['poEndMonth'] ?? '');
            $sheet->setCellValue("D$row2", $rowData['HC'] !== null ? $rowData['HC'] : '');
            $sheet->setCellValue("E$row2", $rowData['BR'] !== null ? number_format($rowData['BR'], 2) : '');
            $sheet->setCellValue("F$row2", $rowData['PR'] !== null ? number_format($rowData['PR'], 2) : '');
            $sheet->setCellValue("G$row2", $rowData['Final_GP'] !== null ? number_format($rowData['Final_GP'], 2) : '');
            $sheet->setCellValue("H$row2", $rowData['GP%'] !== null ? number_format($rowData['GP%'], 2) : '');
            $sheet->getStyle("A$row2:H$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            foreach (range('D', 'H') as $col) {
                $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $row2++;
        }
        // Total row for PO Expiry
        $totals = ['HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
        $countForAvg = 0;
        foreach ($data['poExpiryData'] as $rowData) {
            if ($rowData['poEndYear'] === 'Total')
                continue;
            $totals['HC'] += $rowData['HC'] ?? 0;
            $totals['BR'] += $rowData['BR'] ?? 0;
            $totals['PR'] += $rowData['PR'] ?? 0;
            $totals['Final_GP'] += $rowData['Final_GP'] ?? 0;
            if ($rowData['HC'] > 0) {
                $totals['GP%'] += $rowData['GP%'] ?? 0;
                $countForAvg++;
            }
        }
        $totals['GP%'] = $countForAvg > 0 ? $totals['GP%'] / $countForAvg : 0;
        $sheet->setCellValue("A$row2", 'Total');
        $sheet->setCellValue("B$row2", '');
        $sheet->setCellValue("C$row2", '');
        $sheet->setCellValue("D$row2", $totals['HC'] ? $totals['HC'] : '');
        $sheet->setCellValue("E$row2", $totals['BR'] ? number_format($totals['BR'], 2) : '');
        $sheet->setCellValue("F$row2", $totals['PR'] ? number_format($totals['PR'], 2) : '');
        $sheet->setCellValue("G$row2", $totals['Final_GP'] ? number_format($totals['Final_GP'], 2) : '');
        $sheet->setCellValue("H$row2", $totals['GP%'] ? number_format($totals['GP%'], 2) : '');
        $sheet->getStyle("A$row2:H$row2")->getFont()->setBold(true);
        $sheet->getStyle("A$row2:H$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyle("A$row2:H$row2")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('D', 'H') as $col) {
            $sheet->getStyle("{$col}$row2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Auto-size columns
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate and download the file
        $writer = new Xlsx($spreadsheet);
        $filename = "{$region}_Dashboard_{$selectedYear}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
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
        $totalRow = [
            'month' => 'Total',
            'Selected' => 0,
            'Backout' => 0,
            'Terminated' => 0,
            'Offered' => 0,
            'Joined' => 0,
            'FTEConversionFees' => 0,
            'Total' => 0,
        ];

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
                $row['Selected'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $selectedStatusId)
                    ->whereNotNull('selection_date')
                    ->whereRaw("DATE_FORMAT(selection_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Backout'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $backoutStatusId)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Terminated'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $terminatedStatusId)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Offered'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $offeredStatusId)
                    ->whereNotNull('offer_date')
                    ->whereRaw("DATE_FORMAT(offer_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Joined'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $joinedStatusId)
                    ->whereNotNull('join_date')
                    ->whereRaw("DATE_FORMAT(join_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['FTEConversionFees'] = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Permanent')
                    ->where('form_status', 'Approved')
                    ->where('status_id', $fteStatusId)
                    ->whereNotNull('join_date')
                    ->whereRaw("DATE_FORMAT(join_date, '%m-%Y') = ?", [$month])
                    ->sum('final_billing_value') ?? 0;

                $row['Total'] = array_sum(array_slice($row, 1, 6));

                // Update Total row
                $totalRow['Selected'] += $row['Selected'] ?? 0;
                $totalRow['Backout'] += $row['Backout'] ?? 0;
                $totalRow['Terminated'] += $row['Terminated'] ?? 0;
                $totalRow['Offered'] += $row['Offered'] ?? 0;
                $totalRow['Joined'] += $row['Joined'] ?? 0;
                $totalRow['FTEConversionFees'] += $row['FTEConversionFees'] ?? 0;
                $totalRow['Total'] += $row['Total'] ?? 0;
            }
            $permRows[] = $row;
        }

        // Add Total row
        $permRows[] = $totalRow;

        Log::info('Permanent Data Rows', ['rows' => $permRows]);

        return $permRows;
    }

    private function getBackoutData($companyId, $selectedYear)
    {
        $employee = Auth::user()->employee;
        Log::info('Fetching Backout Data for Company ID and Employee', ['company_id' => $companyId, 'year' => $selectedYear, 'maker_id' => $employee->id]);

        $backoutStatusId = DB::table('status_masters')->where('status', 'Backout')->value('id');

        if (!$backoutStatusId) {
            Log::warning('No Backout status found in status_master');
            return [
                ['month' => (string) $selectedYear, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null],
                ['month' => 'Candidate BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
                ['month' => 'Client BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
                ['month' => 'Grand Total', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
            ];
        }

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = sprintf('%02d-%d', $m, $selectedYear);
        }
        $months[] = (string) $selectedYear;

        $backoutRows = [
            ['month' => (string) $selectedYear, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null],
            ['month' => 'Candidate BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
            ['month' => 'Client BO', 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0],
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
                    ->where('maker_id', $employee->id)
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

        // Add Grand Total row
        $grandTotalHC = $backoutRows[1]['HC'] + $backoutRows[2]['HC'];
        $grandTotalBR = $backoutRows[1]['BR'] + $backoutRows[2]['BR'];
        $grandTotalPR = $backoutRows[1]['PR'] + $backoutRows[2]['PR'];
        $grandTotalFinalGP = $backoutRows[1]['Final_GP'] + $backoutRows[2]['Final_GP'];
        $grandTotalGPPercent = ($candidateCount + $clientCount) > 0 ? ($candidateGpSum + $clientGpSum) / ($candidateCount + $clientCount) : 0;

        $backoutRows[] = [
            'month' => 'Grand Total',
            'HC' => $grandTotalHC,
            'BR' => $grandTotalBR,
            'PR' => $grandTotalPR,
            'Final_GP' => $grandTotalFinalGP,
            'GP%' => $grandTotalGPPercent,
        ];

        Log::info('Backout Rows Generated', ['backoutRows' => $backoutRows]);

        return $backoutRows;
    }

    private function getTerminationData($companyId, $selectedYear, $months)
    {
        $employee = Auth::user()->employee;
        Log::info('Fetching Termination Data for Company ID and Employee', ['company_id' => $companyId, 'year' => $selectedYear, 'maker_id' => $employee->id]);

        $terminationStatusId = DB::table('status_masters')->where('status', 'Termination')->value('id');

        $terminationRows = [];
        $totalRow = [
            'month' => 'Total',
            'HC' => 0,
            'BR' => 0,
            'PR' => 0,
            'Final_GP' => 0,
            'GP%' => 0,
        ];

        if (!$terminationStatusId) {
            Log::warning('No Termination status found in status_master');
            foreach ($months as $month) {
                $row = ['month' => $month, 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
                if ($month === (string) $selectedYear) {
                    $row = ['month' => $month, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null];
                }
                $terminationRows[] = $row;
            }
            $terminationRows[] = $totalRow;
            return $terminationRows;
        }

        foreach ($months as $month) {
            $row = ['month' => $month, 'HC' => 0, 'BR' => 0, 'PR' => 0, 'Final_GP' => 0, 'GP%' => 0];
            if ($month === (string) $selectedYear) {
                $row = ['month' => $month, 'HC' => null, 'BR' => null, 'PR' => null, 'Final_GP' => null, 'GP%' => null];
            } else {
                $query = JobSeeker::where('company_id', $companyId)
                    ->where('job_seeker_type', 'Temporary')
                    ->where('status_id', $terminationStatusId)
                    ->where('form_status', 'Approved')
                    ->where('maker_id', $employee->id)
                    ->whereNotNull('backout_term_date')
                    ->whereRaw("DATE_FORMAT(backout_term_date, '%m-%Y') = ?", [$month]);

                $jobs = $query->get();
                $hc = $jobs->count();
                $br = $jobs->sum('bill_rate') ?? 0;
                $pr = $jobs->sum('pay_rate') ?? 0;
                $finalGp = $jobs->sum('final_gp') ?? 0;
                $gpPercent = $hc > 0 ? ($jobs->sum('percentage_gp') / $hc) : 0;

                $row = ['month' => $month, 'HC' => $hc, 'BR' => $br, 'PR' => $pr, 'Final_GP' => $finalGp, 'GP%' => $gpPercent];

                // Update Total row
                $totalRow['HC'] += $hc;
                $totalRow['BR'] += $br;
                $totalRow['PR'] += $pr;
                $totalRow['Final_GP'] += $finalGp;
                $totalRow['GP%'] += $hc > 0 ? $gpPercent * $hc : 0;
            }
            $terminationRows[] = $row;
        }

        // Finalize Total row GP%
        $totalRow['GP%'] = $totalRow['HC'] > 0 ? ($totalRow['GP%'] / $totalRow['HC']) : 0;
        $terminationRows[] = $totalRow;

        Log::info('Termination Rows Generated', ['terminationRows' => $terminationRows]);

        return $terminationRows;
    }

    private function getPoExpiryData($companyId, $selectedYear)
    {
        Log::info('Fetching PO Expiry Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $currentDate = Carbon::now();
        $poExpiryData = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Approved')
            ->whereHas('status', function ($q) {
                $q->where('status', 'Joined');
            })
            ->where('po_end_date', '<=', $currentDate)
            ->where('po_end_year', $selectedYear)
            ->get()
            ->groupBy(['po_end_year', 'client_id']);

        $poExpiryRows = [];
        $grandTotalRow = [
            'poEndYear' => 'Grand Total',
            'client' => '',
            'poEndMonth' => '',
            'HC' => 0,
            'BR' => 0,
            'PR' => 0,
            'Final_GP' => 0,
            'GP%' => 0,
        ];
        $grandTotalHc = 0;
        $grandTotalGpSum = 0;

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
                $clientName = $jobs->first()->client->client_name ?? 'Unknown';
                $hc = $jobs->count();
                $br = $jobs->sum('bill_rate') ?? 0;
                $pr = $jobs->sum('pay_rate') ?? 0;
                $finalGp = $jobs->sum('final_gp') ?? 0;
                $gpPercent = $hc > 0 ? ($jobs->sum('percentage_gp') / $hc) : 0;

                $row = [
                    'poEndYear' => '',
                    'client' => $clientName,
                    'poEndMonth' => $jobs->first()->po_end_month,
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

                // Update Grand Total
                $grandTotalRow['HC'] += $hc;
                $grandTotalRow['BR'] += $br;
                $grandTotalRow['PR'] += $pr;
                $grandTotalRow['Final_GP'] += $finalGp;
                $grandTotalGpSum += $gpPercent * $hc;
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
            $grandTotalHc += $yearlyHc;
        }

        // Add Grand Total row
        $grandTotalRow['GP%'] = $grandTotalHc > 0 ? ($grandTotalGpSum / $grandTotalHc) : 0;
        $poExpiryRows[] = $grandTotalRow;

        Log::info('PO Expiry Rows Generated', ['poExpiryRows' => $poExpiryRows]);

        return $poExpiryRows;
    }

    private function getContractData($companyId, $selectedYear)
    {
        Log::info('Fetching Contract Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $jobSeekersQuery = JobSeeker::where('company_id', $companyId)
            ->where('job_seeker_type', 'Temporary')
            ->where('form_status', 'Approved');

        $statusData = $jobSeekersQuery->get()->filter(function ($job) use ($selectedYear) {
            if ($job->status_id == StatusMaster::where('status', 'Joined')->value('id') && $job->join_date) {
                return Carbon::parse($job->join_date)->year == $selectedYear;
            }
            if ($job->status_id == StatusMaster::where('status', 'Offered')->value('id') && $job->offer_date) {
                return Carbon::parse($job->offer_date)->year == $selectedYear;
            }
            if ($job->status_id == StatusMaster::where('status', 'Selected')->value('id') && $job->selection_date) {
                return Carbon::parse($job->selection_date)->year == $selectedYear;
            }
            return false;
        })->groupBy('status_id');

        $joinedStatusId = StatusMaster::where('status', 'Joined')->value('id');
        $offeredStatusId = StatusMaster::where('status', 'Offered')->value('id');
        $selectedStatusId = StatusMaster::where('status', 'Selected')->value('id');

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

        // Calculate Grand Total
        $validStatusRows = array_filter($contractRows, fn($row) => !empty($row['status']) && $row['status'] !== 'Grand Total' && $row['HC'] > 0);
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

        Log::info('Contract Data Rows', ['rows' => $contractRows]);

        return $contractRows;
    }

    private function getFinanceData($companyId, $selectedYear)
    {
        Log::info('Fetching Finance Data for Company ID', ['company_id' => $companyId, 'year' => $selectedYear]);

        $joinedStatusId = StatusMaster::where('status', 'Joined')->value('id');

        if ($joinedStatusId) {
            $financeData = JobSeeker::where('company_id', $companyId)
                ->where('job_seeker_type', 'Permanent')
                ->where('form_status', 'Approved')
                ->where('status_id', $joinedStatusId)
                ->whereNotNull('invoice_no')
                ->whereNotNull('actual_billing_value')
                ->whereYear('join_date', $selectedYear)
                ->with('client')
                ->get()
                ->groupBy('client_id');

            $financeRows = [];
            foreach ($financeData as $clientId => $jobs) {
                $clientName = $jobs->first()->client->client_name ?? 'Unknown';
                $hc = $jobs->count();
                $invoiceNos = $jobs->pluck('invoice_no')->unique()->implode(', ');
                $totalBilling = $jobs->sum('actual_billing_value') ?? 0;

                $financeRows[] = [
                    'client' => $clientName,
                    'invoiceNo' => $invoiceNos,
                    'HC' => $hc,
                    'total' => $totalBilling,
                ];
            }

            // Sort by client name for consistent display
            usort($financeRows, fn($a, $b) => strcmp($a['client'], $b['client']));

            return $financeRows;
        }

        Log::warning('No Joined status found in status_master');
        return [];
    }
}