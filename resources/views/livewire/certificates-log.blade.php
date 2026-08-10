<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-clipboard-list me-2 text-primary"></i> Certificates Log</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Certificates Log</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('certificates.issue') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-plus me-2"></i> Issue New Certificate
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">Search Student</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name or admission no...">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Certificate Type</label>
                    <select wire:model.live="certificate_type" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Types</option>
                        <option value="character">Character Certificate</option>
                        <option value="bonafide">Bonafide Certificate</option>
                        <option value="leaving">School Leaving Certificate</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Issued Certificates</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $certificates->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3" width="110">Date Issued</th>
                        <th>Student</th>
                        <th width="180">Certificate Type</th>
                        <th width="130">Issued By</th>
                        <th class="text-center pe-3" width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                        <tr>
                            <td class="ps-3">{{ $cert->issued_date->format('d-M-Y') }}</td>
                            <td class="fw-bold">{{ $cert->student->first_name ?? '—' }} {{ $cert->student->last_name ?? '' }} <span class="text-muted">({{ $cert->student->admission_no ?? '—' }})</span></td>
                            <td>{{ $cert->title }}</td>
                            <td class="text-muted">{{ $cert->issuedBy->name ?? '—' }}</td>
                            <td class="text-center pe-3">
                                <a href="{{ route('print-certificate', ['id' => $cert->id]) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-print me-1"></i> Reprint
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-clipboard-list fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No certificates issued yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
