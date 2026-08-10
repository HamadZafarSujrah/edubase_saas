<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-broadcast-tower me-2 text-primary"></i> SMS Blaster</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">SMS Blaster</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
        <i class="fas fa-info-circle me-2"></i> Running in <strong>mock mode</strong> — messages are logged to the SMS Report but not delivered to a real gateway.
    </div>

    @if($show_result)
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle me-2"></i> Sent to <strong>{{ $sent_count }}</strong> recipient(s). {{ $skipped_count }} skipped (no phone number on file).</span>
            <button type="button" class="btn-close" wire:click="$set('show_result', false)"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-muted mb-3">Recipients</h6>
            <div class="row g-3 align-items-end mb-4">
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
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Section</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="p-2 rounded-3 bg-light text-center">
                        <div class="fw-bold fs-5 text-primary">{{ $this->recipientCount }}</div>
                        <div class="small text-muted">Recipient(s) with a phone on file</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-muted mb-0">Message</h6>
                <select wire:model.live="template_id" class="form-select form-select-sm border-0 bg-light shadow-sm" style="max-width: 250px;">
                    <option value="">Use a template...</option>
                    @foreach($templates as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                </select>
            </div>
            <textarea wire:model="message" rows="5" maxlength="640" class="form-control border-0 bg-light shadow-sm" placeholder="Type your message here... Use {student_name} and {class} to personalize each message."></textarea>
            @error('message') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            <div class="small text-muted mt-1">{{ strlen($message) }}/640 characters. Placeholders: <code>{student_name}</code>, <code>{class}</code></div>

            <div class="mt-4">
                <button type="button" wire:click="send" wire:confirm="Send this message to {{ $this->recipientCount }} recipient(s)?" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Send Blast
                </button>
            </div>
        </div>
    </div>
</div>
