<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $certificate->title }} — {{ $certificate->student->first_name }}</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }

        body { background: #f0f2f5; font-family: 'Georgia', 'Times New Roman', serif; }
        .cert-page {
            background: white; width: 210mm; min-height: 297mm; margin: 20px auto; padding: 50px 60px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            border: 10px double #1a1a1a;
        }
        .cert-header { text-align: center; margin-bottom: 30px; }
        .school-name { font-size: 30px; font-weight: bold; letter-spacing: 1px; }
        .school-sub { font-size: 13px; color: #555; margin-top: 4px; }
        .cert-title {
            text-align: center; font-size: 26px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 3px; margin: 40px 0; border-bottom: 2px solid #1a1a1a; border-top: 2px solid #1a1a1a; padding: 12px 0;
        }
        .cert-body { font-size: 16px; line-height: 2; text-align: justify; margin: 30px 10px; }
        .cert-meta { display: flex; justify-content: space-between; margin-top: 60px; font-size: 13px; }
        .signature { text-align: center; }
        .sig-line { border-top: 1px solid #333; width: 180px; margin: 0 auto; padding-top: 6px; }
    </style>
</head>
<body>

    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <strong>{{ $certificate->title }}</strong> — {{ $certificate->student->first_name }} {{ $certificate->student->last_name }}
        </div>
        <div>
            <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
            <a href="{{ route('download-certificate', ['id' => $certificate->id]) }}" style="padding:10px 24px; border-radius:30px; background:#198754; color:white; text-decoration:none; font-weight:bold; margin-left:8px;">Download PDF</a>
            <button onclick="window.close()" style="padding:10px 24px; border-radius:30px; background:transparent; color:white; border:1px solid white; margin-left:8px;">Close</button>
        </div>
    </div>

    <div class="cert-page">
        <div class="cert-header">
            <div class="school-name">{{ $tenant->name ?? 'School Name' }}</div>
            <div class="school-sub">{{ $certificate->student->campus->name ?? '' }}</div>
        </div>

        <div class="cert-title">{{ $certificate->title }}</div>

        <div class="cert-body">
            {{ $certificate->body_text }}
        </div>

        <div class="cert-meta">
            <div>Issued on: {{ $certificate->issued_date->format('d-M-Y') }}</div>
            <div class="signature">
                <div class="sig-line">Principal / Head of Institution</div>
            </div>
        </div>
    </div>

</body>
</html>
