<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-check me-2 text-primary"></i> Leave Approvals</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Leave Approvals</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('hrm.leave-request') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-plus me-2"></i> Request Leave
        </a>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">Search Employee</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name or employee no....">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Leave Requests</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $requests->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Employee</th>
                        <th width="100">Type</th>
                        <th width="200">Dates</th>
                        <th>Reason</th>
                        <th class="text-center" width="100">Status</th>
                        <th width="150">Approved By</th>
                        <th class="text-center pe-3" width="160">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $req->employee->first_name ?? '—' }} {{ $req->employee->last_name ?? '' }} <span class="text-muted small">({{ $req->employee->emp_no ?? '—' }})</span></td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $req->type }}</span></td>
                            <td class="text-muted">{{ $req->from_date->format('d-M-Y') }} — {{ $req->to_date->format('d-M-Y') }}</td>
                            <td class="text-truncate" style="max-width: 220px;" title="{{ $req->reason }}">{{ $req->reason ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $req->status === 'approved' ? 'bg-success' : ($req->status === 'rejected' ? 'bg-danger' : 'bg-warning') }} text-uppercase">{{ $req->status }}</span>
                            </td>
                            <td class="text-muted">{{ $req->approvedBy->name ?? '—' }}</td>
                            <td class="text-center pe-3">
                                @if($req->status === 'pending')
                                    <button wire:click="approve({{ $req->id }})" wire:confirm="Approve this leave request? This will also mark attendance as Leave for these dates." class="btn btn-sm btn-success rounded-pill px-3 mb-1"><i class="fas fa-check"></i> Approve</button>
                                    <button wire:click="reject({{ $req->id }})" wire:confirm="Reject this leave request?" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fas fa-times"></i> Reject</button>
                                @else
                                    <span class="text-muted small">{{ $req->approved_at?->format('d-M-Y h:i A') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-calendar-times fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No leave requests found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
