<div class="container-fluid pb-5">

    {{-- Breadcrumb --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-{{ $mode === 'create' ? 'file-invoice-dollar' : 'edit' }} me-2 text-primary"></i> 
                {{ $mode === 'create' ? 'Create Fee Plan' : 'Edit Fee Plan' }}
            </h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ $mode === 'create' ? route('create.fee-plans') : route('finance.view-edit-fee-plans') }}" class="text-decoration-none">{{ $mode === 'create' ? 'Create Fee Plans' : 'View / Edit Fee Plans' }}</a></li>
                    <li class="breadcrumb-item active">{{ $student->first_name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ $mode === 'create' ? route('create.fee-plans') : route('finance.view-edit-fee-plans') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Student Info Header (matches SS4) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        {{-- Yellow title bar --}}
        <div class="card-header text-center py-3 border-0" style="background: linear-gradient(135deg, {{ $mode === 'create' ? '#ffd700, #ffb300' : '#4cc9f0, #4361ee' }});">
            <h4 class="fw-bold text-{{ $mode === 'create' ? 'dark' : 'white' }} mb-1">
                {{ $mode === 'create' ? 'Create Fee Plan' : 'Update Existing Fee Plan' }}
            </h4>
            <div class="small text-dark opacity-75">
                Admission Date: <strong>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d-M-Y') : '—' }}</strong>
            </div>
        </div>
        {{-- Student Details Grid --}}
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0" style="font-size: 0.85rem;">
                <tr>
                    <th class="bg-light text-muted ps-3" width="12%">Name</th>
                    <td class="fw-bold ps-3" width="22%">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <th class="bg-light text-muted ps-3" width="12%">Father Name</th>
                    <td class="fw-bold ps-3" width="22%">{{ $student->father_name }}</td>
                    <th class="bg-light text-muted ps-3" width="8%">SID</th>
                    <td class="fw-bold ps-3" width="10%">{{ $student->admission_no }}</td>
                    <th class="bg-light text-muted ps-3" width="8%">Fee Plan</th>
                    <td class="fw-bold ps-3 text-primary" width="6%">
                        {{ $student->feePlan->name ?? 'Not Set' }}
                    </td>
                </tr>
                <tr>
                    <th class="bg-light text-muted ps-3">Campus</th>
                    <td class="ps-3">{{ $student->campus->name ?? '—' }}</td>
                    <th class="bg-light text-muted ps-3">Class</th>
                    <td class="ps-3 fw-bold">{{ $student->schoolClass->name ?? '—' }}</td>
                    <th class="bg-light text-muted ps-3">Section</th>
                    <td class="ps-3 fw-bold">{{ $student->section->name ?? '—' }}</td>
                    <th class="bg-light text-muted ps-3">Session</th>
                    <td class="ps-3">{{ $student->session->name ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <form wire:submit.prevent="save">

        {{-- Settings Block (green bg like SS4) --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: #d4edda;">
            <div class="card-body py-3 px-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">With Effect From</label>
                        <input wire:model.live="with_effect_from" type="month" class="form-control bg-white shadow-sm" {{ $mode === 'view' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Percentage Increment</label>
                        <div class="input-group">
                            <input wire:model.live="percentage_increment" type="number" step="0.1" class="form-control bg-white shadow-sm" {{ $mode === 'view' ? 'disabled' : '' }}>
                            <span class="input-group-text bg-white">%</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Fee Plan Year</label>
                        <select wire:model="fee_plan_year" class="form-select bg-white shadow-sm" {{ $mode === 'view' ? 'disabled' : '' }}>
                            <option value="">Select Year</option>
                            @for($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Discount Type / Reason</label>
                        <select wire:model="discount_type" class="form-select bg-white shadow-sm" {{ $mode === 'view' ? 'disabled' : '' }}>
                            @foreach($discount_types as $dtype)
                                <option value="{{ $dtype }}">{{ $dtype }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <input wire:model="notes" type="text" class="form-control bg-white shadow-sm" placeholder="Optional notes..." {{ $mode === 'view' ? 'disabled' : '' }}>
                    </div>
                </div>

                {{-- Fee Plan Template Switcher --}}
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">
                            <i class="fas fa-sync-alt me-1 text-primary"></i> Switch Fee Plan Template
                        </label>
                        <select wire:model.live="selected_fee_plan_id" class="form-select bg-white shadow-sm" {{ $mode === 'view' ? 'disabled' : '' }}>
                            <option value="">— Select Master Plan —</option>
                            @foreach($fee_plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                        <div class="tiny text-muted mt-1">Switching will reload fee particulars from selected master plan.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fee Particulars Table (matches SS4) --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr class="bg-dark text-white text-center" style="font-size: 0.8rem;">
                            <th width="40" class="ps-3">#</th>
                            <th class="text-start">Fee Particular</th>
                            <th width="160">Actual Fee</th>
                            <th width="160">Discount</th>
                            <th width="180">Fee After Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($line_items))
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted fst-italic">
                                    <i class="fas fa-exclamation-circle me-2 text-warning"></i>
                                    No fee particulars loaded. Please select a master fee plan template above.
                                </td>
                            </tr>
                        @else
                            @foreach($line_items as $i => $item)
                                <tr class="{{ $item['is_first_time'] ? 'table-warning' : '' }}">
                                    <td class="text-center fw-bold text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-bold ps-3">
                                        {{ $item['name'] }}
                                        @if($item['is_first_time'])
                                            <span class="badge bg-warning text-dark ms-2 tiny">One-Time</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input wire:model.live="line_items.{{ $i }}.actual_fee"
                                               type="number" step="1" min="0"
                                               class="form-control text-center border-0 bg-light shadow-sm fw-bold text-primary" {{ $mode === 'view' ? 'disabled' : '' }}>
                                        @error("line_items.$i.actual_fee")
                                            <div class="text-danger tiny mt-1">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input wire:model.live="line_items.{{ $i }}.discount"
                                               type="number" step="1" min="0"
                                               class="form-control text-center border-0 bg-light shadow-sm fw-bold text-danger" {{ $mode === 'view' ? 'disabled' : '' }}>
                                        @error("line_items.$i.discount")
                                            <div class="text-danger tiny mt-1">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input wire:model="line_items.{{ $i }}.fee_after_discount"
                                               type="number"
                                               class="form-control text-center border-0 bg-success bg-opacity-10 fw-bold text-success"
                                               readonly>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- Totals Row --}}
                        @if(!empty($line_items))
                        <tr class="bg-danger text-white fw-bold" style="font-size: 0.9rem;">
                            <td colspan="2" class="text-end ps-3">Total</td>
                            <td class="text-center">{{ number_format($totals['actual_fee'], 0) }}</td>
                            <td class="text-center">{{ number_format($totals['discount'], 0) }}</td>
                            <td class="text-center">{{ number_format($totals['fee_after_discount'], 0) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Save Action --}}
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ $mode === 'create' ? route('create.fee-plans') : route('finance.view-edit-fee-plans') }}" class="btn btn-outline-secondary rounded-pill px-5">
                <i class="fas fa-times me-2"></i> Cancel
            </a>
            @if($mode !== 'view')
                <button type="submit" class="btn btn-{{ $mode === 'create' ? 'success' : 'primary' }} rounded-pill px-5 py-3 fw-bold shadow"
                        style="min-width: 200px; font-size: 1rem;">
                    <i class="fas fa-{{ $mode === 'create' ? 'save' : 'check-circle' }} me-2"></i> 
                    {{ $mode === 'create' ? 'Create Fee Plan' : 'Update Settings' }}
                </button>
            @endif
            <div wire:loading wire:target="save" class="text-center small text-primary fw-bold align-self-center">
                <i class="fas fa-spinner fa-spin me-1"></i> Saving...
            </div>
        </div>

    </form>

    <style>
        .tiny { font-size: 0.72rem; }
    </style>
    </style>
</div>
