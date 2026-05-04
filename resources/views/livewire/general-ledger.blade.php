<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-book me-2 text-success"></i> General Ledger</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">General Ledger</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                <i class="fas fa-print me-2"></i> Print
            </button>
            <button class="btn btn-outline-success rounded-pill px-4 btn-sm">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- Date Filter Card --}}
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
                <div class="col-md-3 d-flex gap-2">
                    <button wire:click="$refresh" class="btn btn-success rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-2"></i> Get Record
                    </button>
                    <button wire:click="resetFilters" class="btn btn-warning rounded-3 px-3 shadow-sm">
                        <i class="fas fa-undo me-1"></i> Fix Dates
                    </button>
                </div>
                <div class="col-md-3">
                    {{-- Summary Badges --}}
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 fw-bold">
                            <i class="fas fa-list me-1"></i> Showing: {{ number_format($total_count) }} items
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                    <i class="fas fa-balance-scale fs-5 text-warning"></i>
                </div>
                <div>
                    <div class="tiny text-muted fw-bold text-uppercase">Balance (Dr - Cr)</div>
                    @php $balance = $total_debit - $total_credit; @endphp
                    <div class="fw-bold fs-5 {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format(abs($balance), 2) }}
                        <small class="fs-6">{{ $balance >= 0 ? 'Dr' : 'Cr' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Ledger Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-book-open me-2"></i> Ledger Entries</span>
            <span class="badge bg-white text-dark px-3 py-2">
                {{ $entries->total() }} total records
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    {{-- Main Header Row --}}
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="45">No.</th>
                        <th class="text-center" width="55">ID</th>
                        <th width="120">Campus</th>
                        <th width="170">Trans ID</th>
                        <th class="text-center" width="100">Trans Date</th>
                        <th class="text-center" width="60">Code</th>
                        <th class="text-center" width="55">Line</th>
                        <th class="text-center" width="100">Amount</th>
                        <th width="160">Account Name</th>
                        <th class="text-center" width="55">DR/CR</th>
                        <th>Memo</th>
                        <th class="text-center" width="90">Created By</th>
                        <th class="text-center" width="140">Created At</th>
                    </tr>

                    {{-- Column Filter Row --}}
                    <tr style="background:#f1f5f9;">
                        <th></th>
                        <th></th>
                        <th class="p-1">
                            <select wire:model.live="filter_campus" class="form-select form-select-sm border-0 bg-white shadow-sm" style="font-size:0.7rem;">
                                <option value="">Campus</option>
                                @foreach($campuses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="p-1">
                            <input type="text" wire:model.live.debounce.300ms="filter_trans_id"
                                   class="form-control form-control-sm border-0 bg-white shadow-sm" placeholder="Trans ID..." style="font-size:0.7rem;">
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="p-1">
                            <input type="text" wire:model.live.debounce.300ms="filter_amount"
                                   class="form-control form-control-sm border-0 bg-white shadow-sm" placeholder="Amount..." style="font-size:0.7rem;">
                        </th>
                        <th class="p-1">
                            <input type="text" wire:model.live.debounce.300ms="filter_account"
                                   class="form-control form-control-sm border-0 bg-white shadow-sm" placeholder="Account Name..." style="font-size:0.7rem;">
                        </th>
                        <th class="p-1">
                            <select wire:model.live="filter_dr_cr" class="form-select form-select-sm border-0 bg-white shadow-sm" style="font-size:0.7rem;">
                                <option value="">All</option>
                                <option value="dr">Dr</option>
                                <option value="cr">Cr</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($entries as $i => $row)
                        @php
                            $isDr  = $row->debit > 0;
                            $amount = $isDr ? $row->debit : $row->credit;
                            $lineNo = $lineNumbers[$row->id] ?? '—';
                        @endphp
                        <tr class="{{ $isDr ? '' : 'table-light' }}">
                            <td class="text-center fw-bold text-muted ps-3">
                                {{ ($entries->currentPage() - 1) * $entries->perPage() + $loop->iteration }}
                            </td>
                            <td class="text-center text-muted">{{ $row->id }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1" style="font-size:0.68rem;">
                                    {{ $row->campus_name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary" style="font-size:0.72rem;">{{ $row->voucher_no }}</div>
                                <div class="text-muted" style="font-size:0.67rem; line-height:1.2;">{{ Str::limit($row->narration, 40) }}</div>
                            </td>
                            <td class="text-center fw-bold" style="font-size:0.72rem;">
                                {{ \Carbon\Carbon::parse($row->transaction_date)->format('d-M-Y') }}
                            </td>
                            <td class="text-center fw-bold text-info" style="font-size:0.72rem;">
                                {{ $row->account_code }}
                            </td>
                            <td class="text-center fw-bold text-muted" style="font-size:0.72rem;">
                                {{ $lineNo }}
                            </td>
                            <td class="text-end fw-bold pe-3" style="font-size:0.78rem;">
                                <span class="{{ $isDr ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($amount) }}
                                </span>
                            </td>
                            <td class="fw-bold" style="font-size:0.72rem;">{{ $row->account_name }}</td>
                            <td class="text-center">
                                @if($isDr)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size:0.68rem;">Dr</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1" style="font-size:0.68rem;">Cr</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:0.70rem;">{{ $row->item_memo }}</td>
                            <td class="text-center text-muted" style="font-size:0.68rem;">{{ $row->created_by_name ?? '—' }}</td>
                            <td class="text-center text-muted" style="font-size:0.67rem;">
                                {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-5">
                                <i class="fas fa-book-open fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted">No ledger entries found for the selected date range and filters.</p>
                                <small class="text-muted">Ledger entries are automatically created when a fee challan is paid.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Footer Totals --}}
                @if($entries->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background:#1e293b; color:#fff; font-size:0.78rem;">
                        <td colspan="7" class="text-end pe-3 ps-3">Page Totals:</td>
                        <td class="text-end pe-3">
                            <div class="text-success">Dr: {{ number_format($entries->sum('debit')) }}</div>
                            <div class="text-warning">Cr: {{ number_format($entries->sum('credit')) }}</div>
                        </td>
                        <td colspan="5"></td>
                    </tr>
                    <tr class="fw-bold" style="background:#0f172a; color:#fff; font-size:0.78rem;">
                        <td colspan="7" class="text-end pe-3 ps-3">Overall Totals (All Pages):</td>
                        <td class="text-end pe-3">
                            <div class="text-success">Dr: {{ number_format($total_debit) }}</div>
                            <div class="text-warning">Cr: {{ number_format($total_credit) }}</div>
                        </td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 py-3">
            {{ $entries->links() }}
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
