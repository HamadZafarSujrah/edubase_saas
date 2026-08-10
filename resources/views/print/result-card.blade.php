<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Card — {{ $student->first_name }} {{ $student->last_name }} — {{ $exam->name }}</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body { background: #f0f2f5; font-family: Arial, sans-serif; }
        .card-page { background: white; width: 210mm; min-height: 297mm; margin: 20px auto; padding: 40px 50px; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a1a1a; padding-bottom: 15px; }
        .school-name { font-size: 24px; font-weight: bold; }
        .title { font-size: 15px; text-transform: uppercase; letter-spacing: 2px; color: #555; margin-top: 6px; }
        .meta-grid { display: flex; justify-content: space-between; margin: 20px 0; font-size: 13px; }
        .meta-grid div { line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #1e293b; color: white; font-size: 12px; text-transform: uppercase; }
        .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
        .result-summary { text-align: center; margin-top: 25px; padding: 15px; background: #e8f5e9; border-radius: 8px; }
        .result-summary .label { font-size: 13px; color: #555; }
        .result-summary .value { font-size: 26px; font-weight: bold; color: #198754; }
    </style>
</head>
<body>
    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div><strong>Result Card</strong> — {{ $student->first_name }} {{ $student->last_name }} — {{ $exam->name }}</div>
        <div>
            <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
            <button onclick="window.close()" style="padding:10px 24px; border-radius:30px; background:transparent; color:white; border:1px solid white; margin-left:8px;">Close</button>
        </div>
    </div>

    <div class="card-page">
        <div class="header">
            <div class="school-name">{{ $tenant->name ?? 'School' }}</div>
            <div class="title">Result Card — {{ $exam->name }}</div>
        </div>

        <div class="meta-grid">
            <div>
                <div><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</div>
                <div><strong>Admission No.:</strong> {{ $student->admission_no }}</div>
                <div><strong>Father's Name:</strong> {{ $student->father_name }}</div>
            </div>
            <div>
                <div><strong>Class:</strong> {{ $exam->schoolClass->name ?? '—' }}</div>
                <div><strong>Exam:</strong> {{ $exam->name }} ({{ ucfirst($exam->type) }})</div>
                @if($exam->exam_date)<div><strong>Date:</strong> {{ $exam->exam_date->format('d-M-Y') }}</div>@endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th class="amount">Marks Obtained</th>
                    <th class="amount">Total Marks</th>
                    <th class="amount">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marks as $mark)
                    <tr>
                        <td>{{ $mark->subject->name ?? '—' }}</td>
                        <td class="amount">{{ $mark->marks_obtained ?? '—' }}</td>
                        <td class="amount">{{ $mark->marks_total }}</td>
                        <td class="amount">{{ $mark->grade ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="amount">No marks recorded yet.</td></tr>
                @endforelse
                <tr class="total-row">
                    <td>Total</td>
                    <td class="amount">{{ $totalObtained }}</td>
                    <td class="amount">{{ $totalMarks }}</td>
                    <td class="amount">{{ $overallGrade ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="result-summary">
            <div class="label">Overall Percentage</div>
            <div class="value">{{ $percentage }}% @if($overallGrade) ({{ $overallGrade }}) @endif</div>
        </div>
    </div>
</body>
</html>
