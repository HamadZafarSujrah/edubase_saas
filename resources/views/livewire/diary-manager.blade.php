<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-book me-2 text-primary"></i> Student Diary</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Student Diary</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

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
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Section</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">Select section...</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Date</label>
                    <input type="date" wire:model.live="date" class="form-control border-0 bg-light shadow-sm">
                </div>
            </div>
        </div>
    </div>

    @if($section_id)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">Add Diary Entry</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Subject (Optional)</label>
                        <input type="text" wire:model="subject" class="form-control border-0 bg-light shadow-sm" placeholder="E.g. Math, English">
                    </div>
                    <div class="col-md-9">
                        <label class="small fw-bold text-muted mb-1">Content</label>
                        <textarea wire:model="content" rows="2" class="form-control border-0 bg-light shadow-sm" placeholder="Homework, remarks, announcements for this class..."></textarea>
                        @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <button wire:click="addEntry" class="btn btn-primary fw-bold px-4 rounded-3 shadow-sm"><i class="fas fa-plus me-2"></i> Add Entry</button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> Entries for {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($this->entries as $entry)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($entry->subject) <span class="badge bg-secondary me-2">{{ $entry->subject }}</span> @endif
                                <span class="text-muted small">by {{ $entry->createdBy->name ?? '—' }}</span>
                            </div>
                            <button wire:click="deleteEntry({{ $entry->id }})" onclick="confirm('Delete this entry?') || event.stopImmediatePropagation()" class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="mt-2">{{ $entry->content }}</div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">No diary entries for this date.</div>
                @endforelse
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-book fs-2 opacity-50 mb-3 d-block"></i>
            Select a campus, class, and section above to view or add diary entries.
        </div>
    @endif
</div>
