<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm border-start border-primary border-5">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark">Pay or Print Fee Challans</h2>
            <p class="text-muted small mb-0">Track payments, print receipts, and manage student accounts.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark shadow-sm"><i class="fas fa-file-pdf me-1"></i> Bulk Print Selected</button>
            <a href="/generate-challans" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> New Billing Cycle</a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted ps-1">Search Student</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light rounded-pill px-3" placeholder="Name or Adm No...">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted ps-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light rounded-pill">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted ps-1">Month</label>
                    <select wire:model.live="month" class="form-select border-0 bg-light rounded-pill text-capitalize">
                        <option value="">Any Month</option>
                        @foreach($months as $m) <option value="{{ $m }}">{{ $m }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted ps-1">Status</label>
                    <select wire:model.live="status" class="form-select border-0 bg-light rounded-pill">
                        <option value="">All Status</option>
                        <option value="pending" class="text-warning">Pending</option>
                        <option value="paid" class="text-success">Paid</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$reset" class="btn btn-light w-100 rounded-pill border">Reset</button>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm animate__animated animate__shakeX">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Lists -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Challan Info</th>
                        <th>Student Details</th>
                        <th>Total Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($challans as $c)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $c->challan_no }}</div>
                                <div class="tiny text-muted text-uppercase">{{ $c->month }} {{ $c->year }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $c->student->first_name }} {{ $c->student->last_name }}</div>
                                <div class="small text-muted">{{ $c->student->admission_no }} | {{ $c->student->campus->name }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ number_format($c->total_amount, 2) }}</div>
                            </td>
                            <td>
                                <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i> {{ $c->due_date }}</span>
                            </td>
                            <td>
                                @if($c->status == 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><i class="fas fa-check me-1"></i> Paid</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3"><i class="fas fa-clock me-1"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button title="Print Challan" class="btn btn-outline-dark btn-sm rounded-circle me-1"><i class="fas fa-print"></i></button>
                                    @if($c->status != 'paid')
                                        <button wire:click="markAsPaid({{ $c->id }})" title="Receive Payment" class="btn btn-outline-success btn-sm rounded-circle me-1"><i class="fas fa-hand-holding-usd"></i></button>
                                    @endif
                                    <button onclick="confirm('Void this challan?') || event.stopImmediatePropagation()" wire:click="deleteChallan({{ $c->id }})" title="Void" class="btn btn-outline-danger btn-sm rounded-circle"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 80px;" class="mb-3 opacity-25">
                                <p class="text-muted">No challans found for this criteria.</p>
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
</div>
