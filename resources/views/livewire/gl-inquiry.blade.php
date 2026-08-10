<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-th me-2 text-primary"></i> GL Inquiry</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">GL Inquiry</li>
                </ol>
            </nav>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            <i class="fas fa-print me-2"></i> Print
        </button>
    </div>

    @if($accounts->count() === 0)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-list-ol fs-1 text-muted opacity-25 mb-3 d-block"></i>
                <p class="text-muted fw-bold mb-0">No GL accounts found. Create one in Chart of Accounts / GL Accounts first.</p>
            </div>
        </div>
    @else

        {{-- Filter Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">GL Account</label>
                        <select wire:model.live="account_id" class="form-select border-0 bg-light shadow-sm rounded-3">
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Start Date</label>
                        <input type="date" wire:model.live="start_date" class="form-control border-0 bg-light shadow-sm rounded-3">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">End Date</label>
                        <input type="date" wire:model.live="end_date" class="form-control border-0 bg-light shadow-sm rounded-3">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
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

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <i class="fas fa-door-open fs-5 text-primary"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Opening Balance</div>
                        <div class="fw-bold fs-5 text-primary">{{ number_format($opening_balance, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10">
                        <i class="fas fa-arrow-down fs-5 text-success"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Total Debit</div>
                        <div class="fw-bold fs-5 text-success">{{ number_format($total_debit, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                        <i class="fas fa-arrow-up fs-5 text-danger"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Total Credit</div>
                        <div class="fw-bold fs-5 text-danger">{{ number_format($total_credit, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                        <i class="fas fa-door-closed fs-5 text-warning"></i>
                    </div>
                    <div>
                        <div class="tiny text-muted fw-bold text-uppercase">Closing Balance</div>
                        <div class="fw-bold fs-5 {{ $closing_balance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($closing_balance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ledger Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3 d-flex justify-content-between align-items-center"
                 style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold">
                    <i class="fas fa-book-open me-2"></i>
                    @if($selectedAccount) {{ $selectedAccount->code }} - {{ $selectedAccount->name }} @endif
                </span>
                <span class="badge bg-white text-dark px-3 py-2">{{ count($rows) }} transaction(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3" width="100">Date</th>
                            <th width="150">Voucher No.</th>
                            <th>Memo</th>
                            <th class="text-end" width="140">Debit</th>
                            <th class="text-end" width="140">Credit</th>
                            <th class="text-end pe-3" width="150">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-light">
                            <td class="ps-3 fw-bold text-muted" colspan="5">Opening Balance</td>
                            <td class="text-end pe-3 fw-bold">{{ number_format($opening_balance, 2) }}</td>
                        </tr>

                        @forelse($rows as $row)
                            <tr>
                                <td class="ps-3" style="font-size:0.72rem;">{{ \Carbon\Carbon::parse($row['date'])->format('d-M-Y') }}</td>
                                <td class="fw-bold text-primary" style="font-size:0.72rem;">{{ $row['voucher_no'] }}</td>
                                <td class="text-muted" style="font-size:0.72rem;">{{ \Illuminate\Support\Str::limit($row['memo'], 50) }}</td>
                                <td class="text-end text-success fw-bold">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}</td>
                                <td class="text-end text-danger fw-bold">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}</td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-th fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No transactions found for this account in the selected date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                            <td colspan="3" class="text-end pe-3 ps-3">Closing Balance:</td>
                            <td class="text-end text-success">{{ number_format($total_debit, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($total_credit, 2) }}</td>
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
