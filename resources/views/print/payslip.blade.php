<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip — {{ $payment->employee->first_name }} {{ $payment->employee->last_name }} — {{ \Carbon\Carbon::createFromDate($payment->year, $payment->month, 1)->format('F Y') }}</title>
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body { background: #f0f2f5; font-family: Arial, sans-serif; }
        .payslip-page { background: white; width: 210mm; min-height: 297mm; margin: 20px auto; padding: 40px 50px; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a1a1a; padding-bottom: 15px; }
        .school-name { font-size: 24px; font-weight: bold; }
        .payslip-title { font-size: 15px; text-transform: uppercase; letter-spacing: 2px; color: #555; margin-top: 6px; }
        .meta-grid { display: flex; justify-content: space-between; margin: 20px 0; font-size: 13px; }
        .meta-grid div { line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: {{ $template['header_color'] }}; color: white; font-size: 12px; text-transform: uppercase; }
        .header { border-bottom-color: {{ $template['header_color'] }} !important; }
        .school-logo { max-height: 60px; margin-bottom: 8px; }
        .footer-note { text-align: center; margin-top: 20px; font-size: 11px; color: #888; }
        .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
        .net-pay { text-align: center; margin-top: 25px; padding: 15px; background: #e8f5e9; border-radius: 8px; }
        .net-pay .label { font-size: 13px; color: #555; }
        .net-pay .value { font-size: 26px; font-weight: bold; color: #198754; }
        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="no-print" style="padding:16px; background:#1a1a1a; color:white; display:flex; justify-content:space-between; align-items:center;">
        <div><strong>Payslip</strong> — {{ $payment->employee->first_name }} {{ $payment->employee->last_name }} — {{ \Carbon\Carbon::createFromDate($payment->year, $payment->month, 1)->format('F Y') }}</div>
        <div>
            <button onclick="window.print()" style="padding:10px 24px; border-radius:30px; background:#0d6efd; color:white; border:none; font-weight:bold;">Print</button>
            <button onclick="window.close()" style="padding:10px 24px; border-radius:30px; background:transparent; color:white; border:1px solid white; margin-left:8px;">Close</button>
        </div>
    </div>

    <div class="payslip-page">
        <div class="header">
            @if($template['show_logo'] && $tenant?->logo_url)
                <img src="{{ $tenant->logo_url }}" class="school-logo">
            @endif
            <div class="school-name">{{ $tenant->name ?? 'School' }}</div>
            <div class="payslip-title">Salary Slip — {{ \Carbon\Carbon::createFromDate($payment->year, $payment->month, 1)->format('F Y') }}</div>
        </div>

        <div class="meta-grid">
            <div>
                <div><strong>Employee:</strong> {{ $payment->employee->first_name }} {{ $payment->employee->last_name }}</div>
                <div><strong>Employee No.:</strong> {{ $payment->employee->emp_no }}</div>
                <div><strong>Department:</strong> {{ $payment->employee->department->name ?? '—' }}</div>
                <div><strong>Designation:</strong> {{ $payment->employee->designation->name ?? '—' }}</div>
            </div>
            <div>
                <div><strong>Pay Period:</strong> {{ \Carbon\Carbon::createFromDate($payment->year, $payment->month, 1)->format('F Y') }}</div>
                <div><strong>Status:</strong> <span class="status-badge" style="background: {{ $payment->status === 'paid' ? '#198754' : '#ffc107' }}; color: white;">{{ $payment->status }}</span></div>
                @if($payment->paid_at)
                    <div><strong>Paid On:</strong> {{ $payment->paid_at->format('d-M-Y') }}</div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="amount">{{ number_format($payment->basic_salary, 2) }}</td>
                </tr>
                @foreach($payment->salaryPlan->allowances->where('type', 'allowance') as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="amount">{{ number_format($a->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Gross Pay</td>
                    <td class="amount">{{ number_format($payment->gross_amount, 2) }}</td>
                </tr>
                @foreach($payment->salaryPlan->allowances->where('type', 'deduction') as $d)
                    <tr>
                        <td>{{ $d->name }}</td>
                        <td class="amount">-{{ number_format($d->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Deductions</td>
                    <td class="amount">-{{ number_format($payment->deductions_amount, 2) }}</td>
                </tr>
                @if($payment->advance_deducted > 0)
                    <tr>
                        <td>Salary Advance Recovery</td>
                        <td class="amount">-{{ number_format($payment->advance_deducted, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="net-pay">
            <div class="label">Net Pay</div>
            <div class="value">{{ number_format($payment->net_amount, 2) }}</div>
        </div>

        @if($template['footer_text'])
            <div class="footer-note">{{ $template['footer_text'] }}</div>
        @endif
    </div>
</body>
</html>
