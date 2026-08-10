<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-lock me-2 text-primary"></i> Lock / Unlock Attendance</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Lock / Unlock Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Department</label>
                    <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Date</label>
                    <input type="date" wire:model.live="date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <button wire:click="loadRoster" class="btn btn-primary w-100 fw-bold rounded-3 shadow-sm">
                        <i class="fas fa-list me-2"></i> Load Roster
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($loaded)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-users me-2"></i> Roster — {{ count($roster) }} employee(s)</span>
                <div class="d-flex gap-2">
                    <button wire:click="lockDay" wire:loading.attr="disabled" class="btn btn-sm btn-danger rounded-pill px-3"><i class="fas fa-lock me-1"></i> Lock Day</button>
                    <button wire:click="unlockDay" wire:loading.attr="disabled" class="btn btn-sm btn-outline-light rounded-pill px-3"><i class="fas fa-unlock me-1"></i> Unlock Day</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Employee</th>
                            <th width="100">Emp. No.</th>
                            <th width="120">Status</th>
                            <th width="120">Lock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roster as $employeeId => $entry)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $entry['name'] }}</td>
                                <td class="text-muted">{{ $entry['emp_no'] }}</td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($entry['status']) }}</span></td>
                                <td>
                                    @if($entry['locked'])
                                        <span class="badge bg-danger"><i class="fas fa-lock me-1"></i> Locked</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-unlock me-1"></i> Open</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3 small text-muted">
                <i class="fas fa-info-circle me-1"></i> Locking a day prevents further edits from the Take/Update Attendance screen. Any employee without a recorded mark will be locked as Present.
            </div>
        </div>
    @endif
</div>
