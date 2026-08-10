<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-times-circle me-2 text-danger"></i> Voided Transactions</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Voided Transactions</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Search Student / Bill #</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name or Bill #...">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">From</label>
                    <input type="date" wire:model.live="from_date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">To</label>
                    <input type="date" wire:model.live="to_date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-2">
                    <div class="text-muted small fw-bold">Total Voided</div>
                    <div class="fs-5 fw-bold text-danger">{{ number_format($totalVoided, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Bill #</th>
                            <th>Student</th>
                            <th class="text-end">Voided Amount</th>
                            <th>Reason</th>
                            <th>Voided By</th>
                            <th>Voided At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $log->challan_no }}</td>
                            <td>{{ $log->student_name }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($log->voided_amount, 2) }}</td>
                            <td class="text-muted">{{ $log->void_reason ?: '—' }}</td>
                            <td>{{ $log->voidedBy->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $log->created_at->format('d-M-Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-times-circle mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Voided Transactions Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $logs->links() }}
        </div>
    </div>
</div>
