<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i> Attendance Sheet</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Attendance Sheet</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
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
                    <label class="small fw-bold text-muted mb-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light shadow-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light shadow-sm">
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('print-attendance-sheet', ['section_id' => $section_id, 'month' => $month, 'year' => $year]) }}"
                   target="_blank"
                   class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm {{ !$section_id ? 'disabled' : '' }}"
                   @if(!$section_id) tabindex="-1" aria-disabled="true" onclick="return false;" @endif>
                    <i class="fas fa-print me-2"></i> Print Attendance Sheet
                </a>
            </div>
        </div>
    </div>
</div>
