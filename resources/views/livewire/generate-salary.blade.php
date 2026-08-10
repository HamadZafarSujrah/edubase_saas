<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calculator me-2 text-primary"></i> Generate Monthly Salary</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Generate Monthly Salary</li>
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
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Department</label>
                    <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light shadow-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-2">
                    <div class="form-check form-switch pt-2">
                        <input type="checkbox" class="form-check-input" wire:model.live="showOnlyPending" id="pendingOnly">
                        <label class="form-check-label small fw-bold" for="pendingOnly">Not yet generated</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button wire:click="generate" wire:confirm="Generate salary for {{ $this->eligibleEmployees->count() }} employee(s) for {{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }} {{ $year }}?" class="btn btn-primary w-100 fw-bold rounded-3 shadow-sm">
                        <i class="fas fa-bolt me-2"></i> Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-users me-2"></i> Eligible Employees</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $this->eligibleEmployees->count() }} employee(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Employee</th>
                        <th width="110">Basic Salary</th>
                        <th width="110">Gross</th>
                        <th width="110">Deductions</th>
                        <th width="110">Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->eligibleEmployees as $emp)
                        @php $plan = $emp->activeSalaryPlan; @endphp
                        <tr>
                            <td class="ps-3 fw-bold">{{ $emp->first_name }} {{ $emp->last_name }} <span class="text-muted small">({{ $emp->emp_no }})</span></td>
                            <td>{{ number_format($plan->basic_salary, 2) }}</td>
                            <td class="text-success">{{ number_format($plan->gross, 2) }}</td>
                            <td class="text-danger">{{ number_format($plan->deductions, 2) }}</td>
                            <td class="fw-bold">{{ number_format($plan->net, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-info-circle fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No eligible employees found. An employee needs an <strong>approved, active</strong> salary plan to appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
