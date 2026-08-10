<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-clock me-2 text-primary"></i> Individual Attendance Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Individual Attendance Report</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 position-relative">
                    <label class="small fw-bold text-muted mb-1">Search Student</label>
                    <input type="text" wire:model.live.debounce.300ms="student_search" placeholder="Name, admission no. or father name..." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                    @if(count($suggested_students) > 0)
                        <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; top: 100%;">
                            @foreach($suggested_students as $s)
                                <button type="button" wire:click="selectStudent({{ $s['id'] }})" class="list-group-item list-group-item-action">
                                    <strong>{{ $s['first_name'] }} {{ $s['last_name'] }}</strong>
                                    <span class="text-muted small"> — {{ $s['admission_no'] }} — S/o {{ $s['father_name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
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

    @if($selected_student)
        {{-- Student info + summary cards --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-1">{{ $selected_student->first_name }} {{ $selected_student->last_name }}</h5>
                        <div class="text-muted small">
                            {{ $selected_student->admission_no }} &middot;
                            {{ $selected_student->schoolClass->name ?? '—' }} {{ $selected_student->section->name ?? '' }} &middot;
                            {{ $selected_student->campus->name ?? '—' }}
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-2 text-center">
                            <div class="col">
                                <div class="p-2 rounded-3 bg-success-subtle"><div class="fw-bold text-success fs-5">{{ $summary['present'] }}</div><div class="small text-muted">Present</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-danger-subtle"><div class="fw-bold text-danger fs-5">{{ $summary['absent'] }}</div><div class="small text-muted">Absent</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-warning-subtle"><div class="fw-bold text-warning fs-5">{{ $summary['leave'] }}</div><div class="small text-muted">Leave</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-info-subtle"><div class="fw-bold text-info fs-5">{{ $summary['late'] }}</div><div class="small text-muted">Late</div></div>
                            </div>
                            <div class="col">
                                <div class="p-2 rounded-3 bg-light"><div class="fw-bold fs-5">{{ $summary['percentage'] }}%</div><div class="small text-muted">Attendance</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> Daily Record</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Date</th>
                            <th>Day</th>
                            <th class="text-center">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr>
                                <td class="ps-3">{{ \Carbon\Carbon::parse($rec->date)->format('d-M-Y') }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($rec->date)->format('l') }}</td>
                                <td class="text-center">
                                    <span class="badge
                                        @if($rec->status === 'present') bg-success
                                        @elseif($rec->status === 'absent') bg-danger
                                        @elseif($rec->status === 'leave') bg-warning
                                        @else bg-info @endif
                                        text-uppercase">{{ $rec->status }}</span>
                                </td>
                                <td class="text-muted">{{ $rec->remarks ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No attendance records for this date range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
            Search and select a student above to view their attendance report.
        </div>
    @endif
</div>
