<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-balance-scale me-2 text-primary"></i> Trial Balance</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Trial Balance</li>
                </ol>
            </nav>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="fas fa-print me-2"></i> Print
        </button>
    </div>

    {{-- Date / Campus Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Select Start Date</label>
                    <input type="date" wire:model.live="start_date"
                           class="form-control border-0 bg-light shadow-sm rounded-3">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Select End Date</label>
                    <input type="date" wire:model.live="end_date"
                           class="form-control border-0 bg-light shadow-sm rounded-3">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="filter_campus" class="form-select border-0 bg-light shadow-sm rounded-3">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button wire:click="$refresh" class="btn btn-success rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-2"></i> Get Record
                    </button>
                    <button wire:click="resetFilters" class="btn btn-warning rounded-3 px-3 shadow-sm">
                        <i class="fas fa-undo me-1"></i> Fix Dates
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Out-of-balance warning - never hide a real data-integrity issue --}}
    @if(!$is_balanced)
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3 fw-bold">
            <i class="fas fa-exclamation-triangle fs-4"></i>
            <div>
                Trial Balance does NOT balance for the selected filters.
                Difference of <span class="fs-5">{{ number_format(abs($difference), 2) }}</span>
                ({{ $difference > 0 ? 'Debit exceeds Credit' : 'Credit exceeds Debit' }}).
                Please review the journal entries in this date range.
            </div>
        </div>
    @endif

    {{-- Summary Totals Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="fas fa-arrow-down fs-5 text-success"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Debit (Dr)</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($total_debit, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="fas fa-arrow-up fs-5 text-danger"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Credit (Cr)</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($total_credit, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 {{ $is_balanced ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                    <i class="fas fa-{{ $is_balanced ? 'check-circle' : 'times-circle' }} fs-5 {{ $is_balanced ? 'text-success' : 'text-danger' }}"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Difference (Dr - Cr)</div>
                    <div class="fw-bold fs-5 {{ $is_balanced ? 'text-success' : 'text-danger' }}">
                        {{ number_format(abs($difference), 2) }}
                        <small class="fs-6">{{ $is_balanced ? 'Balanced' : ($difference > 0 ? 'Dr' : 'Cr') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Trial Balance Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-table me-2"></i> Trial Balance</span>
            <span class="badge bg-white text-dark px-3 py-2">
                {{ \Carbon\Carbon::parse($start_date)->format('d-M-Y') }} &mdash; {{ \Carbon\Carbon::parse($end_date)->format('d-M-Y') }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3" width="100">Code</th>
                        <th>Account Name</th>
                        <th class="text-end" width="160">Debit</th>
                        <th class="text-end pe-3" width="160">Credit</th>
                    </tr>
                </thead>

                <tbody>
                    @php $hasAnyAccounts = collect($grouped_accounts)->flatten(1)->count() > 0; @endphp

                    @forelse($grouped_accounts as $type => $accounts)
                        @if($accounts->count() > 0)
                            {{-- Group Header Row (Asset, Liability, Equity, Income, Expense) --}}
                            <tr>
                                <td colspan="4" class="fw-bold text-uppercase small text-dark py-2 ps-3" style="letter-spacing: 1px; background:#e2e8f0;">
                                    {{ ucfirst($type) }}
                                </td>
                            </tr>

                            @foreach($accounts as $account)
                                <tr>
                                    <td class="ps-3 fw-bold text-info" style="font-size:0.72rem;">{{ $account->code }}</td>
                                    <td style="font-size:0.75rem;">{{ $account->name }}</td>
                                    <td class="text-end" style="font-size:0.78rem;">
                                        @if($account->total_debit > 0)
                                            <span class="text-success fw-bold">{{ number_format($account->total_debit, 2) }}</span>
                                        @else
                                            <span class="text-muted">0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3" style="font-size:0.78rem;">
                                        @if($account->total_credit > 0)
                                            <span class="text-danger fw-bold">{{ number_format($account->total_credit, 2) }}</span>
                                        @else
                                            <span class="text-muted">0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Subtotal Row for this account type --}}
                            <tr class="fw-bold" style="background:#f1f5f9; font-size:0.75rem;">
                                <td colspan="2" class="text-end ps-3">Subtotal &mdash; {{ ucfirst($type) }}:</td>
                                <td class="text-end text-success">{{ number_format($accounts->sum('total_debit'), 2) }}</td>
                                <td class="text-end pe-3 text-danger">{{ number_format($accounts->sum('total_credit'), 2) }}</td>
                            </tr>
                        @endif
                    @empty
                    @endforelse

                    @if(!$hasAnyAccounts)
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-balance-scale fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted">No active GL accounts found for this tenant.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>

                @if($hasAnyAccounts)
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.82rem;">
                        <td colspan="2" class="text-end ps-3">GRAND TOTAL:</td>
                        <td class="text-end">{{ number_format($total_debit, 2) }}</td>
                        <td class="text-end pe-3">{{ number_format($total_credit, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <style>
        .tiny { font-size: 0.72rem; }
        @media print {
            .btn, nav, .card-header .badge { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</div>
