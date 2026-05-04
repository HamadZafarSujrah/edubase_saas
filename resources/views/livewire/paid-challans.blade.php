<div class="container-fluid min-vh-100">
    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 bg-white">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
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
                <div class="col-md-2">
                    <label class="tiny fw-bold text-muted ps-1">Year</label>
                    <input type="number" wire:model.live="year" class="form-control border-0 bg-light rounded-3 shadow-sm" placeholder="Year">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <a href="{{ route('finance.generate-challans') }}" class="btn btn-primary btn-sm rounded-3 px-3 flex-fill">
                        <i class="fas fa-magic me-1"></i> Generate Bills
                    </a>
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
        <div class="card-header bg-success py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white fw-bold"><i class="fas fa-check-circle me-2"></i> Paid Challans History</h6>
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
                                    <a href="{{ route('print-challans', ['month' => $challan->month, 'year' => $challan->year, 'search' => $challan->student->admission_no]) }}"
                                       target="_blank" class="btn btn-sm btn-light border-0 text-primary" title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button"
                                            onclick="Swal.fire({
                                                title: 'Revert Payment?',
                                                text: 'This will reverse the accounting entries and mark the challan as UNPAID.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc3545',
                                                cancelButtonColor: '#6c757d',
                                                confirmButtonText: 'Yes, revert it!'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    @this.deleteChallan({{ $challan->id }});
                                                }
                                            })"
                                            class="btn btn-sm btn-outline-danger border-0" title="Revert Payment">
                                        <i class="fas fa-undo-alt"></i> Revert
                                    </button>
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
    <style>
        .tiny { font-size: 0.75rem; }
    </style>
</div>

