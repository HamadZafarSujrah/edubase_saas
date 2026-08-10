<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line me-2 text-warning"></i> Student Yearly Fee Increment</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Yearly Fee Increment</li>
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

    {{-- Filters + Increment Settings --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-2 small fw-bold text-uppercase text-muted">Scope &amp; Increment</div>
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
                    <label class="small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All classes</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Section (optional)</label>
                    <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All sections</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Fee Particular</label>
                    <select wire:model.live="fee_particular_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All particulars</option>
                        @foreach($fee_particulars as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-3">

            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Increment Type</label>
                    <select wire:model.live="increment_type" class="form-select border-0 bg-light shadow-sm">
                        <option value="percent">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Increment Value {{ $increment_type === 'percent' ? '(%)' : '(Rs.)' }}</label>
                    <input type="number" step="0.01" min="0" wire:model.live="increment_value" class="form-control border-0 bg-light shadow-sm fw-bold text-warning">
                </div>
                <div class="col-md-6">
                    <div class="text-muted small"><i class="fas fa-info-circle me-1"></i> Applies to every fee item matching the scope above. Review the preview below carefully before applying — this affects billing for all matched students.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview --}}
    @if(count($preview) > 0)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-eye me-2"></i> Preview</span>
                <span class="badge bg-white text-dark px-3 py-2">{{ count($preview) }} student(s) affected</span>
            </div>
            <div class="table-responsive" style="max-height: 400px;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3">Student</th>
                            <th width="120">Admission No.</th>
                            <th class="text-center" width="100">Particulars</th>
                            <th class="text-end" width="140">Current Total</th>
                            <th class="text-end pe-3" width="140">New Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview as $row)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $row['student_name'] }}</td>
                                <td class="text-muted">{{ $row['admission_no'] }}</td>
                                <td class="text-center">{{ $row['particulars'] }}</td>
                                <td class="text-end text-muted">{{ number_format($row['current'], 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-success">{{ number_format($row['new'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background:#0f172a; color:#fff;">
                            <td colspan="3" class="text-end pe-3 ps-3">Total:</td>
                            <td class="text-end">{{ number_format(collect($preview)->sum('current'), 2) }}</td>
                            <td class="text-end pe-3">{{ number_format(collect($preview)->sum('new'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <button wire:click="apply"
                onclick="return confirm('Apply this increment to {{ count($preview) }} student(s)? This directly changes their billed fee amounts.');"
                wire:loading.attr="disabled"
                class="btn btn-warning fw-bold px-5 py-2 rounded-3 shadow-sm">
            <i class="fas fa-check-circle me-2"></i> Apply Increment to {{ count($preview) }} Student(s)
        </button>
    @elseif($increment_value && $campus_id === '' && $school_class_id === '')
        <div class="alert alert-info border-0 shadow-sm"><i class="fas fa-info-circle me-2"></i>Select at least a campus or class to preview the affected students.</div>
    @endif
</div>
