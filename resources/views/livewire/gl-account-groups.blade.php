<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-dark text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center shadow-sm" style="background-color: #2c3e50 !important;">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">GL Account Groups</span>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">Showing - {{ $groups->total() }} items</span>
        <button class="btn btn-emerald text-white fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#groupModal" wire:click="resetForm">
            <i class="fas fa-plus me-1"></i> Create
        </button>
    </div>

    <!-- Groups Table -->
    <div class="card shadow-sm border-0 rounded-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center border">
                <thead class="bg-light text-muted tiny fw-bold text-uppercase border-bottom">
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>ID</th>
                        <th>Group Name</th>
                        <th>Account Class</th>
                        <th>Active</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th class="pe-3">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <!-- Filter Row (Match Screenshot) -->
                    <tr class="bg-light bg-opacity-10 border-bottom">
                        <td></td>
                        <td class="p-1"><input type="text" wire:model.live="search_id" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" wire:model.live="search_name" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1">
                            <select wire:model.live="search_class" class="form-select form-select-sm border-0 shadow-none bg-white">
                                <option value="">Account Class</option>
                                <option value="asset">Assets</option>
                                <option value="liability">Liabilities</option>
                                <option value="equity">Equity</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </td>
                        <td class="p-1">
                            <select wire:model.live="search_inactive" class="form-select form-select-sm border-0 shadow-none bg-white">
                                <option value="">Active?</option>
                                <option value="0">Yes</option>
                                <option value="1">No</option>
                            </select>
                        </td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td></td>
                    </tr>

                    @forelse($groups as $group)
                        <tr class="border-bottom">
                            <td class="ps-3 text-muted small">{{ ($groups->currentPage() - 1) * $groups->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $group->id }}</td>
                            <td class="text-start ps-4 fw-bold text-dark">{{ $group->name }}</td>
                            <td class="small">{{ $group->account_class }}</td>
                            <td>
                                @if(!$group->is_inactive)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3">YES</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3">NO</span>
                                @endif
                            </td>
                            <td class="tiny text-muted">{{ $group->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="tiny text-muted">{{ $group->updated_at->format('Y-m-d H:i:s') }}</td>
                            <td class="small text-muted">{{ $group->creator->name ?? 'systemadmin' }}</td>
                            <td class="small text-muted">{{ $group->updater->name ?? 'systemadmin' }}</td>
                            <td class="pe-3">
                                <div class="btn-group btn-group-sm rounded shadow-sm overflow-hidden">
                                    <button wire:click="edit({{ $group->id }})" data-bs-toggle="modal" data-bs-target="#viewModal" class="btn btn-primary" title="View"><i class="fas fa-eye"></i></button>
                                    <button wire:click="edit({{ $group->id }})" data-bs-toggle="modal" data-bs-target="#groupModal" class="btn btn-success" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button wire:click="delete({{ $group->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-5 text-muted">No GL groups found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3 border-top">
            {{ $groups->links() }}
        </div>
    </div>

    <!-- View Modal -->
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Group Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @if($editing_id)
                                <div class="col-6">
                                    <label class="tiny text-muted fw-bold text-uppercase">Group Name</label>
                                    <p class="fs-5 fw-bold text-dark mb-0">{{ $name }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="tiny text-muted fw-bold text-uppercase">Account Class</label>
                                    <p class="fs-5 text-dark mb-0">{{ $account_class }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="tiny text-muted fw-bold text-uppercase">Status</label>
                                    <div>
                                        @if(!$is_inactive)
                                            <span class="badge bg-success rounded-pill px-3">ACTIVE</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3">INACTIVE</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="tiny text-muted fw-bold text-uppercase">Group ID</label>
                                    <p class="fs-5 text-dark mb-0">#{{ $editing_id }}</p>
                                </div>
                                @else
                                <div class="col-12 text-center py-4">
                                    <p class="text-muted mb-0">No group selected.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($editing_id)
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Audit History</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                    <i class="fas fa-user-plus text-primary small"></i>
                                </div>
                                <div>
                                    <div class="tiny text-muted">Created By / At</div>
                                    <div class="small fw-bold text-dark">{{ \App\Models\Finance\GLAccountGroup::find($editing_id)->creator->name ?? 'System' }}</div>
                                    <div class="tiny text-muted">{{ \App\Models\Finance\GLAccountGroup::find($editing_id)->created_at ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fas fa-user-edit text-success small"></i>
                                </div>
                                <div>
                                    <div class="tiny text-muted">Last Updated By / At</div>
                                    <div class="small fw-bold text-dark">{{ \App\Models\Finance\GLAccountGroup::find($editing_id)->updater->name ?? 'System' }}</div>
                                    <div class="tiny text-muted">{{ \App\Models\Finance\GLAccountGroup::find($editing_id)->updated_at ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-dark rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal (Match Template Layout) -->
    <div wire:ignore.self class="modal fade" id="groupModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 p-4 pb-0">
                    <h2 class="modal-title fw-bold text-center w-100 fs-1" style="color: #333;">{{ $editing_id ? 'Edit' : 'Create' }} GL Group</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 pt-4">
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Group Name</label>
                        <input type="text" wire:model="name" class="form-control form-control-lg border shadow-none rounded-2">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Account Class</label>
                        <select wire:model="account_class" class="form-select form-select-lg border shadow-none rounded-2">
                            <option value="">Select a Group Class</option>
                            <option value="asset">Assets</option>
                            <option value="liability">Liabilities</option>
                            <option value="equity">Equity</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Active</label>
                        <select wire:model="is_inactive" class="form-select form-select-lg border shadow-none rounded-2">
                            <option value="0">Yes</option>
                            <option value="1">No</option>
                        </select>
                    </div>

                    <div class="text-center mt-5">
                        <button type="button" wire:click="save" class="btn btn-emerald text-white px-5 py-2 fs-5 rounded-1 shadow-sm">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    .btn-emerald { background-color: #66bb6a; transition: 0.3s; }
    .btn-emerald:hover { background-color: #4caf50; transform: translateY(-1px); }
    .tiny { font-size: 0.7rem; }
</style>

<script>
    window.addEventListener('closeModal', event => {
        var myModalEl = document.getElementById('groupModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) {
            modal.hide();
        }
    });
</script>
</div>

