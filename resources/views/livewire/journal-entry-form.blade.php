<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb / Navigator -->
    <div class="bg-success text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">Journal Entry</span>
    </div>

    <!-- Main Header -->
    <div class="text-center bg-warning py-3 rounded-2 shadow-sm mb-4">
        <h2 class="fw-bold mb-0 text-dark" style="letter-spacing: 1px;">Journal Entry</h2>
    </div>

    @if(session()->has('error'))
        <div class="alert alert-danger shadow-sm border-0 mb-4 animate__animated animate__shakeX">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <div class="card-body p-4 bg-white">
            <!-- Top Controls -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 offset-md-2">
                    <label class="form-label small fw-bold text-muted">Transaction Date</label>
                    <div class="input-group">
                        <input type="date" wire:model="transaction_date" class="form-control bg-light border-0 shadow-none">
                        <span class="input-group-text bg-primary text-white border-0"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Campus</label>
                    <select wire:model="campus_id" class="form-select bg-light border-0 shadow-none">
                        <option value="">Select Campus</option>
                        @foreach($campuses as $campus) <option value="{{ $campus->id }}">{{ $campus->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            <!-- Hint Bar -->
            <div class="text-center bg-danger text-white py-2 mb-0 rounded-top" style="font-size: 1.1rem; font-weight: 500;">
                You can add new row by pressing "ENTER" key
            </div>

            <!-- Journal Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th style="width: 35%;">Account</th>
                            <th style="width: 15%;">Debit</th>
                            <th style="width: 15%;">Credit</th>
                            <th style="width: 25%;">Memo</th>
                            <th style="width: 10%;">
                                <button type="button" wire:click="addRow" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="fas fa-plus me-1"></i> Add More
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($items as $index => $item)
                        <tr>
                            <td class="p-2">
                                <select wire:model="items.{{ $index }}.gl_account_id" class="form-select border-0 shadow-none bg-light">
                                    <option value="">Select an Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" wire:model.lazy="items.{{ $index }}.debit" wire:change="calculateTotals" 
                                       class="form-control text-center border-0 shadow-none bg-light fw-bold" placeholder="0">
                            </td>
                            <td class="p-2">
                                <input type="number" wire:model.lazy="items.{{ $index }}.credit" wire:change="calculateTotals" 
                                       class="form-control text-center border-0 shadow-none bg-light fw-bold" placeholder="0">
                            </td>
                            <td class="p-2">
                                <textarea wire:model="items.{{ $index }}.memo" rows="1" 
                                          class="form-control border-0 shadow-none bg-light py-1" placeholder="Memo..."></textarea>
                            </td>
                            <td class="text-center p-2">
                                @if(count($items) > 2)
                                <button type="button" wire:click="removeRow({{ $index }})" class="btn btn-danger btn-sm rounded-circle">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-opacity-10 bg-danger fw-bold">
                        <tr style="background-color: #f8d7da;">
                            <td class="ps-4">Total</td>
                            <td class="text-center text-primary fs-5">{{ number_format($total_debit, 2) }}</td>
                            <td class="text-center text-primary fs-5">{{ number_format($total_credit, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Narration -->
            <div class="mt-4 row">
                <div class="col-md-12">
                    <label class="form-label small fw-bold text-muted">General Narration / Description</label>
                    <textarea wire:model="narration" class="form-control bg-light border-0 shadow-none" rows="3" placeholder="Enter general description for this voucher..."></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-5 d-flex justify-content-center gap-3 pb-3">
                <button wire:click="save" class="btn btn-success px-5 py-2 fw-bold rounded-pill shadow-sm">
                    <i class="fas fa-save me-2"></i> Save
                </button>
                <button class="btn btn-danger px-5 py-2 fw-bold rounded-pill shadow-sm">
                    <i class="fas fa-print me-2"></i> Save & Print
                </button>
            </div>
        </div>
    </div>
</div>
