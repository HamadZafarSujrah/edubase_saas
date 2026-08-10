<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-paper me-2 text-primary"></i> Take / Update Attendance</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Take / Update Attendance</li>
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

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select campus...</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select class...</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Section</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select section...</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Date</label>
                    <input type="date" wire:model.live="date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-2">
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
                <span class="text-white fw-bold"><i class="fas fa-users me-2"></i> Roster — {{ count($roster) }} student(s)</span>
                <div class="d-flex gap-2">
                    <button wire:click="markAll('present')" class="btn btn-sm btn-success rounded-pill px-3">Mark All Present</button>
                    <button wire:click="markAll('absent')" class="btn btn-sm btn-outline-light rounded-pill px-3">Mark All Absent</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Student</th>
                            <th width="120">Admission No.</th>
                            <th width="320">Status</th>
                            <th width="220">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roster as $studentId => $entry)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $entry['name'] }}</td>
                                <td class="text-muted">{{ $entry['admission_no'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100" role="group">
                                        <button type="button" wire:click="$set('roster.{{ $studentId }}.status', 'present')" class="btn {{ $entry['status'] === 'present' ? 'btn-success' : 'btn-outline-success' }}">Present</button>
                                        <button type="button" wire:click="$set('roster.{{ $studentId }}.status', 'absent')" class="btn {{ $entry['status'] === 'absent' ? 'btn-danger' : 'btn-outline-danger' }}">Absent</button>
                                        <button type="button" wire:click="$set('roster.{{ $studentId }}.status', 'leave')" class="btn {{ $entry['status'] === 'leave' ? 'btn-warning' : 'btn-outline-warning' }}">Leave</button>
                                        <button type="button" wire:click="$set('roster.{{ $studentId }}.status', 'late')" class="btn {{ $entry['status'] === 'late' ? 'btn-info' : 'btn-outline-info' }}">Late</button>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" wire:model="roster.{{ $studentId }}.remarks" class="form-control form-control-sm border-0 bg-light" placeholder="Optional...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-save me-2"></i> Save Attendance
                </button>
            </div>
        </div>
    @endif
</div>
