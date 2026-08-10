<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-balance-scale me-2 text-primary"></i> Balance Sheet</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Balance Sheet</li>
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
                    <label class="small fw-bold text-muted mb-1">As Of Date</label>
                    <input type="date" wire:model.live="as_of_date"
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
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 fw-bold">
                            <i class="fas fa-calendar-check me-1"></i> As of {{ \Carbon\Carbon::parse($as_of_date)->format('d-M-Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Summary Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="fas fa-coins fs-5 text-success"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Assets</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($total_assets, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="fas fa-file-invoice-dollar fs-5 text-danger"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Liabilities</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($total_liabilities, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                    <i class="fas fa-university fs-5 text-warning"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Total Equity</div>
                    <div class="fw-bold fs-5 text-warning">{{ number_format($total_equity, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="fas fa-chart-line fs-5 text-primary"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Net Profit / (Loss)</div>
                    <div class="fw-bold fs-5 {{ $net_income >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($net_income, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ASSETS --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-coins me-2"></i> Assets</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $assets->count() }} accounts</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="90">Code</th>
                        <th>Account Name</th>
                        <th class="text-end pe-3" width="180">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $row)
                        <tr>
                            <td class="text-center fw-bold text-info ps-3">{{ $row->code }}</td>
                            <td class="fw-bold">{{ $row->name }}</td>
                            <td class="text-end fw-bold pe-3 text-success">{{ number_format($row->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-coins fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No asset balances found as of the selected date.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($assets->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="2" class="text-end pe-3 ps-3">Total Assets</td>
                        <td class="text-end pe-3">{{ number_format($total_assets, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- LIABILITIES --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i> Liabilities</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $liabilities->count() }} accounts</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="90">Code</th>
                        <th>Account Name</th>
                        <th class="text-end pe-3" width="180">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liabilities as $row)
                        <tr>
                            <td class="text-center fw-bold text-info ps-3">{{ $row->code }}</td>
                            <td class="fw-bold">{{ $row->name }}</td>
                            <td class="text-end fw-bold pe-3 text-danger">{{ number_format($row->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No liability balances found as of the selected date.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($liabilities->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="2" class="text-end pe-3 ps-3">Total Liabilities</td>
                        <td class="text-end pe-3">{{ number_format($total_liabilities, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- EQUITY --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-university me-2"></i> Equity</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $equity->count() }} accounts</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="90">Code</th>
                        <th>Account Name</th>
                        <th class="text-end pe-3" width="180">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equity as $row)
                        <tr>
                            <td class="text-center fw-bold text-info ps-3">{{ $row->code }}</td>
                            <td class="fw-bold">{{ $row->name }}</td>
                            <td class="text-end fw-bold pe-3 text-warning">{{ number_format($row->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <p class="text-muted mb-0">No equity account balances found as of the selected date.</p>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Computed Current Period Profit / (Loss) line — not backed by a GL account --}}
                    <tr class="table-light">
                        <td class="text-center ps-3">&mdash;</td>
                        <td class="fw-bold">
                            Current Period Profit / (Loss)
                            <span class="badge bg-secondary bg-opacity-10 text-muted border ms-2" style="font-size:0.62rem;">computed</span>
                        </td>
                        <td class="text-end fw-bold pe-3 {{ $net_income >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($net_income, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="2" class="text-end pe-3 ps-3">Total Equity</td>
                        <td class="text-end pe-3">{{ number_format($total_equity, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- VALIDATION BANNER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4 text-center border-end">
                    <div class="tiny text-muted fw-bold text-uppercase mb-1">Total Assets</div>
                    <div class="fw-bold fs-5 text-dark">{{ number_format($total_assets, 2) }}</div>
                </div>
                <div class="col-md-4 text-center border-end">
                    <div class="tiny text-muted fw-bold text-uppercase mb-1">Total Liabilities + Equity</div>
                    <div class="fw-bold fs-5 text-dark">{{ number_format($total_liabilities_and_equity, 2) }}</div>
                </div>
                <div class="col-md-4 text-center">
                    @if($is_balanced)
                        <span class="badge bg-success px-4 py-2 fs-6">
                            <i class="fas fa-check-circle me-2"></i> Balanced
                        </span>
                    @else
                        <span class="badge bg-danger px-4 py-2 fs-6">
                            <i class="fas fa-exclamation-triangle me-2"></i> Out of Balance
                        </span>
                    @endif
                </div>
            </div>

            @if(!$is_balanced)
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mt-4 mb-0 d-flex align-items-center gap-3">
                    <i class="fas fa-triangle-exclamation fs-4"></i>
                    <div>
                        <div class="fw-bold">Balance Sheet does not balance.</div>
                        <div class="small">
                            Difference of <strong>{{ number_format(abs($difference), 2) }}</strong>
                            ({{ $difference > 0 ? 'Assets exceed Liabilities + Equity' : 'Liabilities + Equity exceed Assets' }}).
                            Please review journal entries for posting errors.
                        </div>
                    </div>
                </div>
            @endif
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
