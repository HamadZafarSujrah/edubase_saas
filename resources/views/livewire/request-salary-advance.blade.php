<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2 text-primary"></i> Request Salary Advance</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hrm.salary-advance-approvals') }}" class="text-decoration-none">Advance Approvals</a></li>
                    <li class="breadcrumb-item active">Request Advance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6 position-relative">
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
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" wire:model="amount" class="form-control border-0 bg-light shadow-sm">
                    @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="small fw-bold text-muted mb-1">Reason</label>
                    <textarea wire:model="reason" rows="3" class="form-control border-0 bg-light shadow-sm"></textarea>
                </div>
            </div>

            <div class="alert alert-info border-0 mt-3 mb-0 py-2">
                <i class="fas fa-info-circle me-2"></i> Once approved, this amount is automatically deducted from the employee's next generated salary payment.
            </div>

            <div class="mt-4">
                <button type="button" wire:click="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                </button>
            </div>
        </div>
    </div>
</div>
