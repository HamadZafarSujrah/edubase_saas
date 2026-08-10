<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2 text-success"></i> Add Other Income</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Add Other Income</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="row g-4">
        {{-- Entry Form --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <span class="text-white fw-bold"><i class="fas fa-coins me-2"></i> Record Other Income</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" wire:model="transaction_date" class="form-control border-0 bg-light shadow-sm">
                        @error('transaction_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Campus (optional)</label>
                        <select wire:model="campus_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">All / Not campus-specific</option>
                            @foreach($campuses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Income Category</label>
                        <select wire:model="income_account_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select income account...</option>
                            @foreach($income_accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                        @error('income_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @if($income_accounts->isEmpty())
                            <div class="text-warning small mt-1"><i class="fas fa-exclamation-triangle me-1"></i>No income accounts found. Create one in GL Accounts first.</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Received Into (Cash/Bank Account)</label>
                        <select wire:model="receiving_account_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select receiving account...</option>
                            @foreach($receiving_accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                        @error('receiving_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Amount</label>
                        <input type="number" step="0.01" min="0" wire:model="amount" class="form-control border-0 bg-light shadow-sm fw-bold text-success">
                        @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Description (optional)</label>
                        <textarea wire:model="narration" rows="2" class="form-control border-0 bg-light shadow-sm" placeholder="e.g. Canteen rent for July"></textarea>
                    </div>

                    <button wire:click="save" wire:loading.attr="disabled" class="btn btn-success w-100 fw-bold py-2 rounded-3 shadow-sm">
                        <i class="fas fa-save me-2"></i> Save Income
                    </button>
                </div>
            </div>
        </div>

        {{-- Recent Income --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Recent Other Income</span>
                    <span class="badge bg-white text-dark px-3 py-2">{{ $recent_income->total() }} total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                        <thead>
                            <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                                <th class="ps-3" width="100">Date</th>
                                <th width="130">Voucher</th>
                                <th>Category</th>
                                <th class="text-end pe-3" width="120">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_income as $entry)
                                @php $incomeLine = $entry->items->firstWhere('credit', '>', 0); @endphp
                                <tr>
                                    <td class="ps-3">{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d-M-Y') }}</td>
                                    <td class="fw-bold text-primary">{{ $entry->voucher_no }}</td>
                                    <td class="fw-bold">{{ $incomeLine?->account?->name ?? $entry->narration }}</td>
                                    <td class="text-end pe-3 fw-bold text-success">{{ number_format($incomeLine?->credit ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-hand-holding-usd fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No other income recorded yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    {{ $recent_income->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
