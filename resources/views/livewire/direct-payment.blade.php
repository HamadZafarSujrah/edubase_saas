<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-money-bill-wave me-2 text-primary"></i> Direct Payment</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Direct Payment</li>
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
                <label class="small fw-bold text-muted mb-1">Student <span class="text-danger">*</span></label>
                <input type="text" wire:model.live.debounce.300ms="student_search" placeholder="Search by name, adm no, or father name..." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                @error('student_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @if(count($suggested_students) > 0)
                    <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                        @foreach($suggested_students as $s)
                            <button type="button" wire:click="selectStudent({{ $s['id'] }})" class="list-group-item list-group-item-action">
                                <strong>{{ $s['first_name'] }} {{ $s['last_name'] }}</strong>
                                <span class="text-muted small"> — {{ $s['admission_no'] }} | Father: {{ $s['father_name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($selected_student)
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
                        <label class="small fw-bold text-muted mb-1">Discount / Bad Debt Account</label>
                        <select wire:model="discount_account_id" class="form-select border-0 bg-light shadow-sm">
                            <option value="">Select...</option>
                            @foreach($discount_accounts as $a) <option value="{{ $a->id }}">{{ $a->name }}</option> @endforeach
                        </select>
                        @error('discount_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" wire:model="paid_date" class="form-control border-0 bg-light shadow-sm">
                        @error('paid_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-1">Receipt No. (optional)</label>
                        <input type="text" wire:model="receipt_no" class="form-control border-0 bg-light shadow-sm" placeholder="Auto-generated if left blank">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-1">Notes</label>
                        <input type="text" wire:model="challan_notes" class="form-control border-0 bg-light shadow-sm">
                    </div>
                </div>

                @error('line_items') <div class="alert alert-danger border-0 py-2 mb-3">{{ $message }}</div> @enderror

                <label class="small fw-bold text-muted mb-1">Particulars</label>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th width="45%">Particular / Reason</th>
                            <th width="20%">Amount</th>
                            <th width="20%">Discount</th>
                            <th width="15%"><button type="button" wire:click="addLineItem" class="btn btn-success btn-sm w-100 rounded-0"><i class="fas fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($line_items as $index => $row)
                        <tr>
                            <td><input type="text" wire:model="line_items.{{ $index }}.name" class="form-control form-control-sm border-0 bg-light" placeholder="E.g. Admission Fee, Miscellaneous Charges"></td>
                            <td><input type="number" step="0.01" wire:model="line_items.{{ $index }}.amount" class="form-control form-control-sm border-0 bg-light"></td>
                            <td><input type="number" step="0.01" wire:model="line_items.{{ $index }}.discount" class="form-control form-control-sm border-0 bg-light"></td>
                            <td class="text-center bg-light">
                                @if(count($line_items) > 1)
                                    <button type="button" wire:click="removeLineItem({{ $index }})" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    <div class="text-end">
                        <div class="small text-muted">Payable: {{ number_format($this->totalPayable, 2) }} &nbsp; Discount: {{ number_format($this->totalDiscount, 2) }}</div>
                        <div class="fs-4 fw-bold text-success">Collecting: {{ number_format($this->totalPaid, 2) }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" wire:click="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                        <i class="fas fa-hand-holding-usd me-2"></i> Collect Payment
                    </button>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
                    Search and select a student above to record a direct payment.
                </div>
            @endif
        </div>
    </div>
</div>
