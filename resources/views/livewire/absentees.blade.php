<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-eye-slash me-2 text-danger"></i> Absentees</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Absentees</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Date</label>
                    <input type="date" wire:model.live="date" class="form-control border-0 bg-light shadow-sm">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-user-times me-2"></i> Absent / On Leave — {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $this->absentees->count() }} student(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Student</th>
                        <th width="120">Admission No.</th>
                        <th width="150">Campus</th>
                        <th width="150">Class / Section</th>
                        <th class="text-center" width="100">Status</th>
                        <th width="200">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->absentees as $a)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $a->student->first_name ?? '—' }} {{ $a->student->last_name ?? '' }}</td>
                            <td class="text-muted">{{ $a->student->admission_no ?? '—' }}</td>
                            <td>{{ $a->student->campus->name ?? '—' }}</td>
                            <td>{{ $a->student->schoolClass->name ?? '—' }} {{ $a->student->section->name ?? '' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $a->status === 'absent' ? 'bg-danger' : 'bg-warning' }} text-uppercase">{{ $a->status }}</span>
                            </td>
                            <td class="text-muted">{{ $a->remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-check-circle fs-2 text-success opacity-50 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No absentees recorded for this date{{ $campus_id || $school_class_id ? ' (with current filters)' : '' }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
