<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i> Monthly Attendance</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Monthly Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Department</label>
                    <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light shadow-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light shadow-sm">
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('print-employee-attendance-sheet', ['department_id' => $department_id, 'month' => $month, 'year' => $year]) }}"
                   target="_blank"
                   class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-print me-2"></i> View / Print Monthly Attendance
                </a>
            </div>
        </div>
    </div>
</div>
