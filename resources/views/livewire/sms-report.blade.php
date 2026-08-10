<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-comment-dots me-2 text-primary"></i> SMS Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">SMS Report</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">From</label>
                    <input type="date" wire:model.live="start_date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">To</label>
                    <input type="date" wire:model.live="end_date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Type</label>
                    <select wire:model.live="type" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Types</option>
                        <option value="fee">Fee</option>
                        <option value="attendance">Attendance</option>
                        <option value="result">Result</option>
                        <option value="blast">Blast</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select wire:model.live="status" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Message Log</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $logs->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3" width="130">Sent At</th>
                        <th width="160">Recipient</th>
                        <th width="120">Phone</th>
                        <th>Message</th>
                        <th class="text-center" width="100">Type</th>
                        <th class="text-center" width="90">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 text-muted">{{ $log->sent_at?->format('d-M-Y h:i A') ?? '—' }}</td>
                            <td class="fw-bold">
                                {{ $log->recipient_name ?? ($log->student->full_name ?? '—') }}
                                @if($log->student)
                                    <span class="text-muted small d-block">{{ $log->student->admission_no }}</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $log->phone }}</td>
                            <td class="text-truncate" style="max-width: 320px;" title="{{ $log->message }}">{{ $log->message }}</td>
                            <td class="text-center"><span class="badge bg-secondary text-uppercase">{{ $log->type }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $log->status === 'sent' ? 'bg-success' : ($log->status === 'failed' ? 'bg-danger' : 'bg-warning') }} text-uppercase">{{ $log->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-comment-slash fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No SMS logs found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
