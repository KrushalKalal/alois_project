<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #F26522;
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .rejection-reason {
            font-weight: bold;
            color: #d32f2f;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>
                @if ($action === 'created')
                    New Job Seeker Created
                @elseif ($action === 'approved')
                    Job Seeker Approved
                @elseif ($action === 'rejected')
                    Job Seeker Rejected
                @else
                    Job Seeker Updated
                @endif
            </h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>
                A {{ $jobSeeker->job_seeker_type }} Job Seeker has been
                @if ($action === 'created')
                    created
                @elseif ($action === 'approved')
                    approved
                @elseif ($action === 'rejected')
                    rejected
                @else
                    updated
                @endif
                by {{ $user->name }} ({{ $user->employee->role ?? 'Admin' }}).
            </p>

            @if ($action === 'rejected' && $jobSeeker->reason_of_rejection)
                <p class="rejection-reason">Reason for Rejection: {{ $jobSeeker->reason_of_rejection }}</p>
            @endif

            @if ($action === 'updated' && !empty($changedFields))
                <h3>Changed Fields</h3>
                <table>
                    <tr>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                    </tr>
                    @foreach ($changedFields as $field => $values)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                            <td>{{ $values['old'] ?? 'N/A' }}</td>
                            <td>{{ $values['new'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <h3>Job Seeker Details</h3>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Consultant Name</td>
                    <td>{{ $jobSeeker->consultant_name }}</td>
                </tr>
                <tr>
                    <td>Job Seeker Type</td>
                    <td>{{ $jobSeeker->job_seeker_type }}</td>
                </tr>
                <tr>
                    <td>Company</td>
                    <td>{{ $jobSeeker->company ? $jobSeeker->company->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Jobseeker Status</td>
                    <td>{{ $jobSeeker->status ? $jobSeeker->status->status : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Application Status</td>
                    <td>{{ $jobSeeker->form_status }}</td>
                </tr>
                @if ($jobSeeker->join_date)
                    <tr>
                        <td>Join Date</td>
                        <td>{{ \Carbon\Carbon::parse($jobSeeker->join_date)->format('Y-m-d') }}</td>
                    </tr>
                @endif
            </table>
            <p>Please review the details in the application if needed.</p>
        </div>
        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
        </div>
    </div>
</body>

</html>