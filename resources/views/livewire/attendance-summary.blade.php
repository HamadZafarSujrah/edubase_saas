<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-bar me-2 text-primary"></i> Attendance Summary</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Attendance Summary</li>
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

    {{-- Grand total cards --}}
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-success-subtle"><div class="fw-bold text-success fs-4">{{ $grand_totals['present'] }}</div><div class="small text-muted">Total Present</div></div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-danger-subtle"><div class="fw-bold text-danger fs-4">{{ $grand_totals['absent'] }}</div><div class="small text-muted">Total Absent</div></div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-warning-subtle"><div class="fw-bold text-warning fs-4">{{ $grand_totals['leave'] }}</div><div class="small text-muted">Total Leave</div></div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-info-subtle"><div class="fw-bold text-info fs-4">{{ $grand_totals['late'] }}</div><div class="small text-muted">Total Late</div></div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-light"><div class="fw-bold fs-4">{{ $grand_totals['total'] }}</div><div class="small text-muted">Total Marked</div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-calendar-day me-2"></i> Day-wise Summary</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ count($rows) }} day(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Date</th>
                        <th>Day</th>
                        <th class="text-center" width="90">Present</th>
                        <th class="text-center" width="90">Absent</th>
                        <th class="text-center" width="90">Leave</th>
                        <th class="text-center" width="90">Late</th>
                        <th class="text-center" width="100">Total Marked</th>
                        <th class="text-center" width="110">% Present</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td class="ps-3 fw-bold">{{ \Carbon\Carbon::parse($row['date'])->format('d-M-Y') }}</td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($row['date'])->format('l') }}</td>
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
                                <p class="text-muted mb-0">No attendance records found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
