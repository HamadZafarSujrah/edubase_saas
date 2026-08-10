<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-plus-circle me-2 text-primary"></i> Add Amount in Generated Challan</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.pay-print-challans') }}" class="text-decoration-none">Manage Challans</a></li>
                    <li class="breadcrumb-item active">Add Amount</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="small text-muted">Student</div>
                    <div class="fw-bold">{{ $challan->student->first_name }} {{ $challan->student->last_name }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Bill #</div>
                    <div class="fw-bold">{{ $challan->challan_no }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Current Total</div>
                    <div class="fw-bold text-primary fs-5">{{ number_format($challan->total_amount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h6 class="fw-bold text-primary mb-0">Existing Items</h6>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th class="ps-4">Particular</th><th class="text-end pe-4">Amount</th></tr>
                </thead>
                <tbody>
                    @foreach($challan->items as $item)
                        <tr>
                            <td class="ps-4">{{ $item->particular_name }}</td>
                            <td class="text-end pe-4">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h6 class="fw-bold text-primary mb-0">Add New Amount</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-1">Particular / Reason <span class="text-danger">*</span></label>
                    <input type="text" wire:model="particular_name" class="form-control border-0 bg-light shadow-sm" placeholder="E.g. Late Fee, Field Trip Charges">
                    @error('particular_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" wire:model="amount" class="form-control border-0 bg-light shadow-sm">
                    @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="addAmount" class="btn btn-primary w-100 fw-bold rounded-3 shadow-sm">
                        <i class="fas fa-plus me-2"></i> Add Amount
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
