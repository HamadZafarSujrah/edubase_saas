<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Sheet — {{ $exam->name }} — {{ $exam->schoolClass->name ?? '' }}</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body { background: #f0f2f5; font-family: Arial, sans-serif; }
        .sheet-page { background: white; width: 210mm; min-height: 148mm; margin: 20px auto; padding: 30px 40px; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1a1a1a; padding-bottom: 10px; }
        .school-name { font-size: 20px; font-weight: bold; }
        .title { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; color: #555; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; font-size: 13px; }
        th { background: #1e293b; color: white; font-size: 12px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div><strong>Date Sheet</strong> — {{ $exam->name }} — {{ $exam->schoolClass->name ?? '' }}</div>
        <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
    </div>

    <div class="sheet-page">
        <div class="header">
            <div class="school-name">{{ $tenant->name ?? 'School' }}</div>
            <div class="title">Date Sheet — {{ $exam->name }} ({{ ucfirst($exam->type) }}) — {{ $exam->schoolClass->name ?? '' }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                    <tr>
                        <td>{{ $s->subject->name ?? '—' }}</td>
                        <td>{{ $s->date?->format('d-M-Y') ?? '—' }}</td>
                        <td>
                            @if($s->start_time && $s->end_time)
                                {{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $s->room ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No date sheet entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
