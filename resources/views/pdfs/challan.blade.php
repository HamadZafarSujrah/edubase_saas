<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 10px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .challan-wrapper { width: 100%; display: table; table-layout: fixed; border-collapse: separate; border-spacing: 15px 0; }
        .challan-column { display: table-cell; vertical-align: top; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #4e73df; padding-bottom: 10px; margin-bottom: 10px; }
        .school-name { font-size: 14px; font-weight: bold; color: #224abe; margin: 0; text-transform: uppercase; }
        .challan-type { background: #f8f9fa; padding: 3px 10px; border-radius: 20px; font-weight: bold; display: inline-block; margin-top: 5px; color: #666; }
        .info-grid { width: 100%; margin: 10px 0; border-collapse: collapse; }
        .info-grid td { padding: 4px 0; border-bottom: 1px dashed #eee; }
        .label { font-weight: bold; width: 40%; }
        .fee-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .fee-table th { background: #4e73df; color: #white; text-align: left; padding: 5px 8px; font-size: 10px; }
        .fee-table td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .total-row { background: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #777; }
        .signatures { margin-top: 40px; display: table; width: 100%; }
        .sig-box { display: table-cell; text-align: center; border-top: 1px solid #333; width: 45%; padding-top: 5px; }
        .watermark { position: absolute; opacity: 0.05; transform: rotate(-45deg); font-size: 50px; font-weight: bold; color: #000; z-index: -1; top: 150px; left: 30px; }
    </style>
</head>
<body>
    <div class="challan-wrapper">
        @foreach(['BANK COPY', 'SCHOOL COPY', 'PARENT COPY'] as $copyType)
        <div class="challan-column">
            <div class="watermark">{{ $copyType }}</div>
            <div class="header">
                <div class="school-name">Al-hikma School System</div>
                <div class="small">Knowledge is Light</div>
                <div class="challan-type">{{ $copyType }}</div>
            </div>

            <table class="info-grid">
                <tr><td class="label">Bill No:</td><td>CHL-2026-904</td></tr>
                <tr><td class="label">Issue Date:</td><td>19-Apr-2026</td></tr>
                <tr><td class="label">Due Date:</td><td><strong>10-May-2026</strong></td></tr>
            </table>

            <div style="background: #eef2ff; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <div class="fw-bold" style="font-size: 12px;">Student Name: <strong>Ahmed Hassan</strong></div>
                <div class="small text-muted">Class: Grade 8-A | G.R No: 4521</div>
            </div>

            <table class="fee-table">
                <thead style="background: #4e73df; color: white;">
                    <tr><th>Particulars</th><th style="text-align: right;">Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>Monthly Tuition Fee</td><td style="text-align: right;">4,500</td></tr>
                    <tr><td>Computer Lab Fee</td><td style="text-align: right;">500</td></tr>
                    <tr><td>Library Fund</td><td style="text-align: right;">200</td></tr>
                    <tr><td>Arrears/Previous</td><td style="text-align: right;">0.00</td></tr>
                    <tr class="total-row">
                        <td>TOTAL PAYABLE</td>
                        <td style="text-align: right; color: #224abe; border-top: 2px solid #4e73df;">PKR 5,200</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>Note: Valid at all branches of Muslim Commercial Bank. Late fee surcharge of PKR 200 applicable after due date.</p>
            </div>

            <div class="signatures">
                <div class="sig-box">Depositor</div>
                <div style="display: table-cell; width: 10%;"></div>
                <div class="sig-box">Bank / Cashier</div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
