<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 24px; }

        .header { margin-bottom: 14px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { font-size: 19px; color: #2563eb; margin-bottom: 4px; }
        .header p  { font-size: 11px; color: #555; }

        h2.module {
            font-size: 13px;
            color: #fff;
            background-color: #2563eb;
            padding: 6px 9px;
            margin-top: 18px;
            margin-bottom: 7px;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        thead th {
            background-color: #dbeafe;
            color: #1e3a8a;
            padding: 6px 7px;
            text-align: left;
            font-size: 10.5px;
            font-weight: bold;
            border-bottom: 1px solid #93c5fd;
        }
        tbody td {
            padding: 6px 7px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10.5px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background-color: #f8fafc; }
        col.feature   { width: 20%; }
        col.condition { width: 38%; }
        col.outcome   { width: 42%; }

        .note { font-size: 10px; color: #6b7280; font-style: italic; margin-top: 2px; margin-bottom: 10px; }
        .footer { margin-top: 16px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HRMIS Functionality Matrix</h1>
        <p>Feature &rarr; Condition &rarr; Outcome reference for UNCST HRMIS &nbsp;&nbsp;|&nbsp;&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    @foreach ($modules as $module)
        <h2 class="module">{{ $module['title'] }}</h2>
        @if (!empty($module['note']))
            <p class="note">{{ $module['note'] }}</p>
        @endif
        <table>
            <colgroup>
                <col class="feature">
                <col class="condition">
                <col class="outcome">
            </colgroup>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Condition</th>
                    <th>Outcome</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($module['rows'] as $row)
                    <tr>
                        <td>{{ $row['feature'] }}</td>
                        <td>{{ $row['condition'] }}</td>
                        <td>{{ $row['outcome'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">UNCST HRMIS &mdash; Functionality Matrix</div>

</body>
</html>
