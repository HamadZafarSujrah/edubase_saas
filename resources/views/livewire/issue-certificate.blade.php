<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-paper-plane me-2 text-primary"></i> Print Certificates</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Print Certificates</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('certificates.log') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="fas fa-clipboard-list me-2"></i> Certificates Log
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- Student Search --}}
            <div class="mb-4 position-relative">
                <label class="form-label small fw-bold">Search Student</label>
                <input type="text" wire:model.live.debounce.300ms="student_search" class="form-control border-0 bg-light shadow-sm" placeholder="Name, admission no, or father name...">
                @error('student_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                @if(count($suggested_students) > 0)
                    <div class="list-group shadow-sm position-absolute w-100" style="z-index: 10;">
                        @foreach($suggested_students as $s)
                            <button type="button" wire:click="selectStudent({{ $s['id'] }})" class="list-group-item list-group-item-action">
                                <strong>{{ $s['first_name'] }} {{ $s['last_name'] }}</strong> — {{ $s['admission_no'] }} (S/o {{ $s['father_name'] }})
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($selected_student)
                <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $selected_student->first_name }} {{ $selected_student->last_name }}</strong>
                        — {{ $selected_student->admission_no }} — {{ $selected_student->schoolClass->name ?? '—' }}
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Certificate Type</label>
                        <select wire:model.live="certificate_type" class="form-select border-0 bg-light shadow-sm">
                            @foreach($certificate_types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Issue Date</label>
                        <input type="date" wire:model="issued_date" class="form-control border-0 bg-light shadow-sm">
                        @error('issued_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if($certificate_type === 'custom')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Custom Title</label>
                        <input type="text" wire:model="custom_title" class="form-control border-0 bg-light shadow-sm" placeholder="e.g. Sports Participation Certificate">
                        @error('custom_title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label small fw-bold">Certificate Text <span class="text-muted">(edit as needed before issuing)</span></label>
                    <textarea wire:model="body_text" rows="8" class="form-control border-0 bg-light shadow-sm"></textarea>
                    @error('body_text') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <button wire:click="issue" wire:loading.attr="disabled" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-print me-2"></i> Issue &amp; Print
                </button>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-graduate fs-1 opacity-25 mb-3 d-block"></i>
                    Search and select a student above to issue a certificate.
                </div>
            @endif
        </div>
    </div>
</div>
