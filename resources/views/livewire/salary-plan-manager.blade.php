<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Create / Update Salary Plan</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hrm.salary-plan-approvals') }}" class="text-decoration-none">Salary Plan Approvals</a></li>
                    <li class="breadcrumb-item active">Create / Update Salary Plan</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="position-relative mb-3">
                <label class="small fw-bold text-muted mb-1">Employee <span class="text-danger">*</span></label>
                <input type="text" wire:model.live.debounce.300ms="employee_search" placeholder="Search by name or employee no...." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                @error('employee_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @if(count($suggested_employees) > 0)
                    <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                        @foreach($suggested_employees as $e)
                            <button type="button" wire:click="selectEmployee({{ $e['id'] }})" class="list-group-item list-group-item-action">
                                <strong>{{ $e['first_name'] }} {{ $e['last_name'] }}</strong>
                                <span class="text-muted small"> — {{ $e['emp_no'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($selected_employee)
                @if($current_plan)
                    <div class="alert alert-info border-0 py-2 mb-3">
                        <i class="fas fa-info-circle me-2"></i> Current plan on file: <strong>{{ number_format($current_plan->basic_salary, 2) }}</strong> basic salary,
                        status <span class="badge {{ $current_plan->status === 'approved' ? 'bg-success' : ($current_plan->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">{{ $current_plan->status }}</span>.
                        Submitting below creates a <strong>new version</strong> requiring approval before it takes effect.
                    </div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-1">Basic Salary <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" wire:model="basic_salary" class="form-control border-0 bg-light shadow-sm">
                        @error('basic_salary') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-1">Effective From <span class="text-danger">*</span></label>
                        <input type="date" wire:model="effective_from" class="form-control border-0 bg-light shadow-sm">
                        @error('effective_from') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <label class="small fw-bold text-muted mb-1">Allowances &amp; Deductions</label>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th width="40%">Name</th>
                            <th width="25%">Amount</th>
                            <th width="25%">Type</th>
                            <th width="10%"><button type="button" wire:click="addAllowanceRow" class="btn btn-success btn-sm w-100 rounded-0"><i class="fas fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allowance_rows as $index => $row)
                        <tr>
                            <td><input type="text" list="allowanceCatalog" wire:model="allowance_rows.{{ $index }}.name" class="form-control form-control-sm border-0 bg-light" placeholder="E.g. House Rent, Provident Fund"></td>
                            <td><input type="number" step="0.01" wire:model="allowance_rows.{{ $index }}.amount" class="form-control form-control-sm border-0 bg-light"></td>
                            <td>
                                <select wire:model="allowance_rows.{{ $index }}.type" class="form-select form-select-sm border-0 bg-light">
                                    <option value="allowance">Allowance</option>
                                    <option value="deduction">Deduction</option>
                                </select>
                            </td>
                            <td class="text-center bg-light">
                                @if(count($allowance_rows) > 1)
                                    <button type="button" wire:click="removeAllowanceRow({{ $index }})" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <datalist id="allowanceCatalog">
                    @foreach($allowanceTypes as $at)
                        <option value="{{ $at->name }}"></option>
                    @endforeach
                </datalist>

                <div class="mt-4">
                    <button type="button" wire:click="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                    </button>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
                    Search and select an employee above to create or update their salary plan.
                </div>
            @endif
        </div>
    </div>
</div>
