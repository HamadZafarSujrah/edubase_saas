<div class="container-fluid min-vh-100">
    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 bg-white">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="tiny fw-bold text-muted ps-1">Search Student / Bill #</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light rounded-3 px-3 shadow-sm" placeholder="Name, Adm No, or Bill #...">
                </div>

                <div class="col-md-2">
                    <label class="tiny fw-bold text-muted ps-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light rounded-3 shadow-sm text-capitalize">
                        <option value="">All Months</option>
                        @foreach($months as $m) <option value="{{ $m }}">{{ strtoupper($m) }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="tiny fw-bold text-muted ps-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light rounded-3 shadow-sm" placeholder="Year">
                </div>
                <div class="col-md-2">
                    <label class="tiny fw-bold text-muted ps-1">Student</label>
                    <select wire:model.live="student_status_filter" class="form-select border-0 bg-light rounded-3 shadow-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Alumni / Inactive</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <a href="{{ route('finance.generate-challans') }}" class="btn btn-primary btn-sm rounded-3 px-3 flex-fill">
                        <i class="fas fa-magic me-1"></i> Generate Bills
                    </a>
                    @if(count($selected_challans) > 0)
                        <a href="{{ route('print-challans', ['ids' => $selected_challans]) }}" target="_blank" class="btn btn-dark btn-sm rounded-3 px-3 flex-fill animate__animated animate__pulse">
                            <i class="fas fa-print me-1"></i> Print Selected ({{ count($selected_challans) }})
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 animate__animated animate__shakeX">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Challan List -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden shadow-lg mb-5">
        <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i> Pay or Print Fee Challans</h6>
            <div class="small text-white opacity-75">Showing Generated Items for {{ strtoupper($month) }} {{ $year }}</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="tiny fw-bold text-uppercase text-muted border-bottom">
                        <th class="ps-4" width="40px">
                            <div class="form-check">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                            </div>
                        </th>
                        <th>Student Name / Family</th>
                        <th>Class / Section</th>
                        <th>Bill #</th>
                        <th class="text-center">Month</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Payable</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($challans as $challan)
                        <tr>
                            <td class="ps-4">
                                <div class="form-check">
                                    <input type="checkbox" wire:model.live="selected_challans" value="{{ $challan->id }}" class="form-check-input">
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold small text-dark">{{ $challan->student->first_name }} {{ $challan->student->last_name }}</div>
                                <div class="tiny text-muted">Adm: {{ $challan->student->admission_no }} | Father: {{ $challan->student->father_name }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold text-primary">{{ $challan->student->schoolClass->name ?? 'N/A' }}</div>
                                <div class="tiny text-muted">{{ $challan->student->section?->name ?? 'No Section' }}</div>
                            </td>
                            <td class="small fw-bold">{{ $challan->challan_no }}</td>
                            <td class="text-center text-capitalize small">{{ $challan->month }}, {{ $challan->year }}</td>
                            <td class="text-center">
                                @if($challan->status == 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 tiny fw-bold">PAID</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 tiny fw-bold">UNPAID</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ number_format($challan->total_amount) }}
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                    @if($challan->status !== 'paid')
                                        <a href="{{ route('finance.pay-fee-challan', ['challan_id' => $challan->id]) }}"
                                           class="btn btn-sm btn-success border-0" title="Pay Now">
                                            <i class="fas fa-hand-holding-usd me-1"></i> Pay
                                        </a>
                                    @else
                                        <span class="btn btn-sm btn-light border-0 text-success fw-bold" title="Paid">
                                            <i class="fas fa-check-circle me-1"></i> Paid
                                        </span>
                                    @endif
                                    <a href="{{ route('print-challans', ['month' => $challan->month, 'year' => $challan->year, 'search' => $challan->student->admission_no]) }}"
                                       target="_blank" class="btn btn-sm btn-light border-0" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($challan->status !== 'paid')
                                        <a href="{{ route('finance.add-challan-amount', ['challan_id' => $challan->id]) }}" class="btn btn-sm btn-light border-0" title="Add Amount">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                    @endif
                                    @if($challan->status === 'paid' || $challan->receipt_no)
                                        <button type="button" wire:click="openVoidModal({{ $challan->id }})" class="btn btn-sm btn-outline-danger border-0" title="Void">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                onclick="Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: 'You are about to DELETE this challan. This action cannot be undone.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#dc3545',
                                                    cancelButtonColor: '#6c757d',
                                                    confirmButtonText: 'Yes, delete it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        @this.deleteChallan({{ $challan->id }});
                                                    }
                                                })"
                                                class="btn btn-sm btn-outline-danger border-0" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-search fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted">No generated challans found for this criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $challans->links() }}
        </div>
    </div>

    @if($isVoidModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-times-circle me-2"></i>Void Payment</h5>
            <button type="button" class="btn-close" wire:click="closeVoidModal"></button>
          </div>
          <div class="modal-body p-4">
              <p class="text-muted small">This will reverse the ledger entries for this payment and mark the challan unpaid again. This action is logged for the audit trail.</p>
              <div class="mb-3">
                  <label class="form-label fw-bold">Reason for Voiding <span class="text-danger">*</span></label>
                  <textarea class="form-control bg-light" rows="3" wire:model="void_reason" placeholder="E.g. Entered against the wrong student, duplicate payment, ..."></textarea>
                  @error('void_reason') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeVoidModal">Cancel</button>
            <button type="button" class="btn btn-danger px-4" wire:click="confirmVoid">Void Payment</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    <style>
        .tiny { font-size: 0.75rem; }
    </style>
</div>

