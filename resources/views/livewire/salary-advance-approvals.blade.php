<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2 text-primary"></i> Salary Advance Approvals</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Salary Advance Approvals</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('hrm.salary-advance-request') }}" class="btn btn-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-plus me-2"></i> Request Advance
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
            <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Salary Advance Requests</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $advances->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Employee</th>
                        <th width="120">Amount</th>
                        <th>Reason</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="90">Deducted</th>
                        <th width="150">Approved By</th>
                        <th class="text-center pe-3" width="160">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advances as $adv)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $adv->employee->first_name ?? '—' }} {{ $adv->employee->last_name ?? '' }} <span class="text-muted small">({{ $adv->employee->emp_no ?? '—' }})</span></td>
                            <td>{{ number_format($adv->amount, 2) }}</td>
                            <td class="text-truncate" style="max-width: 220px;" title="{{ $adv->reason }}">{{ $adv->reason ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $adv->status === 'approved' ? 'bg-success' : ($adv->status === 'rejected' ? 'bg-danger' : 'bg-warning') }} text-uppercase">{{ $adv->status }}</span>
                            </td>
                            <td class="text-center">
                                @if($adv->status === 'approved')
                                    <span class="badge {{ $adv->deducted ? 'bg-success' : 'bg-secondary' }}">{{ $adv->deducted ? 'Yes' : 'Pending' }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-muted">{{ $adv->approvedBy->name ?? '—' }}</td>
                            <td class="text-center pe-3">
                                @if($adv->status === 'pending')
                                    <button wire:click="approve({{ $adv->id }})" wire:confirm="Approve this salary advance?" class="btn btn-sm btn-success rounded-pill px-3 mb-1"><i class="fas fa-check"></i> Approve</button>
                                    <button wire:click="reject({{ $adv->id }})" wire:confirm="Reject this salary advance?" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fas fa-times"></i> Reject</button>
                                @else
                                    <span class="text-muted small">{{ $adv->approved_at?->format('d-M-Y') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-hand-holding-usd fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No salary advance requests found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $advances->links() }}
        </div>
    </div>
</div>
