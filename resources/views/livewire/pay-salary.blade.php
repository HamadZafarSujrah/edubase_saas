<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2 text-primary"></i> Pay / Print Salary</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Pay / Print Salary</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Search Employee</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name or employee no....">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Department</label>
                    <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light shadow-sm" placeholder="All">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-money-check-alt me-2"></i> Salary Payments</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $payments->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Employee</th>
                        <th width="130">Period</th>
                        <th width="110">Net Pay</th>
                        <th class="text-center" width="90">Status</th>
                        <th class="text-center pe-3" width="220">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $p->employee->first_name ?? '—' }} {{ $p->employee->last_name ?? '' }} <span class="text-muted small">({{ $p->employee->emp_no ?? '—' }})</span></td>
                            <td class="text-muted">{{ \Carbon\Carbon::createFromDate($p->year, $p->month, 1)->format('F Y') }}</td>
                            <td class="fw-bold">{{ number_format($p->net_amount, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $p->status === 'paid' ? 'bg-success' : 'bg-warning' }} text-uppercase">{{ $p->status }}</span>
                            </td>
                            <td class="text-center pe-3">
                                @if($p->status === 'pending')
                                    <button wire:click="markPaid({{ $p->id }})" wire:confirm="Mark this salary payment as paid?" class="btn btn-sm btn-success rounded-pill px-3"><i class="fas fa-check"></i> Mark Paid</button>
                                @endif
                                <a href="{{ route('print-payslip', $p->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fas fa-print"></i> Payslip</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-money-check fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No salary payments found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>
