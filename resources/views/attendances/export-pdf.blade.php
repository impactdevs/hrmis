<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; padding: 20px; }

        .header { margin-bottom: 16px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { font-size: 16px; color: #2563eb; margin-bottom: 4px; }
        .header p  { font-size: 9px; color: #555; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th {
            background-color: #2563eb;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tbody tr:nth-child(even) td { background-color: #f8fafc; }
        .footer { margin-top: 14px; font-size: 8px; color: #999; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Attendance Report</h1>
        <p>
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
            &ndash;
            {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            {{ $totalRecords }} record(s)
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Generated: {{ now()->format('d M Y, H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Hours Worked</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($processedRows as $row)
                <tr>
                    <td>{{ $row['staff_id'] }}</td>
                    <td>{{ $row['first_name'] }}</td>
                    <td>{{ $row['last_name'] }}</td>
                    <td>{{ $row['department'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['clock_in'] }}</td>
                    <td>{{ $row['clock_out'] }}</td>
                    <td>{{ $row['hours_worked'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#999; padding:12px;">
                        No attendance records found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Exported from HR Attendance System</div>

</body>
</html>