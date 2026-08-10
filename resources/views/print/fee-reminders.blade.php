<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Reminder Notices</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
            .reminder-page { page-break-after: always; }
        }

        body { background: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; }
        .reminder-page { background: white; width: 210mm; min-height: 148mm; margin: 20px auto; padding: 25px 35px; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { text-align: center; border-bottom: 2px solid #1a1a1a; padding-bottom: 10px; margin-bottom: 15px; }
        .school-name { font-size: 20px; font-weight: bold; }
        .notice-title { background: #b91c1c; color: white; text-align: center; padding: 6px; font-weight: bold; margin: 15px 0; letter-spacing: 1px; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; font-size: 13px; }
        .fee-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .fee-table th { background: #f1f5f9; text-align: left; padding: 6px 8px; font-size: 12px; border-bottom: 2px solid #333; }
        .fee-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 12px; }
        .total-row { font-weight: bold; background: #fef2f2; }
        .footer-note { margin-top: 25px; font-size: 12px; color: #444; }
        .signature { margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-line { border-top: 1px solid #333; width: 40%; text-align: center; font-size: 11px; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="no-print p-4 bg-dark text-white sticky-top shadow-lg d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Fee Reminder Notices</h5>
            <small class="opacity-75">{{ $students->count() }} notice(s)</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print Now</button>
            <button onclick="window.close()" style="padding:10px 24px; border-radius:30px; background:transparent; color:white; border:1px solid white;">Close</button>
        </div>
    </div>

    @forelse($students as $student)
        <div class="reminder-page">
            <div class="header">
                <div class="school-name">{{ $tenant->name ?? 'School' }}</div>
                <div>{{ $student->campus->name ?? '' }}</div>
            </div>

            <div class="notice-title">FEE PAYMENT REMINDER NOTICE</div>

            <table class="info-table">
                <tr>
                    <td width="50%"><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</td>
                    <td><strong>Admission No:</strong> {{ $student->admission_no }}</td>
                </tr>
                <tr>
                    <td><strong>Father Name:</strong> {{ $student->father_name }}</td>
                    <td><strong>Class / Section:</strong> {{ $student->schoolClass->name ?? '—' }} {{ $student->section->name ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong> {{ now()->format('d-M-Y') }}</td>
                    <td></td>
                </tr>
            </table>

            <p>Dear Guardian,<br>This is a reminder that the following fee amount(s) remain outstanding. Kindly clear the dues at your earliest convenience to avoid any inconvenience.</p>

            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Challan No.</th>
                        <th>Month / Year</th>
                        <th>Due Date</th>
                        <th style="text-align:right;">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->challans as $challan)
                        <tr>
                            <td>{{ $challan->challan_no }}</td>
                            <td class="text-capitalize">{{ $challan->month }} {{ $challan->year }}</td>
                            <td>{{ $challan->due_date ? \Carbon\Carbon::parse($challan->due_date)->format('d-M-Y') : '—' }}</td>
                            <td style="text-align:right;">{{ number_format((float) $challan->total_amount - (float) $challan->paid_amount - (float) $challan->discount_amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right;">Total Outstanding:</td>
                        <td style="text-align:right;">{{ number_format($student->outstanding_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer-note">Please contact the school office for any queries regarding this notice.</div>

            <div class="signature">
                <div class="sig-line">Parent / Guardian Signature</div>
                <div class="sig-line">School Office Stamp</div>
            </div>
        </div>
    @empty
        <div class="reminder-page text-center py-5">No students selected.</div>
    @endforelse

</body>
</html>
