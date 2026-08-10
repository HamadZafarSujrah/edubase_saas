<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm no-print">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-qrcode me-2 text-primary"></i> Student QR Code</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Student QR Code</li>
                </ol>
            </nav>
        </div>
        <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-print me-2"></i> Print This Page
        </button>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name or admission no...">
                </div>
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
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Alumni / Inactive</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ID Card Grid --}}
    <div class="row g-3">
        @forelse($students as $student)
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden id-card">
                    <div class="p-3 text-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                        <div class="text-white fw-bold small text-truncate">{{ $student->campus->name ?? 'School' }}</div>
                    </div>
                    <div class="card-body text-center p-3">
                        <img src="{{ $qrCodes[$student->id] }}" alt="QR" class="mb-2" width="120" height="120">
                        <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div class="text-muted small">{{ $student->admission_no }}</div>
                        <div class="text-muted small">{{ $student->schoolClass->name ?? '—' }} {{ $student->section->name ?? '' }}</div>
                        @if(!$student->is_active)
                            <span class="badge bg-secondary mt-1">Alumni</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 text-muted">
                        No students found for these filters.
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 no-print">
        {{ $students->links() }}
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            .id-card { break-inside: avoid; }
        }
    </style>
</div>
