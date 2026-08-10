<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-money-bill-wave me-2 text-success"></i> Cash Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Cash Report</li>
                </ol>
            </nav>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="fas fa-print me-2"></i> Print
        </button>
    </div>

    @if($cashAccounts->count() === 0)
        {{-- Empty state: no cash/bank GL account found for this tenant --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-piggy-bank fs-1 text-muted opacity-25 mb-3 d-block"></i>
                <p class="text-muted fw-bold mb-1">No Cash/Bank GL account found.</p>
                <small class="text-muted">
                    We looked for active GL accounts whose name contains "Cash" or "Bank" but none exist yet for
                    this tenant. Create a Cash or Bank account in Chart of Accounts to see this report.
                </small>
            </div>
        </div>
    @else

        {{-- Filter Card --}}
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
                        <label class="small fw-bold text-muted mb-1">Cash / Bank Account</label>
                        <select wire:model.live="selected_account_id"
                                class="form-select border-0 bg-light shadow-sm rounded-3">
                            @foreach($cashAccounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button wire:click="$refresh" class="btn btn-success rounded-3 px-4 shadow-sm">
                            <i class="fas fa-check me-2"></i> Get Record
                        </button>
                        <button wire:click="resetFilters" class="btn btn-warning rounded-3 px-3 shadow-sm">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Combined Summary Cards: across ALL cash/bank accounts for this tenant --}}
        <div class="mb-2">
            <span class="tiny text-muted fw-bold text-uppercase">
                <i class="fas fa-layer-group me-1"></i> Combined position across all {{ $cashAccounts->count() }} cash/bank account(s)
            </span>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <i class="fas fa-door-open fs-5 text-primary"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Combined Opening</div>
                        <div class="fw-bold fs-5 text-primary">{{ number_format($combined_opening, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10">
                        <i class="fas fa-arrow-down fs-5 text-success"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Combined Inflow</div>
                        <div class="fw-bold fs-5 text-success">{{ number_format($combined_inflow, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                        <i class="fas fa-arrow-up fs-5 text-danger"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Combined Outflow</div>
                        <div class="fw-bold fs-5 text-danger">{{ number_format($combined_outflow, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                        <i class="fas fa-door-closed fs-5 text-warning"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Combined Closing</div>
                        <div class="fw-bold fs-5 {{ $combined_closing >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($combined_closing, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Per-Account Drill-down Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3 d-flex justify-content-between align-items-center"
                 style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold">
                    <i class="fas fa-calendar-day me-2"></i> Daily Cash Flow
                    @php $selectedAccount = $cashAccounts->firstWhere('id', $selected_account_id); @endphp
                    @if($selectedAccount)
                        &mdash; {{ $selectedAccount->code }} {{ $selectedAccount->name }}
                    @endif
                </span>
                <span class="badge bg-white text-dark px-3 py-2">
                    {{ count($daily_rows) }} day(s) with activity
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="text-center ps-3" width="60">No.</th>
                            <th width="150">Date</th>
                            <th class="text-end" width="160">Inflow (Debit)</th>
                            <th class="text-end" width="160">Outflow (Credit)</th>
                            <th class="text-end" width="180">Running Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="table-light">
                            <td class="text-center fw-bold text-muted ps-3">—</td>
                            <td class="fw-bold">Opening Balance</td>
                            <td class="text-end">&mdash;</td>
                            <td class="text-end">&mdash;</td>
                            <td class="text-end fw-bold">{{ number_format($opening_balance, 2) }}</td>
                        </tr>

                        @forelse($daily_rows as $i => $row)
                            <tr>
                                <td class="text-center fw-bold text-muted ps-3">{{ $i + 1 }}</td>
                                <td class="fw-bold" style="font-size:0.72rem;">
                                    {{ \Carbon\Carbon::parse($row['date'])->format('d-M-Y') }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ $row['inflow'] > 0 ? number_format($row['inflow'], 2) : '—' }}
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    {{ $row['outflow'] > 0 ? number_format($row['outflow'], 2) : '—' }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ number_format($row['running_balance'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-calendar-times fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No cash activity found for the selected date range on this account.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr class="fw-bold" style="background:#1e293b; color:#fff; font-size:0.78rem;">
                            <td colspan="2" class="text-end pe-3 ps-3">Period Totals:</td>
                            <td class="text-end pe-3 text-success">{{ number_format($total_inflow, 2) }}</td>
                            <td class="text-end pe-3 text-danger">{{ number_format($total_outflow, 2) }}</td>
                            <td class="text-end pe-3">&nbsp;</td>
                        </tr>
                        <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                            <td colspan="4" class="text-end pe-3 ps-3">Closing Balance:</td>
                            <td class="text-end pe-3">{{ number_format($closing_balance, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @endif

    <style>
        .tiny { font-size: 0.72rem; }
        @media print {
            .btn, nav, .card-header .badge { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</div>
