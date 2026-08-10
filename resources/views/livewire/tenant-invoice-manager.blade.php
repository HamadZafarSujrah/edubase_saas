<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Platform Admin — Subscription Invoices</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('platform.tenants') }}" class="text-decoration-none">Institutions</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button wire:click="generateMonthlyInvoices" wire:loading.attr="disabled" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-bolt me-1"></i> Generate This Month's Invoices
            </button>
            <button wire:click="openModal" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> New Invoice</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <select wire:model.live="tenant_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Institutions</option>
                        @foreach($tenants as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="void">Void</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice #</th>
                            <th>Institution</th>
                            <th>Period</th>
                            <th class="text-end">Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td class="ps-4 small fw-bold">{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->tenant->name ?? '—' }}</td>
                            <td class="small">{{ $invoice->billing_period_start->format('d-M') }} — {{ $invoice->billing_period_end->format('d-M-Y') }}</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->amount, 2) }}</td>
                            <td class="small">{{ $invoice->due_date->format('d-M-Y') }}</td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Paid</span>
                                @elseif($invoice->status === 'void')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Void</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($invoice->status === 'unpaid')
                                    <button wire:click="openPayModal({{ $invoice->id }})" class="btn btn-sm btn-light text-success border" title="Mark Paid"><i class="fas fa-check-circle"></i></button>
                                    <button onclick="confirm('Void this invoice?') || event.stopImmediatePropagation()" wire:click="voidInvoice({{ $invoice->id }})" class="btn btn-sm btn-light text-danger border" title="Void"><i class="fas fa-times-circle"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Invoices Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $invoices->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice text-primary me-2"></i>New Invoice</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="row g-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Institution <span class="text-danger">*</span></label>
                      <select class="form-select bg-light" wire:model.live="tenant_id">
                          <option value="">Select...</option>
                          @foreach($tenants as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                      </select>
                      @error('tenant_id') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Plan</label>
                      <select class="form-select bg-light" wire:model="plan_id">
                          <option value="">None</option>
                          @foreach($plans as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" class="form-control bg-light" wire:model="amount">
                      @error('amount') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Period Start <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light" wire:model="billing_period_start">
                      @error('billing_period_start') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Period End <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light" wire:model="billing_period_end">
                      @error('billing_period_end') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Due Date <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light" wire:model="due_date">
                      @error('due_date') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Notes</label>
                      <input type="text" class="form-control bg-light" wire:model="notes">
                  </div>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if($isPayModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold text-success"><i class="fas fa-check-circle me-2"></i>Mark Invoice as Paid</h5>
            <button type="button" class="btn-close" wire:click="closePayModal"></button>
          </div>
          <div class="modal-body p-4">
              <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
              <select class="form-select bg-light" wire:model="payment_method">
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="cheque">Cheque</option>
                  <option value="cash">Cash</option>
                  <option value="other">Other</option>
              </select>
              @error('payment_method') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closePayModal">Cancel</button>
            <button type="button" class="btn btn-success px-4" wire:click="markPaid">Confirm Payment</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
