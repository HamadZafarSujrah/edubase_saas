<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-minus me-2 text-primary"></i> Request Leave</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hrm.leave-approvals') }}" class="text-decoration-none">Leave Approvals</a></li>
                    <li class="breadcrumb-item active">Request Leave</li>
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
                    <label class="small fw-bold text-muted mb-1">Leave Type <span class="text-danger">*</span></label>
                    <select wire:model="type" class="form-select border-0 bg-light shadow-sm">
                        <option value="casual">Casual</option>
                        <option value="sick">Sick</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">From Date <span class="text-danger">*</span></label>
                    <input type="date" wire:model="from_date" class="form-control border-0 bg-light shadow-sm">
                    @error('from_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">To Date <span class="text-danger">*</span></label>
                    <input type="date" wire:model="to_date" class="form-control border-0 bg-light shadow-sm">
                    @error('to_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="small fw-bold text-muted mb-1">Reason</label>
                    <textarea wire:model="reason" rows="3" class="form-control border-0 bg-light shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" wire:click="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                </button>
            </div>
        </div>
    </div>
</div>
