<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Sheet — {{ $monthName }}</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body { background: #f0f2f5; font-family: Arial, sans-serif; font-size: 10px; }
        .sheet-page { background: white; width: 297mm; min-height: 210mm; margin: 20px auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { text-align: center; margin-bottom: 12px; }
        .school-name { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 3px; text-align: center; }
        th { background: #1e293b; color: white; font-size: 9px; }
        td.name-col, th.name-col { text-align: left; padding-left: 6px; width: 160px; }
        .legend { margin-top: 15px; font-size: 10px; }
        .legend span { margin-right: 15px; }
    </style>
</head>
<body>
    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div><strong>Employee Attendance Sheet</strong> — {{ $department->name ?? 'All Departments' }} — {{ $monthName }}</div>
        <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
    </div>

    <div class="sheet-page">
        <div class="header">
            <div class="school-name">{{ $tenant->name ?? 'School' }}</div>
            <div>Monthly Employee Attendance Sheet — {{ $department->name ?? 'All Departments' }} — {{ $monthName }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="name-col">Employee Name</th>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        <th>{{ $d }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $e)
                    <tr>
                        <td class="name-col">{{ $e->first_name }} {{ $e->last_name }} <span style="color:#888;">({{ $e->emp_no }})</span></td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <td>{{ $grid[$e->id][$d] ?? '' }}</td>
                        @endfor
                    </tr>
                @empty
                    <tr><td colspan="{{ $daysInMonth + 1 }}">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="legend">
            <span><strong>P</strong> = Present</span>
            <span><strong>A</strong> = Absent</span>
            <span><strong>L</strong> = Leave</span>
            <span><strong>T</strong> = Late</span>
            <span>(blank = not marked)</span>
        </div>
    </div>
</body>
</html>
