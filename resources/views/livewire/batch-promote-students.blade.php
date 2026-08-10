<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-exchange-alt me-2 text-warning"></i> Batch Transfer / Promote Students</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Batch Transfer / Promote</li>
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

    {{-- Mode Toggle --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="btn-group w-100" role="group">
                <button type="button" wire:click="$set('mode', 'promote')" class="btn {{ $mode === 'promote' ? 'btn-warning' : 'btn-outline-warning' }} fw-bold">
                    <i class="fas fa-arrow-up me-2"></i> Promote to Next Class
                </button>
                <button type="button" wire:click="$set('mode', 'transfer')" class="btn {{ $mode === 'transfer' ? 'btn-info' : 'btn-outline-info' }} fw-bold">
                    <i class="fas fa-random me-2"></i> Transfer Between Sections
                </button>
            </div>
        </div>
    </div>

    {{-- Source Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-2 small fw-bold text-uppercase text-muted">Step 1 — Find Students</div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select campus...</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Current Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select class...</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Current Section {{ $mode === 'transfer' ? '(required)' : '(optional)' }}</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All sections</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Session</label>
                    <select wire:model.live="session_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All sessions</option>
                        @foreach($sessions as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Student List --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-users me-2"></i> Step 2 — Select Students</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ count($selected_students) }} / {{ $students->count() }} selected</span>
        </div>
        <div class="table-responsive" style="max-height: 400px;">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="40"><input type="checkbox" wire:model.live="selectAll" class="form-check-input"></th>
                        <th>Name</th>
                        <th width="120">Admission No.</th>
                        <th width="150">Current Class</th>
                        <th width="120">Current Section</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                        <tr>
                            <td class="text-center ps-3"><input type="checkbox" wire:model.live="selected_students" value="{{ $s->id }}" class="form-check-input"></td>
                            <td class="fw-bold">{{ $s->first_name }} {{ $s->last_name }}</td>
                            <td class="text-muted">{{ $s->admission_no }}</td>
                            <td>{{ $s->schoolClass->name ?? '—' }}</td>
                            <td>{{ $s->section->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted mb-0">No students match these filters. Select a campus and class above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Target + Apply --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-2 small fw-bold text-uppercase text-muted">Step 3 — Choose Destination &amp; Apply</div>
        <div class="card-body p-3">
            @if($mode === 'promote')
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Promote To Class</label>
                        <select wire:model.live="target_class_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select target class...</option>
                            @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">New Session</label>
                        <select wire:model.live="target_session_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select session...</option>
                            @foreach($sessions as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">New Section (optional)</label>
                        <select wire:model.live="target_section_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Unassigned (set later)</option>
                            @foreach($targetSections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-muted small mt-3"><i class="fas fa-info-circle me-1"></i> Fee plans are not carried over automatically — assign or generate fee plans for the new class separately after promoting.</div>
            @else
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-1">Transfer To Section</label>
                        <select wire:model.live="target_section_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select target section...</option>
                            @foreach($targetSections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <button wire:click="apply"
                    onclick="return confirm('Move {{ count($selected_students) }} selected student(s)? This cannot be undone automatically.');"
                    wire:loading.attr="disabled"
                    class="btn {{ $mode === 'promote' ? 'btn-warning' : 'btn-info' }} fw-bold px-5 py-2 rounded-3 shadow-sm mt-4">
                <i class="fas fa-check-circle me-2"></i> Apply to {{ count($selected_students) }} Student(s)
            </button>
        </div>
    </div>
</div>
