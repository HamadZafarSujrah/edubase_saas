<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line me-2 text-success"></i> Profit &amp; Loss Statement</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Profit &amp; Loss Statement</li>
                </ol>
            </nav>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="fas fa-print me-2"></i> Print
        </button>
    </div>

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

    {{-- Summary Totals Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="fas fa-arrow-trend-up fs-5 text-success"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Income</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($total_income, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="fas fa-arrow-trend-down fs-5 text-danger"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Expense</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($total_expense, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 {{ $net_profit >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                    <i class="fas fa-scale-balanced fs-5 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Net Profit / (Loss)</div>
                    <div class="fw-bold fs-5 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format(abs($net_profit), 2) }}
                        <small class="fs-6">{{ $net_profit >= 0 ? 'Profit' : '(Loss)' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Income Section --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-arrow-trend-up me-2"></i> Income</span>
            <span class="badge bg-white text-dark px-3 py-2">
                {{ $incomeAccounts->count() }} account(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="60">Code</th>
                        <th>Account Name</th>
                        <th class="text-end pe-3" width="160">Net Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomeAccounts as $row)
                        <tr>
                            <td class="text-center fw-bold text-info ps-3" style="font-size:0.72rem;">{{ $row->account_code }}</td>
                            <td class="fw-bold" style="font-size:0.72rem;">{{ $row->account_name }}</td>
                            <td class="text-end fw-bold pe-3 text-success" style="font-size:0.78rem;">
                                {{ number_format($row->net, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-arrow-trend-up fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No income activity found for the selected date range and filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($incomeAccounts->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="2" class="text-end pe-3 ps-3">Total Income:</td>
                        <td class="text-end pe-3 text-success">{{ number_format($total_income, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Expenses Section --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-arrow-trend-down me-2"></i> Expenses</span>
            <span class="badge bg-white text-dark px-3 py-2">
                {{ $expenseAccounts->count() }} account(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="60">Code</th>
                        <th>Account Name</th>
                        <th class="text-end pe-3" width="160">Net Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseAccounts as $row)
                        <tr>
                            <td class="text-center fw-bold text-info ps-3" style="font-size:0.72rem;">{{ $row->account_code }}</td>
                            <td class="fw-bold" style="font-size:0.72rem;">{{ $row->account_name }}</td>
                            <td class="text-end fw-bold pe-3 text-danger" style="font-size:0.78rem;">
                                {{ number_format($row->net, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-arrow-trend-down fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No expense activity found for the selected date range and filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($expenseAccounts->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="2" class="text-end pe-3 ps-3">Total Expense:</td>
                        <td class="text-end pe-3 text-danger">{{ number_format($total_expense, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Net Profit / (Loss) Banner --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 {{ $net_profit >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="fw-bold fs-5 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                <i class="fas fa-scale-balanced me-2"></i> Net Profit / (Loss)
            </div>
            <div class="fw-bold fs-4 {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format(abs($net_profit), 2) }}
                <small class="fs-6">{{ $net_profit >= 0 ? 'Profit' : '(Loss)' }}</small>
            </div>
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
