<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style type="text/css">
        /* Minimal CSS for email clients */
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px 4px;
            font-size: 11px;
            text-align: left;
        }
        th {
            background-color: #F26522;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .total-row {
            background-color: #D3D3D3;
            font-weight: bold;
        }
        .right-align {
            text-align: right;
        }
        .center-align {
            text-align: center;
        }
        .bold-status {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <p>Hi All,</p>
    <p>Please find attached Daily Hire Report for {{ $currentDate }}:</p>

    <!-- Main Layout: Two columns -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px;">
        <tr>
            <!-- First Column: Tables 1, 2, 3, and 4 stacked vertically -->
            <td width="50%" valign="top" style="padding-right: 10px; border: none;">
                <!-- Table 1: Perm Data -->
                <table border="1" cellpadding="0" cellspacing="0" width="100%" class="data-table" style="margin-bottom: 20px;">
                    <tr>
                        <th colspan="8" class="center-align">Perm Data {{ $year }} ({{ $data['currency'] }})</th>
                    </tr>
                    <tr>
                        <th style="width: 12%;">Months</th>
                        <th style="width: 12%;" class="center-align">Selected</th>
                        <th style="width: 12%;" class="center-align">Backout</th>
                        <th style="width: 12%;" class="center-align">Terminated</th>
                        <th style="width: 12%;" class="center-align">Offered</th>
                        <th style="width: 12%;" class="center-align">Joined</th>
                        <th style="width: 14%;" class="center-align">FTE&nbsp;Conversation&nbsp;Fees</th>
                        <th style="width: 14%;" class="center-align">Total</th>
                    </tr>
                    @if (isset($data['permData']) && is_array($data['permData']) && count($data['permData']) > 0)
                        @foreach ($data['permData'] as $row)
                            @php
        $rowClass = ($row['month'] === 'Total') ? 'total-row' : '';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td style="width: 12%;">{{ $row['month'] ?? '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['Selected']) && $row['Selected'] !== null ? number_format($row['Selected'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['Backout']) && $row['Backout'] !== null ? number_format($row['Backout'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['Terminated']) && $row['Terminated'] !== null ? number_format($row['Terminated'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['Offered']) && $row['Offered'] !== null ? number_format($row['Offered'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['Joined']) && $row['Joined'] !== null ? number_format($row['Joined'], 2) : '' }}</td>
                                <td style="width: 14%;" class="right-align">{{ isset($row['FTEConversionFees']) && $row['FTEConversionFees'] !== null ? number_format($row['FTEConversionFees'], 2) : '' }}</td>
                                <td style="width: 14%;" class="right-align">{{ isset($row['Total']) && $row['Total'] !== null ? number_format($row['Total'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="center-align" style="width: 100%;">No perm data available</td>
                        </tr>
                    @endif
                </table>

                <!-- Table 2: Backout Data -->
                <table border="1" cellpadding="0" cellspacing="0" width="100%" class="data-table" style="margin-bottom: 20px;">
                    <tr>
                        <th colspan="6" class="center-align">
                            {{ $region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly') }} Contracting Backouts
                            {{ $year }} ({{ $data['currency'] }})
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 16%;">Months</th>
                        <th style="width: 17%;" class="center-align">HC</th>
                        <th style="width: 17%;" class="center-align">BR</th>
                        <th style="width: 17%;" class="center-align">PR</th>
                        <th style="width: 16%;" class="center-align">Final_GP</th>
                        <th style="width: 17%;" class="center-align">GP %</th>
                    </tr>
                    @if (isset($data['backoutData']) && is_array($data['backoutData']) && count($data['backoutData']) > 0)
                        @foreach ($data['backoutData'] as $row)
                            @php
        $rowClass = ($row['month'] === 'Total') ? 'total-row' : '';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td style="width: 16%;">{{ $row['month'] ?? '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['HC']) && $row['HC'] !== null ? $row['HC'] : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['BR']) && $row['BR'] !== null ? number_format($row['BR'], 2) : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['PR']) && $row['PR'] !== null ? number_format($row['PR'], 2) : '' }}</td>
                                <td style="width: 16%;" class="right-align">{{ isset($row['Final_GP']) && $row['Final_GP'] !== null ? number_format($row['Final_GP'], 2) : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['GP%']) && $row['GP%'] !== null ? number_format($row['GP%'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="center-align" style="width: 100%;">No data available</td>
                        </tr>
                    @endif
                </table>

                <!-- Table 3: Termination Data -->
                <table border="1" cellpadding="0" cellspacing="0" width="100%" class="data-table" style="margin-bottom: 20px;">
                    <tr>
                        <th colspan="6" class="center-align">
                            {{ $region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly') }} Contracting
                            Termination {{ $year }} ({{ $data['currency'] }})
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 16%;">Months</th>
                        <th style="width: 17%;" class="center-align">HC</th>
                        <th style="width: 17%;" class="center-align">BR</th>
                        <th style="width: 17%;" class="center-align">PR</th>
                        <th style="width: 16%;" class="center-align">Final_GP</th>
                        <th style="width: 17%;" class="center-align">GP %</th>
                    </tr>
                    @if (isset($data['terminationData']) && is_array($data['terminationData']) && count($data['terminationData']) > 0)
                        @foreach ($data['terminationData'] as $row)
                            @php
        $rowClass = ($row['month'] === 'Total') ? 'total-row' : '';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td style="width: 16%;">{{ $row['month'] ?? '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['HC']) && $row['HC'] !== null ? $row['HC'] : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['BR']) && $row['BR'] !== null ? number_format($row['BR'], 2) : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['PR']) && $row['PR'] !== null ? number_format($row['PR'], 2) : '' }}</td>
                                <td style="width: 16%;" class="right-align">{{ isset($row['Final_GP']) && $row['Final_GP'] !== null ? number_format($row['Final_GP'], 2) : '' }}</td>
                                <td style="width: 17%;" class="right-align">{{ isset($row['GP%']) && $row['GP%'] !== null ? number_format($row['GP%'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="center-align" style="width: 100%;">No data available</td>
                        </tr>
                    @endif
                </table>

                <!-- Table 4: PO Expiry -->
                <table border="1" cellpadding="0" cellspacing="0" width="100%" class="data-table" style="margin-bottom: 20px;">
                    <tr>
                        <th colspan="8" class="center-align">PO Not Available/Expiry Summary {{ $year }} ({{ $data['currency'] }})</th>
                    </tr>
                    <tr>
                        <th style="width: 12%;">Po End Year</th>
                        <th style="width: 25%;">Client</th>
                        <th style="width: 12%;">Po End Month</th>
                        <th style="width: 12%;" class="center-align">HC</th>
                        <th style="width: 12%;" class="center-align">BR</th>
                        <th style="width: 12%;" class="center-align">PR</th>
                        <th style="width: 8%;" class="center-align">Final_GP</th>
                        <th style="width: 8%;" class="center-align">GP %</th>
                    </tr>
                    @if (isset($data['poExpiryData']) && is_array($data['poExpiryData']) && count($data['poExpiryData']) > 0)
                        @foreach ($data['poExpiryData'] as $row)
                            @php
        $rowClass = ($row['poEndYear'] === 'Total') ? 'total-row' : '';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td style="width: 12%;">{{ $row['poEndYear'] ?? '' }}</td>
                                <td style="width: 25%;">{{ $row['client'] ?? '' }}</td>
                                <td style="width: 12%;">{{ $row['poEndMonth'] ?? '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['HC']) && $row['HC'] !== null ? $row['HC'] : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['BR']) && $row['BR'] !== null ? number_format($row['BR'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['PR']) && $row['PR'] !== null ? number_format($row['PR'], 2) : '' }}</td>
                                <td style="width: 8%;" class="right-align">{{ isset($row['Final_GP']) && $row['Final_GP'] !== null ? number_format($row['Final_GP'], 2) : '' }}</td>
                                <td style="width: 8%;" class="right-align">{{ isset($row['GP%']) && $row['GP%'] !== null ? number_format($row['GP%'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="center-align" style="width: 100%;">No data available</td>
                        </tr>
                    @endif
                </table>
            </td>
            <!-- Second Column: Table 5 -->
            <td width="50%" valign="top" style="padding-left: 10px; border: none;">
                <!-- Table 5: Contracting JOS -->
                <table border="1" cellpadding="0" cellspacing="0" width="100%" class="data-table">
                    <tr>
                        <th colspan="7" class="center-align">
                            {{ $region === 'APAC' ? 'Daily' : ($region === 'EU-UK' ? 'Hourly' : 'Monthly') }} Contracting
                            (Joined/Offer/Selected) {{ $year }} ({{ $data['currency'] }})
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 14%;">Status</th>
                        <th style="width: 28%;">Client</th>
                        <th style="width: 12%;" class="center-align">HC</th>
                        <th style="width: 12%;" class="center-align">BR</th>
                        <th style="width: 12%;" class="center-align">PR</th>
                        <th style="width: 11%;" class="center-align">Final_GP</th>
                        <th style="width: 11%;" class="center-align">GP %</th>
                    </tr>
                    @if (isset($data['contractData']) && is_array($data['contractData']) && count($data['contractData']) > 0)
                        @foreach ($data['contractData'] as $row)
                            @php
        $rowClass = ($row['status'] === 'Grand Total') ? 'total-row' : '';
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td style="width: 14%;" @if (in_array($row['status'], ['Joined', 'Offered', 'Selected'])) class="bold-status" @endif>
                                    {{ $row['status'] ?? '' }}
                                </td>
                                <td style="width: 28%;">{{ $row['client'] ?? '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['HC']) && $row['HC'] !== null ? $row['HC'] : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['BR']) && $row['BR'] !== null ? number_format($row['BR'], 2) : '' }}</td>
                                <td style="width: 12%;" class="right-align">{{ isset($row['PR']) && $row['PR'] !== null ? number_format($row['PR'], 2) : '' }}</td>
                                <td style="width: 11%;" class="right-align">{{ isset($row['Final_GP']) && $row['Final_GP'] !== null ? number_format($row['Final_GP'], 2) : '' }}</td>
                                <td style="width: 11%;" class="right-align">{{ isset($row['GP%']) && $row['GP%'] !== null ? number_format($row['GP%'], 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="center-align" style="width: 100%;">No contract data available</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Remarks Sections -->
    <h2 style="font-size: 16px; margin-top: 20px; margin-bottom: 10px;">Contract:</h2>
    @if (!empty($tempRemarks))
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($tempRemarks as $remark)
                <li style="margin-bottom: 5px;">{{ $remark }}</li>
            @endforeach
        </ul>
    @else
        <p style="margin: 0;">No temporary job seeker remarks available</p>
    @endif

    <h2 style="font-size: 16px; margin-top: 20px; margin-bottom: 10px;">Perm:</h2>
    @if (!empty($permRemarks))
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($permRemarks as $remark)
                <li style="margin-bottom: 5px;">{{ $remark }}</li>
            @endforeach
    @else
        <p style="margin: 0;">No permanent job seeker remarks available</p>
    @endif

    <p style="margin-top: 20px;">Please find the attached Excel file for detailed data.</p>

</body>
</html>
