<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roll No. Slips</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body { background: #f0f2f5; font-family: Arial, sans-serif; }
        .slip-grid { width: 210mm; margin: 20px auto; display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .slip { width: calc(50% - 5px); border: 2px dashed #888; border-radius: 8px; padding: 14px; box-sizing: border-box; }
        .slip-header { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 6px; margin-bottom: 8px; }
        .slip-school { font-size: 14px; font-weight: bold; }
        .slip-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #666; background: #1e293b; color: white; display: inline-block; padding: 2px 10px; border-radius: 10px; margin-top: 4px; }
        .slip-row { font-size: 12px; margin: 4px 0; }
        .slip-roll { text-align: center; margin-top: 10px; padding: 6px; background: #eef2ff; border-radius: 6px; }
        .slip-roll .num { font-size: 22px; font-weight: bold; color: #1e293b; }
    </style>
</head>
<body>
    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div><strong>Roll No. Slips</strong> — {{ $students->count() }} slip(s)</div>
        <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
    </div>

    <div class="slip-grid">
        @forelse($students as $student)
            <div class="slip">
                <div class="slip-header">
                    <div class="slip-school">{{ $tenant->name ?? 'School' }}</div>
                    <div class="slip-title">Roll No. Slip</div>
                </div>
                <div class="slip-row"><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</div>
                <div class="slip-row"><strong>Father:</strong> {{ $student->father_name }}</div>
                <div class="slip-row"><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }} {{ $student->section->name ?? '' }}</div>
                <div class="slip-row"><strong>Admission No.:</strong> {{ $student->admission_no }}</div>
                <div class="slip-roll">
                    <div class="num">{{ $student->roll_no ?: '—' }}</div>
                    <div style="font-size: 10px; color: #666;">ROLL NUMBER</div>
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-muted">No students found for the selected filters.</div>
        @endforelse
    </div>
</body>
</html>
