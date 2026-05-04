<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-success text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center shadow-sm">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">Accounting Periods</span>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">Showing - {{ $periods->total() }} items</span>
        <button class="btn btn-emerald text-white fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#periodModal" wire:click="resetFields">
            <i class="fas fa-plus me-1"></i> Create
        </button>
    </div>

    <!-- Period Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-dark text-white text-uppercase tiny fw-bold">
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>ID</th>
                        <th>Begin</th>
                        <th>End</th>
                        <th>Is Closed</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th class="pe-3">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white border-top-0">
                    <!-- High-Visibility Filter Row (Match Screenshot) -->
                    <tr class="bg-light bg-opacity-10 border-bottom">
                        <td></td>
                        <td class="p-1"><input type="text" wire:model.live="search_id" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1">
                            <div class="position-relative">
                                <input type="text" wire:model.live="search_begin" class="form-control form-control-sm border-0 bg-white text-center shadow-none">
                                <i class="fas fa-calendar-alt position-absolute end-0 top-50 translate-middle-y me-2 text-primary opacity-50 pointer-none"></i>
                            </div>
                        </td>
                        <td class="p-1">
                            <div class="position-relative">
                                <input type="text" wire:model.live="search_end" class="form-control form-control-sm border-0 bg-white text-center shadow-none">
                                <i class="fas fa-calendar-alt position-absolute end-0 top-50 translate-middle-y me-2 text-primary opacity-50 pointer-none"></i>
                            </div>
                        </td>
                        <td class="p-1"><input type="text" wire:model.live="search_closed" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td></td>
                    </tr>

                    @forelse($periods as $period)
                        <tr>
                            <td class="ps-3 text-muted small">{{ ($periods->currentPage() - 1) * $periods->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $period->id }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($period->start_date)->format('d-M-Y') }}</td>
                            <td class="small">{{ \Carbon\Carbon::parse($period->end_date)->format('d-M-Y') }}</td>
                            <td>
                                @if($period->is_closed)
                                    <span class="badge bg-danger rounded-pill px-3">Yes</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 fw-bold">No</span>
                                @endif
                            </td>
                            <td class="tiny text-muted">{{ $period->created_at->format('d-M-Y h:i:s A') }}</td>
                            <td class="tiny text-muted">{{ $period->updated_at->format('d-M-Y h:i:s A') }}</td>
                            <td class="small">{{ $period->creator->name ?? 'System' }}</td>
                            <td class="small">{{ $period->updater->name ?? 'System' }}</td>
                            <td class="pe-3">
                                <div class="btn-group btn-group-sm rounded shadow-sm overflow-hidden">
                                    <button wire:click="toggleStatus({{ $period->id }})" class="btn btn-primary" title="Details / Toggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button wire:click="edit({{ $period->id }})" data-bs-toggle="modal" data-bs-target="#periodModal" class="btn btn-success" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $period->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-5 text-muted">No accounting periods found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3">
            {{ $periods->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div wire:ignore.self class="modal fade" id="periodModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">{{ $editing_id ? 'Edit' : 'Create' }} Accounting Period</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Period Start Date</label>
                        <input type="date" wire:model="start_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Period End Date</label>
                        <input type="date" wire:model="end_date" class="form-control">
                    </div>
                    @if($editing_id)
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-switch" type="checkbox" wire:model="is_closed" id="closeSwitch">
                        <label class="form-check-label ms-2 small fw-bold" for="closeSwitch">Is Period Closed?</label>
                    </div>
                    @endif
                </div>
                <div class="modal-footer p-4 border-0">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="button" wire:click="save" class="btn btn-dark px-5 rounded-pill shadow-sm">
                        {{ $editing_id ? 'Update' : 'Generate' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

<style>
    .btn-emerald { background-color: #2a9d8f; }
    .btn-emerald:hover { background-color: #21867a; }
    .tiny { font-size: 0.7rem; }
</style>
</div>

