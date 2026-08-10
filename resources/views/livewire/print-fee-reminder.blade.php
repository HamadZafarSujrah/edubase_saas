<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-print me-2 text-danger"></i> Print Fee Reminder</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Print Fee Reminder</li>
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
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Student Status</label>
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Alumni / Inactive</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Min. Outstanding</label>
                    <input type="number" step="0.01" wire:model.live.debounce.300ms="min_outstanding" class="form-control border-0 bg-light shadow-sm" placeholder="e.g. 500">
                </div>
            </div>
        </div>
    </div>

    {{-- Defaulters List --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-exclamation-circle me-2"></i> Fee Defaulters</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ count($selected_students) }} / {{ $this->defaulters->count() }} selected</span>
        </div>
        <div class="table-responsive" style="max-height: 450px;">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="40"><input type="checkbox" wire:model.live="selectAll" class="form-check-input"></th>
                        <th>Name</th>
                        <th width="120">Admission No.</th>
                        <th width="150">Class / Section</th>
                        <th class="text-center" width="100">Challans</th>
                        <th class="text-end pe-3" width="140">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->defaulters as $s)
                        <tr>
                            <td class="text-center ps-3"><input type="checkbox" wire:model.live="selected_students" value="{{ $s->id }}" class="form-check-input"></td>
                            <td class="fw-bold">{{ $s->first_name }} {{ $s->last_name }}</td>
                            <td class="text-muted">{{ $s->admission_no }}</td>
                            <td>{{ $s->schoolClass->name ?? '—' }} {{ $s->section->name ?? '' }}</td>
                            <td class="text-center">{{ $s->challans->count() }}</td>
                            <td class="text-end pe-3 fw-bold text-danger">{{ number_format($s->outstanding_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-check-circle fs-2 text-success opacity-50 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No fee defaulters found for these filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <form action="{{ route('print-fee-reminders') }}" method="GET" target="_blank">
        @foreach($selected_students as $id)
            <input type="hidden" name="ids[]" value="{{ $id }}">
        @endforeach
        <button type="submit" @if(empty($selected_students)) disabled @endif class="btn btn-danger fw-bold px-5 py-2 rounded-3 shadow-sm">
            <i class="fas fa-print me-2"></i> Print {{ count($selected_students) }} Reminder Notice(s)
        </button>
    </form>
</div>
