<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users me-2 text-primary"></i> Combine Attendance Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Combine Attendance Report</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Section</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">From</label>
                    <input type="date" wire:model.live="start_date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">To</label>
                    <input type="date" wire:model.live="end_date" class="form-control border-0 bg-light shadow-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-table me-2"></i> Attendance Totals — {{ \Carbon\Carbon::parse($start_date)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('d-M-Y') }}</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ count($rows) }} student(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Student</th>
                        <th width="120">Admission No.</th>
                        <th class="text-center" width="90">Present</th>
                        <th class="text-center" width="90">Absent</th>
                        <th class="text-center" width="90">Leave</th>
                        <th class="text-center" width="90">Late</th>
                        <th class="text-center" width="90">Total Marked</th>
                        <th class="text-center" width="110">% Present</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $row['student']->first_name }} {{ $row['student']->last_name }}</td>
                            <td class="text-muted">{{ $row['student']->admission_no }}</td>
                            <td class="text-center text-success fw-bold">{{ $row['present'] }}</td>
                            <td class="text-center text-danger fw-bold">{{ $row['absent'] }}</td>
                            <td class="text-center text-warning fw-bold">{{ $row['leave'] }}</td>
                            <td class="text-center text-info fw-bold">{{ $row['late'] }}</td>
                            <td class="text-center">{{ $row['total'] }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row['percentage'] >= 75 ? 'bg-success' : ($row['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}">{{ $row['percentage'] }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-info-circle fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No students or attendance records found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
