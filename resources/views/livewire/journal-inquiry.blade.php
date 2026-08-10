<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-search-dollar me-2 text-primary"></i> Journal Inquiry</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Journal Inquiry</li>
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
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Start Date</label>
                    <input type="date" wire:model.live="start_date" class="form-control border-0 bg-light shadow-sm rounded-3">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">End Date</label>
                    <input type="date" wire:model.live="end_date" class="form-control border-0 bg-light shadow-sm rounded-3">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Search Voucher / Narration</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm rounded-3" placeholder="e.g. RCPT-2026 or student name...">
                </div>
                <div class="col-md-2">
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
            </div>
        </div>
    </div>

    {{-- Entries Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-book me-2"></i> Journal Entries</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $entries->total() }} total</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="text-center ps-3" width="40"></th>
                        <th width="150">Voucher No.</th>
                        <th class="text-center" width="100">Date</th>
                        <th width="120">Campus</th>
                        <th>Narration</th>
                        <th class="text-end" width="120">Debit</th>
                        <th class="text-end" width="120">Credit</th>
                        <th class="text-center" width="90">Balanced</th>
                        <th class="text-center pe-3" width="100">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        @php
                            $isBalanced = abs($entry->total_debit - $entry->total_credit) <= 0.01;
                        @endphp
                        <tr wire:click="toggleExpand({{ $entry->id }})" style="cursor:pointer;" class="{{ $expanded_entry_id === $entry->id ? 'table-primary' : '' }}">
                            <td class="text-center ps-3">
                                <i class="fas fa-chevron-{{ $expanded_entry_id === $entry->id ? 'down' : 'right' }} text-muted"></i>
                            </td>
                            <td class="fw-bold text-primary">{{ $entry->voucher_no }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d-M-Y') }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1" style="font-size:0.68rem;">
                                    {{ $entry->campus->name ?? '—' }}
                                </span>
                            </td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($entry->narration, 60) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($entry->total_debit, 2) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($entry->total_credit, 2) }}</td>
                            <td class="text-center">
                                @if($isBalanced)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle text-danger" title="Debit/Credit mismatch"></i>
                                @endif
                            </td>
                            <td class="text-center pe-3 text-muted">{{ $entry->creator->name ?? '—' }}</td>
                        </tr>
                        @if($expanded_entry_id === $entry->id)
                            <tr>
                                <td colspan="9" class="p-0" style="background:#f8fafc;">
                                    <table class="table table-sm mb-0" style="font-size:0.75rem;">
                                        <thead class="text-uppercase text-muted" style="font-size:0.68rem;">
                                            <tr>
                                                <th class="ps-4" width="100">Code</th>
                                                <th width="220">Account</th>
                                                <th>Memo</th>
                                                <th class="text-end" width="120">Debit</th>
                                                <th class="text-end pe-4" width="120">Credit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($entry->items as $line)
                                                <tr>
                                                    <td class="ps-4 text-info fw-bold">{{ $line->account->code ?? '—' }}</td>
                                                    <td class="fw-bold">{{ $line->account->name ?? '—' }}</td>
                                                    <td class="text-muted">{{ $line->item_memo }}</td>
                                                    <td class="text-end text-success">{{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                                                    <td class="text-end pe-4 text-danger">{{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-search-dollar fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted">No journal entries found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
