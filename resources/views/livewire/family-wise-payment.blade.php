<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users me-2 text-primary"></i> Family-Wise Payment</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Family-Wise Payment</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="position-relative mb-3">
                <label class="small fw-bold text-muted mb-1">Family <span class="text-danger">*</span></label>
                <input type="text" wire:model.live.debounce.300ms="family_search" placeholder="Search by father name, family no, or guardian name..." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                @if(count($suggested_families) > 0)
                    <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                        @foreach($suggested_families as $f)
                            <button type="button" wire:click="selectFamily({{ $f['id'] }})" class="list-group-item list-group-item-action">
                                <strong>{{ $f['father_name'] ?: $f['guardian_name'] }}</strong>
                                <span class="text-muted small"> — Family No. {{ $f['family_no'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($selected_family)
                @error('rows') <div class="alert alert-danger border-0 py-2 mb-3">{{ $message }}</div> @enderror

                <table class="table table-sm align-middle mb-3">
                    <thead>
                        <tr>
                            <th width="10%"></th>
                            <th width="35%">Student</th>
                            <th width="25%">Bill #</th>
                            <th width="30%" class="text-end">Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $studentId => $row)
                        <tr>
                            <td>
                                @if($row['challan_id'] && $row['remaining'] > 0)
                                    <input type="checkbox" wire:model="rows.{{ $studentId }}.selected" class="form-check-input">
                                @endif
                            </td>
                            <td class="fw-bold">{{ $row['name'] }}</td>
                            <td class="text-muted small">{{ $row['challan_no'] ?? 'No outstanding bill' }}</td>
                            <td class="text-end fw-bold {{ $row['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($row['remaining'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No students found in this family.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Payment Receiving Account <span class="text-danger">*</span></label>
                        <select wire:model="receiving_account_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select...</option>
                            @foreach($receiving_accounts as $a) <option value="{{ $a->id }}">{{ $a->name }}</option> @endforeach
                        </select>
                        @error('receiving_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" wire:model="paid_date" class="form-control border-0 bg-light shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Receipt No. (optional)</label>
                        <input type="text" wire:model="receipt_no" class="form-control border-0 bg-light shadow-sm">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="fs-5 fw-bold text-success">Total Collecting: {{ number_format($this->totalSelected, 2) }}</div>
                    <button type="button" wire:click="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-hand-holding-usd me-2"></i> Collect Payment
                    </button>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
                    Search and select a family above to pay all siblings' outstanding balances in one transaction.
                </div>
            @endif
        </div>
    </div>
</div>
