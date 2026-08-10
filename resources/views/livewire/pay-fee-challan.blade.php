<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt me-2 text-primary"></i> Pay Fee Challan</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.pay-print-challans') }}" class="text-decoration-none">Pay or Print Fee Challans</a></li>
                    <li class="breadcrumb-item active">{{ $challan->challan_no }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('finance.pay-print-challans') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    {{-- Student Info Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header text-center py-3 border-0" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
            <h5 class="fw-bold text-white mb-0"><i class="fas fa-file-invoice-dollar me-2"></i> Pay Fee Challan</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0" style="font-size:0.82rem;">
                <tr>
                    <th class="bg-info bg-opacity-10 text-dark ps-3" width="13%">Student Name</th>
                    <td class="fw-bold ps-3" width="20%">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <th class="bg-info bg-opacity-10 text-dark ps-3" width="13%">Father Name</th>
                    <td class="fw-bold ps-3" width="20%">{{ $student->father_name }}</td>
                    <th class="bg-info bg-opacity-10 text-dark ps-3" width="12%">Registration No.</th>
                    <td class="fw-bold ps-3" width="10%">{{ $student->system_id ?? $student->admission_no }}</td>
                    <th class="bg-info bg-opacity-10 text-dark ps-3" width="6%">Campus</th>
                    <td class="fw-bold ps-3">{{ $student->campus?->name }}</td>
                </tr>
                <tr>
                    <th class="bg-warning bg-opacity-10 text-dark ps-3">Class</th>
                    <td class="fw-bold ps-3">{{ $student->schoolClass?->name }}</td>
                    <th class="bg-warning bg-opacity-10 text-dark ps-3">Section</th>
                    <td class="fw-bold ps-3">{{ $student->section?->name ?? '—' }}</td>
                    <th class="bg-warning bg-opacity-10 text-dark ps-3">Session</th>
                    <td class="fw-bold ps-3">{{ $student->academicSession?->name }}</td>
                    <th class="bg-warning bg-opacity-10 text-dark ps-3">Father Contact</th>
                    <td class="fw-bold ps-3">{{ $student->father_phone }}</td>
                </tr>
                <tr class="bg-warning bg-opacity-10">
                    <th class="text-dark ps-3">Installment No.</th>
                    <td class="fw-bold ps-3">{{ $challan->id }}</td>
                    <th class="text-dark ps-3">Challan Month</th>
                    <td class="fw-bold ps-3 text-capitalize">{{ $challan->month }}, {{ $challan->year }}</td>
                    <th class="text-dark ps-3">Challan No.</th>
                    <td class="fw-bold ps-3 text-primary">{{ $challan->challan_no }}</td>
                    <th class="text-dark ps-3">Challan Expiry</th>
                    <td class="fw-bold ps-3 text-danger">{{ $challan->due_date ? \Carbon\Carbon::parse($challan->due_date)->format('d M Y') : '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT: Main Payment Form --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">

                    {{-- Receipt No & Dates --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Receipt No</label>
                            <input type="text" wire:model="receipt_no" class="form-control bg-light border-0 shadow-sm" placeholder="Auto if blank">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Due Date</label>
                            <input type="date" wire:model="due_date" class="form-control bg-light border-0 shadow-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Paid Date</label>
                            <input type="date" wire:model="paid_date" class="form-control bg-light border-0 shadow-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <div class="form-control bg-light border-0 shadow-sm fw-bold
                                {{ $challan->status === 'paid' ? 'text-success' : 'text-danger' }}">
                                {{ strtoupper($challan->status) }}
                            </div>
                        </div>
                    </div>

                    {{-- Challan Notes --}}
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Challan Notes</label>
                        <textarea wire:model="challan_notes" class="form-control bg-light border-0 shadow-sm" rows="2" placeholder="Write Challan Notes..."></textarea>
                    </div>

                    {{-- Account Selectors --}}
                    <div class="row g-0 mb-4">
                        {{-- Discount/Bad Debt Account --}}
                        <div class="col-md-6 pe-2">
                            <div class="card border-0 rounded-3 overflow-hidden shadow-sm">
                                <div class="card-header text-center text-white fw-bold py-2" style="background:#0d9488; font-size:0.82rem;">
                                    <i class="fas fa-tags me-1"></i> Discount/Bad Debt Account
                                </div>
                                <div class="card-body p-2">
                                    <select wire:model.live="discount_account_id" class="form-select border-0 bg-light shadow-sm">
                                        <option value="">Select Discount/Bad Debt</option>
                                        @foreach($discount_accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('discount_account_id')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- Payment Receiving Account --}}
                        <div class="col-md-6 ps-2">
                            <div class="card border-0 rounded-3 overflow-hidden shadow-sm">
                                <div class="card-header text-center text-white fw-bold py-2" style="background:#0d6efd; font-size:0.82rem;">
                                    <i class="fas fa-cash-register me-1"></i> Payment Receiving Account
                                </div>
                                <div class="card-body p-2">
                                    <select wire:model.live="receiving_account_id" class="form-select border-0 bg-light shadow-sm">
                                        <option value="">Prime Pymt Acct to Find</option>
                                        @foreach($receiving_accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('receiving_account_id')
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fee Particulars Table --}}
                    <div class="table-responsive rounded-3 overflow-hidden shadow-sm mb-4">
                        <table class="table table-bordered align-middle mb-0" style="font-size:0.83rem;">
                            <thead>
                                <tr class="text-center fw-bold" style="background:#1e293b; color:#fff;">
                                    <th class="ps-3 text-start" width="40">#</th>
                                    <th class="text-start">Particular</th>
                                    <th width="120">Payable</th>
                                    <th width="140">Discount / Bad Debt</th>
                                    <th width="150">After Discount Payable</th>
                                    <th width="150">Current Payment</th>
                                    <th width="120">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($line_items as $i => $item)
                                    <tr class="{{ $item['payable'] == 0 ? 'table-light text-muted' : '' }}">
                                        <td class="text-center fw-bold text-muted ps-3">{{ $i + 1 }}</td>
                                        <td class="fw-bold ps-3">{{ $item['name'] }}</td>
                                        <td class="text-center fw-bold text-dark">
                                            {{ number_format($item['payable']) }}
                                        </td>
                                        <td class="text-center p-1">
                                            <input wire:model.live="line_items.{{ $i }}.discount"
                                                   type="number" step="1" min="0"
                                                   class="form-control form-control-sm text-center border-0 bg-light fw-bold text-danger shadow-sm"
                                                   {{ $challan->status === 'paid' ? 'disabled' : '' }}>
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ number_format($item['after_discount']) }}
                                        </td>
                                        <td class="text-center p-1">
                                            <input wire:model.live="line_items.{{ $i }}.current_payment"
                                                   type="number" step="1" min="0"
                                                   class="form-control form-control-sm text-center border-0 bg-primary bg-opacity-10 fw-bold text-primary shadow-sm"
                                                   {{ $challan->status === 'paid' ? 'disabled' : '' }}>
                                        </td>
                                        <td class="text-center fw-bold text-danger">
                                            {{ number_format($item['remaining']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold text-center" style="background:#1e293b; color:#fff; font-size:0.9rem;">
                                    <td colspan="2" class="text-end ps-3">Total</td>
                                    <td>{{ number_format($total_payable) }}</td>
                                    <td>{{ number_format($total_discount) }}</td>
                                    <td>{{ number_format($total_after_discount) }}</td>
                                    <td>{{ number_format($total_paid) }}</td>
                                    <td class="{{ $total_remaining > 0 ? 'text-warning' : '' }}">{{ number_format($total_remaining) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Named Discounts (sibling, merit, scholarship, ...) --}}
                    <div class="card border-0 rounded-3 overflow-hidden shadow-sm mb-4">
                        <div class="card-header text-center text-white fw-bold py-2" style="background:#0d9488; font-size:0.82rem;">
                            <i class="fas fa-percent me-1"></i> Named Discounts
                        </div>
                        <div class="card-body p-3">

                            @error('named_discounts')
                                <div class="alert alert-danger py-2 small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror

                            @if(count($existing_discounts) > 0)
                                <div class="mb-3">
                                    <div class="small fw-bold text-muted mb-1">Already applied to this challan</div>
                                    <table class="table table-sm table-bordered mb-0" style="font-size:0.8rem;">
                                        <thead class="table-light">
                                            <tr><th>Discount Type</th><th>Amount</th><th>Reason</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($existing_discounts as $ed)
                                                <tr>
                                                    <td>{{ $ed['discount_type']['name'] ?? '—' }}</td>
                                                    <td class="text-danger fw-bold">{{ number_format($ed['amount'], 2) }}</td>
                                                    <td class="text-muted">{{ $ed['reason'] ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if($challan->status !== 'paid')
                                @foreach($named_discounts as $i => $discount)
                                    <div class="row g-2 mb-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Discount Type</label>
                                            <select wire:model.live="named_discounts.{{ $i }}.discount_type_id" class="form-select form-select-sm border-0 bg-light shadow-sm">
                                                <option value="">Select discount type</option>
                                                @foreach($discount_types as $dt)
                                                    <option value="{{ $dt->id }}">{{ $dt->name }} ({{ $dt->type === 'percent' ? '%' : 'Fixed' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @php($selectedType = $discount_types->firstWhere('id', (int) ($discount['discount_type_id'] ?: 0)))
                                        @if($selectedType && $selectedType->type === 'percent')
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Percent</label>
                                                <input type="number" step="0.01" min="0" max="100"
                                                       wire:model.live="named_discounts.{{ $i }}.percent"
                                                       class="form-control form-control-sm border-0 bg-light shadow-sm">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Amount</label>
                                                <div class="form-control form-control-sm bg-light border-0 fw-bold text-muted">{{ number_format($discount['amount'], 2) }}</div>
                                            </div>
                                        @else
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Amount</label>
                                                <input type="number" step="0.01" min="0"
                                                       wire:model.live="named_discounts.{{ $i }}.amount"
                                                       class="form-control form-control-sm border-0 bg-light shadow-sm">
                                            </div>
                                        @endif
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Reason (optional)</label>
                                            <input type="text" wire:model.live="named_discounts.{{ $i }}.reason" class="form-control form-control-sm border-0 bg-light shadow-sm">
                                        </div>
                                        <div class="col-md-1">
                                            <button wire:click="removeNamedDiscount({{ $i }})" class="btn btn-outline-danger btn-sm rounded-circle"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                                <button wire:click="addNamedDiscount" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-1">
                                    <i class="fas fa-plus me-1"></i> Add Discount
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    @if($challan->status !== 'paid')
                        @error('total_paid')
                            <div class="alert alert-danger border-0 shadow-sm mb-3"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                        <div class="d-flex gap-3">
                            <button wire:click="submit" wire:loading.attr="disabled"
                                    class="btn btn-success px-5 py-2 fw-bold rounded-3 shadow-sm">
                                <span wire:loading.remove wire:target="submit">
                                    <i class="fas fa-check-circle me-2"></i> Submit Payment
                                </span>
                                <span wire:loading wire:target="submit">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                </span>
                            </button>
                            <button wire:click="submit" wire:loading.attr="disabled"
                                    class="btn btn-primary px-5 py-2 fw-bold rounded-3 shadow-sm">
                                <i class="fas fa-sms me-2"></i> Submit &amp; Send SMS
                            </button>
                        </div>
                    @else
                        <div class="alert alert-success border-0 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i>
                            This challan was paid on <strong>{{ $challan->paid_date ? \Carbon\Carbon::parse($challan->paid_date)->format('d M Y') : 'N/A' }}</strong>.
                            Receipt No: <strong>{{ $challan->receipt_no }}</strong>
                        </div>
                    @endif

                    {{-- Notification Options --}}
                    <div class="mt-3 d-flex gap-3 flex-wrap">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="chk_father" checked>
                            <label class="form-check-label small fw-bold text-muted" for="chk_father">Father Contact</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="chk_mother">
                            <label class="form-check-label small fw-bold text-muted" for="chk_mother">Mother Contact</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="chk_student">
                            <label class="form-check-label small fw-bold text-muted" for="chk_student">Student Contact</label>
                        </div>
                        <span class="badge py-2 px-3" style="background:#e63946;">
                            <i class="fas fa-sms me-1"></i> Branded SMS
                        </span>
                        <span class="badge py-2 px-3" style="background:#2ecc71;">
                            <i class="fas fa-mobile-alt me-1"></i> App SMS
                        </span>
                        <span class="badge py-2 px-3" style="background:#25D366;">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp SMS
                        </span>
                        <span class="badge py-2 px-3 bg-primary">
                            <i class="fas fa-bell me-1"></i> Parent App Notification
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Other Payables --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header text-center py-2 border-0" style="background:#0d6efd;">
                    <span class="text-white fw-bold small"><i class="fas fa-list-alt me-1"></i> Other Payables of this Student</span>
                </div>
                <div class="card-body p-0">
                    @if(count($other_challans) > 0)
                        <table class="table table-sm table-hover mb-0" style="font-size:0.78rem;">
                            <thead class="table-light">
                                <tr class="fw-bold text-muted text-uppercase text-center" style="font-size:0.7rem;">
                                    <th class="ps-3 text-start">Month</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>View/Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($other_challans as $other)
                                    <tr class="text-center">
                                        <td class="ps-3 text-start fw-bold text-capitalize">{{ $other['month'] }} {{ $other['year'] }}</td>
                                        <td class="fw-bold text-danger">{{ number_format($other['total_amount']) }}</td>
                                        <td class="text-muted">{{ $other['due_date'] ? \Carbon\Carbon::parse($other['due_date'])->format('d M') : '—' }}</td>
                                        <td>
                                            <a href="{{ route('finance.pay-fee-challan', ['challan_id' => $other['id']]) }}" class="btn btn-xs btn-outline-primary px-2 py-0 rounded-pill" style="font-size:0.7rem;">
                                                Pay
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold text-center table-warning">
                                    <td class="ps-3 text-start">Total</td>
                                    <td colspan="3">{{ number_format(array_sum(array_column($other_challans, 'total_amount'))) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fs-3 text-success mb-2 d-block"></i>
                            <small>No other pending challans.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-xs { padding: 0.15rem 0.5rem; font-size: 0.7rem; }
        .tiny { font-size: 0.75rem; }
    </style>
</div>
