<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Employee Salary Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Employee Salary Report</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-8 position-relative">
                    <label class="small fw-bold text-muted mb-1">Search Employee</label>
                    <input type="text" wire:model.live.debounce.300ms="employee_search" placeholder="Name or employee no...." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                    @if(count($suggested_employees) > 0)
                        <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; top: 100%;">
                            @foreach($suggested_employees as $e)
                                <button type="button" wire:click="selectEmployee({{ $e['id'] }})" class="list-group-item list-group-item-action">
                                    <strong>{{ $e['first_name'] }} {{ $e['last_name'] }}</strong>
                                    <span class="text-muted small"> — {{ $e['emp_no'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light shadow-sm">
                </div>
            </div>
        </div>
    </div>

    @if($selected_employee)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-1">{{ $selected_employee->first_name }} {{ $selected_employee->last_name }}</h5>
                        <div class="text-muted small">
                            {{ $selected_employee->emp_no }} &middot;
                            {{ $selected_employee->department->name ?? '—' }} {{ $selected_employee->designation ? '/ ' . $selected_employee->designation->name : '' }}
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2 text-center">
                            <div class="col">
                                <div class="p-2 rounded-3 bg-success-subtle"><div class="fw-bold text-success fs-6">{{ number_format($totals['gross'], 0) }}</div><div class="small text-muted">Gross</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-danger-subtle"><div class="fw-bold text-danger fs-6">{{ number_format($totals['deductions'], 0) }}</div><div class="small text-muted">Deductions</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-light"><div class="fw-bold fs-6">{{ number_format($totals['net'], 0) }}</div><div class="small text-muted">Net Paid</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-info-subtle"><div class="fw-bold text-info fs-6">{{ $totals['paid_count'] }}</div><div class="small text-muted">Months Paid</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-warning-subtle"><div class="fw-bold text-warning fs-6">{{ $totals['pending_count'] }}</div><div class="small text-muted">Pending</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> Payment History — {{ $year }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Period</th>
                            <th>Basic Salary</th>
                            <th>Gross</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-3">Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td class="ps-3 fw-bold">{{ \Carbon\Carbon::createFromDate($p->year, $p->month, 1)->format('F Y') }}</td>
                                <td>{{ number_format($p->basic_salary, 2) }}</td>
                                <td class="text-success">{{ number_format($p->gross_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($p->deductions_amount, 2) }}</td>
                                <td class="fw-bold">{{ number_format($p->net_amount, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $p->status === 'paid' ? 'bg-success' : 'bg-warning' }} text-uppercase">{{ $p->status }}</span>
                                </td>
                                <td class="text-center pe-3">
                                    <a href="{{ route('print-payslip', $p->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">No salary payments found for {{ $year }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
            Search and select an employee above to view their salary report.
        </div>
    @endif
</div>
