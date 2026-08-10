<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i> Date Sheet</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Date Sheet</li>
                </ol>
            </nav>
        </div>
        @if($exam_id && $loaded)
            <a href="{{ route('print-date-sheet', $exam_id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4"><i class="fas fa-print me-2"></i> Print</a>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <label class="small fw-bold text-muted mb-1">Exam</label>
            <select wire:model.live="exam_id" class="form-select border-0 bg-light shadow-sm" style="max-width: 400px;">
                <option value="">Select exam...</option>
                @foreach($exams as $e) <option value="{{ $e->id }}">{{ $e->name }} — {{ $e->schoolClass->name ?? '' }}</option> @endforeach
            </select>
        </div>
    </div>

    @if($loaded)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> {{ $exam->name }} — {{ $exam->schoolClass->name ?? '' }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Subject</th>
                            <th width="150">Date</th>
                            <th width="120">Start Time</th>
                            <th width="120">End Time</th>
                            <th width="150">Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $subjectId => $row)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $row['name'] }}</td>
                                <td><input type="date" wire:model="schedule.{{ $subjectId }}.date" class="form-control form-control-sm border-0 bg-light"></td>
                                <td><input type="time" wire:model="schedule.{{ $subjectId }}.start_time" class="form-control form-control-sm border-0 bg-light"></td>
                                <td><input type="time" wire:model="schedule.{{ $subjectId }}.end_time" class="form-control form-control-sm border-0 bg-light"></td>
                                <td><input type="text" wire:model="schedule.{{ $subjectId }}.room" class="form-control form-control-sm border-0 bg-light" placeholder="E.g. Room 101"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <button wire:click="save" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                    <i class="fas fa-save me-2"></i> Save Date Sheet
                </button>
            </div>
        </div>
    @endif
</div>
