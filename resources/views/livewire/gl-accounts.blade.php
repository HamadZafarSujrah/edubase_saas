<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-dark text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center shadow-sm" style="background-color: #2c3e50 !important;">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">GL Accounts</span>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">Showing - {{ $accounts->total() }} items</span>
        <button class="btn btn-emerald text-white fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#accountModal" wire:click="resetFields">
            <i class="fas fa-plus me-1"></i> Create
        </button>
    </div>

    <!-- Accounts Table -->
    <div class="card shadow-sm border-0 rounded-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center border">
                <thead class="bg-light text-muted tiny fw-bold text-uppercase border-bottom">
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>ID</th>
                        <th>Acc Code</th>
                        <th>Acc Name</th>
                        <th>Acc Group</th>
                        <th>Acc Class</th>
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
                        <td class="p-1"><input type="text" wire:model.live="search_code" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" wire:model.live="search_name" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" wire:model.live="search_group" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1">
                            <select wire:model.live="search_class" class="form-select form-select-sm border-0 shadow-none bg-white">
                                <option value="">Acc Class</option>
                                <option value="Assets">Assets</option>
                                <option value="Liabilities">Liabilities</option>
                                <option value="Equity">Equity</option>
                                <option value="Income">Income</option>
                                <option value="Expense">Expense</option>
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

                    @forelse($accounts as $account)
                        <tr class="border-bottom">
                            <td class="ps-3 text-muted small">{{ ($accounts->currentPage() - 1) * $accounts->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $account->id }}</td>
                            <td class="fw-bold text-primary">{{ $account->code }}</td>
                            <td class="text-start ps-4 fw-bold text-dark">{{ $account->name }}</td>
                            <td class="small">{{ $account->group->name ?? 'Un-grouped' }}</td>
                            <td class="small">{{ $account->group->account_class ?? 'N/A' }}</td>
                            <td>
                                @if(!$account->is_inactive)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3">YES</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3">NO</span>
                                @endif
                            </td>
                            <td class="tiny text-muted">{{ $account->created_at->format('Y-m-d H:i') }}</td>
                            <td class="tiny text-muted">{{ $account->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="small text-muted">{{ $account->creator->name ?? 'systemadmin' }}</td>
                            <td class="small text-muted text-truncate" style="max-width: 80px;">{{ $account->updater->name ?? 'systemadmin' }}</td>
                            <td class="pe-3">
                                <div class="btn-group btn-group-sm rounded shadow-sm overflow-hidden">
                                    <button wire:click="edit({{ $account->id }})" data-bs-toggle="modal" data-bs-target="#viewModal" class="btn btn-primary" title="View"><i class="fas fa-eye"></i></button>
                                    <button wire:click="edit({{ $account->id }})" data-bs-toggle="modal" data-bs-target="#accountModal" class="btn btn-success" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button wire:click="delete({{ $account->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="py-5 text-muted">No GL accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3 border-top">
            {{ $accounts->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal (Match Template exactly) -->
    <div wire:ignore.self class="modal fade" id="accountModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 p-4 pb-0 text-center">
                    <div class="w-100">
                        <h2 class="modal-title fw-bold fs-1" style="color: #333;">{{ $editing_id ? 'Edit' : 'Create' }} GL Account</h2>
                        <div class="mt-2">
                            <div class="small fw-bold text-dark">Reserved Codes:</div>
                            <div class="small text-muted">2000 - 2099</div>
                            <div class="small text-muted">4000 - 4099</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-4" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 pt-4">
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Account Code</label>
                        <input type="text" wire:model="code" class="form-control form-control-lg border shadow-none rounded-2">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Account Name</label>
                        <input type="text" wire:model="name" class="form-control form-control-lg border shadow-none rounded-2">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-5 fw-bold mb-1">Account Group</label>
                        <select wire:model="group_id" class="form-select form-select-lg border shadow-none rounded-2">
                            <option value="">Select Account Group</option>
                            @foreach($groups as $class => $groupList)
                                <optgroup label="{{ $class }}" class="bg-warning bg-opacity-25 py-2">
                                    @foreach($groupList as $grp)
                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
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
                            {{ $editing_id ? 'Update Account' : 'Create GL Account' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal (Details View) -->
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice me-2"></i>Account Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-3">
                                <div class="tiny text-muted fw-bold">LEDGER ACCOUNT</div>
                                <div class="h4 fw-bold text-primary mb-0">{{ $code }} - {{ $name }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm p-3 h-100">
                                <div class="tiny text-muted fw-bold">GROUP</div>
                                <div class="fw-bold">{{ $groups->flatten()->where('id', $group_id)->first()->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm p-3 h-100">
                                <div class="tiny text-muted fw-bold">CLASS</div>
                                <div class="fw-bold text-uppercase">{{ $groups->flatten()->where('id', $group_id)->first()->account_class ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold text-muted small border-bottom pb-2">AUDIT TRAIL</h6>
                        <div class="small d-flex justify-content-between text-muted py-2">
                            <span>Created By / At</span>
                            <span class="text-dark fw-bold">systemadmin / today</span>
                        </div>
                        <div class="small d-flex justify-content-between text-muted py-2">
                            <span>Owner Campus</span>
                            <span class="text-dark fw-bold">Main Campus</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-dark rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<style>
    .btn-emerald { background-color: #66bb6a; transition: 0.3s; }
    .btn-emerald:hover { background-color: #4caf50; transform: translateY(-1px); }
    .tiny { font-size: 0.7rem; }
    optgroup { font-weight: bold; color: #d63384; font-size: 0.9rem; }
</style>

<script>
    window.addEventListener('closeModal', event => {
        ['accountModal', 'viewModal'].forEach(id => {
            var el = document.getElementById(id);
            var modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });
    });
</script>
</div>

