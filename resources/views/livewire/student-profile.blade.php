<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Print Controls -->
    <div class="d-print-none mb-4 d-flex justify-content-between">
        <a href="/manage-students" class="btn btn-outline-primary rounded-pill shadow-sm"><i class="fas fa-arrow-left me-2"></i> Return to Directory</a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill shadow px-4 fw-bold"><i class="fas fa-print me-2"></i> Print Admission Form</button>
    </div>

    <!-- MAIN FORM CONTAINER -->
    <div class="card shadow border-0 mx-auto" style="max-width: 1000px; padding: 40px; background: white;">
        
        <!-- HEADER (Dynamic Branding from Tenant) -->
        <div class="text-center mb-4 border-bottom pb-4">
            <div class="d-flex justify-content-between align-items-center">
                @if($tenant && $tenant->logo_url)
                    <img src="{{ asset('storage/' . $tenant->logo_url) }}" style="width: 80px; height: 80px; object-fit: contain;">
                @else
                    <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <span class="tiny text-muted">LOGO</span>
                    </div>
                @endif
                
                <div class="flex-grow-1 px-3">
                    <h1 class="h3 fw-bold mb-1 text-uppercase text-primary" style="letter-spacing: 2px;">
                        {{ $tenant->name ?? 'ALHIKMAH HIGHER SECONDARY SCHOOL' }}
                    </h1>
                    <p class="mb-0 small fw-bold text-muted">{{ $tenant->address ?? 'Main Campus: Doaba, Mianwali' }}</p>
                    <p class="mb-0 tiny text-muted">
                        {{ $tenant->phone ?? '+92 345 1231393' }} | {{ $tenant->email ?? 'info@edubase.com' }}
                    </p>
                </div>
                <!-- Student Image -->
                <div class="border rounded shadow-sm p-1" style="width: 110px; height: 110px;">
                    @if($student->student_image)
                        <img src="{{ asset('storage/' . $student->student_image) }}" class="w-100 h-100 object-fit-cover rounded">
                    @else
                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted tiny">No Photo</div>
                    @endif
                </div>
            </div>
            <div class="mt-3 py-1 bg-primary bg-opacity-10 rounded">
                <h5 class="mb-0 fw-bold small text-uppercase">Student Admission Details</h5>
            </div>
        </div>

        <!-- FORM CONTENT -->
        <div class="row g-3">
            <!-- Left Column -->
            <div class="col-8">
                <div class="row g-2">
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Student Name:</span>
                        <span class="small fw-bold">{{ $student->first_name }} {{ $student->last_name }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Campus:</span>
                        <span class="small fw-bold">{{ $student->campus->name ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Registration No:</span>
                        <span class="small fw-bold">{{ $student->id }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Roll No:</span>
                        <span class="small fw-bold">{{ $student->roll_no ?? 'Pending' }}</span>
                    </div>

                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Admission Date:</span>
                        <span class="small fw-bold">{{ $student->admission_date ? $student->admission_date->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Class:</span>
                        <span class="small fw-bold">{{ $student->schoolClass->name ?? 'N/A' }} ({{ $student->section->name ?? 'N/A' }})</span>
                    </div>

                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Date of Birth:</span>
                        <span class="small fw-bold">{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Gender:</span>
                        <span class="small fw-bold">{{ $student->gender }}</span>
                    </div>

                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Student CNIC/B-Form:</span>
                        <span class="small fw-bold">{{ $student->cnic_no ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Caste:</span>
                        <span class="small fw-bold">{{ $student->caste ?? 'N/A' }}</span>
                    </div>

                    <div class="col-12 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Father Name:</span>
                        <span class="small fw-bold">{{ $student->father_name }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Father Contact:</span>
                        <span class="small fw-bold">{{ $student->father_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">WhatsApp No:</span>
                        <span class="small fw-bold text-success">{{ $student->whatsapp_no ?? 'N/A' }}</span>
                    </div>

                    <div class="col-12 border-bottom py-1">
                        <span class="tiny fw-bold text-muted d-block">Current Address:</span>
                        <span class="small fw-bold">{{ $student->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column Meta -->
            <div class="col-4 ps-4 border-start">
                <div class="bg-light p-3 rounded h-100">
                    <h6 class="tiny fw-bold border-bottom pb-2 mb-3 text-uppercase">Emergency Info</h6>
                    <div class="mb-3">
                        <span class="tiny text-muted d-block">Guardian Name:</span>
                        <span class="small fw-bold">{{ $student->guardian_name ?? 'Father' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="tiny text-muted d-block">Guardian Relation:</span>
                        <span class="small fw-bold">{{ $student->guardian_relation ?? 'Father' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="tiny text-muted d-block">Guardian Contact:</span>
                        <span class="small fw-bold">{{ $student->guardian_phone ?? $student->father_phone }}</span>
                    </div>
                    <div class="mt-auto pt-4 text-center">
                         <div class="mt-4 pt-4 border-top">
                            <i class="fas fa-qrcode fs-1 text-muted opacity-25"></i>
                            <p class="tiny text-muted mt-2">Digital Verify</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DECIDED FEE SECTION (Matches Screenshot Bottom) -->
        <div class="mt-5">
            <h6 class="fw-bold border-bottom pb-2 mb-3 bg-light p-2"><i class="fas fa-money-bill-wave me-2"></i> Decided Monthly Fee Structure</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="bg-light tiny">
                        <tr>
                            <th>Fee Particular</th>
                            <th class="text-end">Monthly Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fee_items as $item)
                            <tr>
                                <td class="small">{{ $item->particular->name ?? 'Unknown' }}</td>
                                <td class="small fw-bold text-end">{{ number_format($item->actual_amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center tiny text-muted">No fee plan initialized</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th class="small py-2">TOTAL MONTHLY FEE</th>
                            <th class="small py-2 text-end text-primary">{{ number_format($fee_items->sum('actual_amount')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- SIGNATURES (Matches Screenshot) -->
        <div class="mt-5 pt-5">
            <div class="row text-center mt-5">
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 tiny fw-bold">PARENT'S SIGNATURE</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 tiny fw-bold">PRINCIPAL'S SIGNATURE</div>
                </div>
                <div class="col-4">
                    <div class="border-top pt-2 mx-4 tiny fw-bold">ADMIN / OFFICE SIGNATURE</div>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="mt-5 pt-4 text-muted border-top border-dotted" style="font-size: 0.6rem;">
            <div class="d-flex justify-content-between">
                <span>Created At: {{ $student->created_at }}</span>
                <span>System Verified: EDUBase v8.0</span>
            </div>
        </div>
    </div>

<style>
    @media print {
        body { background: white !important; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
        .bg-primary { background-color: #0d6efd !important; -webkit-print-color-adjust: exact; }
        .container-fluid { padding: 0 !important; }
        .card { shadow: none !important; border: 0 !important; max-width: 100% !important; }
    }
    .tiny { font-size: 0.7rem; }
</style>
</div>

