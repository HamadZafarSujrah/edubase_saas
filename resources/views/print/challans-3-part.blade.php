<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Student Challans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { background: white !important; margin: 0; padding: 0; }
            .no-print { display: none; }
            .challan-page { page-break-after: always; height: 100vh; display: flex; align-items: stretch; }
            .challan-copy { width: 33.33%; border-right: 1px dashed #444; padding: 15px; overflow: hidden; height: 100%; position: relative; }
            .challan-copy:last-child { border-right: none; }
        }

        body { background: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; }
        .challan-page { background: white; width: 297mm; height: 210mm; margin: 20px auto; display: flex; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .challan-copy { width: 33.33%; border-right: 1px dashed #aaa; padding: 15px; position: relative; }
        .challan-copy:last-child { border-right: none; }

        .school-logo { height: 45px; margin-bottom: 10px; }
        .bank-name { font-size: 14px; font-weight: bold; color: #1a1a1a; margin-top: 5px; }
        .challan-title { background: #1a1a1a; color: white; text-align: center; padding: 3px; font-weight: bold; font-size: 9px; margin-bottom: 10px; }
        .copy-label { position: absolute; top: 10px; right: 10px; padding: 2px 8px; background: #eee; font-weight: bold; font-size: 10px; border-radius: 4px; border: 1px solid #ccc; text-transform: uppercase; }
        
        .info-table th { background: #fafafa; width: 30%; font-size: 10px; padding: 4px 5px !important; }
        .info-table td { font-size: 10px; padding: 4px 5px !important; }
        
        .fee-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .fee-table th { border-bottom: 2px solid #333; padding: 5px 0; font-size: 10px; text-transform: uppercase; }
        .fee-table td { padding: 5px 0; border-bottom: 1px solid #eee; font-size: 10px; }
        .total-row { border-top: 2px solid #333; font-weight: bold; background: #f9f9f9; }
        
        .footer-note { font-size: 9px; margin-top: 20px; color: #666; font-style: italic; }
        .signatures { margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-line { border-top: 1px solid #333; width: 45%; text-align: center; font-size: 9px; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="no-print p-4 bg-dark text-white sticky-top shadow-lg d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold"><i class="fas fa-print me-2"></i> Challan Print Preview</h5>
            <small class="opacity-75">Printing {{ $challans->count() }} bills in 3-part landscape format</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm"><i class="fas fa-print me-2"></i> PRINT NOW</button>
            <button onclick="window.close()" class="btn btn-outline-light px-4 rounded-pill">Close Preview</button>
        </div>
    </div>

    @foreach($challans as $challan)
    <div class="challan-page">
        @php $copies = ['Bank Copy', 'School Copy', 'Parent Copy']; @endphp
        
        @foreach($copies as $copy)
        <div class="challan-copy">
            <div class="copy-label">{{ $copy }}</div>
            
            <div class="text-center">
                @if($tenant && $tenant->logo)
                    <img src="{{ $tenant->logo }}" class="school-logo">
                @endif
                <h6 class="mb-0 fw-bold">{{ $tenant->name ?? 'EduBase School System' }}</h6>
                <div class="tiny text-muted">{{ $tenant->address ?? 'Main Campus, Pakistan' }}</div>
                <div class="bank-name">United Bank Limited (UBL)</div>
                <div class="small fw-bold">Account No: 1234-567890-123</div>
            </div>

            <div class="challan-title mt-3">{{ $challan->month }} {{ $challan->year }} FEE CHALLAN</div>

            <table class="table info-table table-bordered mb-0">
                <tr>
                    <th>Challan No</th>
                    <td class="fw-bold">{{ $challan->challan_no }}</td>
                </tr>
                <tr>
                    <th>Admission No</th>
                    <td>{{ $challan->student->admission_no }}</td>
                </tr>
                <tr>
                    <th>Student Name</th>
                    <td class="fw-bold">{{ strtoupper($challan->student->first_name) }} {{ strtoupper($challan->student->last_name) }}</td>
                </tr>
                <tr>
                    <th>Father Name</th>
                    <td>{{ strtoupper($challan->student->father_name) }}</td>
                </tr>
                <tr>
                    <th>Class/Sect</th>
                    <td>{{ $challan->student->schoolClass->name ?? 'N/A' }} - {{ $challan->student->section->name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Issue Date</th>
                    <td>{{ date('d-M-Y', strtotime($challan->issue_date)) }}</td>
                </tr>
                <tr>
                    <th>Due Date</th>
                    <td class="text-danger fw-bold">{{ date('d-M-Y', strtotime($challan->due_date)) }}</td>
                </tr>
            </table>

            <table class="fee-table">
                <thead>
                    <tr>
                        <th class="text-start">Description</th>
                        <th class="text-end">Amount (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($challan->items as $item)
                    <tr>
                        <td class="text-start">{{ $item->particular_name }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="text-start p-2">GRAND TOTAL</td>
                        <td class="text-end p-2">{{ number_format($challan->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="footer-note mt-3">
                <ul class="ps-3 mb-0">
                    <li>Fee must be paid by Due Date.</li>
                    <li>Payment after due date will attract late fee fine.</li>
                    <li>This challan is valid in all online branches.</li>
                </ul>
            </div>

            <div class="signatures">
                <div class="sig-line">Cashier / Bank</div>
                <div class="sig-line">Bank Stamp / Sign</div>
            </div>
            
            <div class="text-center mt-3 tiny fw-bold text-muted border-top pt-2">
                Powered by EduBase SaaS ERP
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
