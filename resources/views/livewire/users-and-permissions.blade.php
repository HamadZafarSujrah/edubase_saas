<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-dark text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center shadow-sm" style="background-color: #2c3e50 !important;">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">Users & Permissions</span>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Global System Settings (Admin Only) -->
    @if(auth()->check() && auth()->user()->roles->whereIn('name', ['Supper Admin', 'Super Admin', 'Senior Developer', 'Developer'])->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4 rounded-3 animate__animated animate__fadeInDown">
        <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #34495e !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-cogs me-2 fs-5"></i>
                <div>
                    <h6 class="mb-0 fw-bold">System Configuration</h6>
                    <small class="opacity-75 tiny">Define which GL accounts are visible for user permissions across this tenant.</small>
                </div>
            </div>
            <button wire:click="saveGlobalSettings" class="btn btn-warning btn-sm fw-bold px-3 shadow-sm">
                <i class="fas fa-save me-1"></i> Save Global Configuration
            </button>
        </div>
        <div class="card-body p-4 bg-white">
            <div class="row align-items-end g-3">
                <div class="col-md-9" x-data="{ open: false, searchQuery: '' }">
                    <label class="form-label small fw-bold text-dark mb-1">Whitelist Cash/Bank Accounts for Permissions Table</label>
                    
                    <!-- Searchable Multi-Select Dropdown (Alpine.js Version) -->
                    <div class="position-relative">
                        <div class="form-control d-flex flex-wrap gap-1 align-items-center p-2 shadow-sm border-secondary-subtle" 
                             style="min-height: 45px; cursor: pointer; background: #fff;" 
                             @click="open = !open">
                            @forelse($global_cash_accounts as $selectedId)
                                @php $acc = $all_system_accounts->find($selectedId); @endphp
                                @if($acc)
                                    <span class="badge bg-primary d-flex align-items-center gap-1 rounded-pill px-2 py-1">
                                        <i class="fas fa-university tiny"></i> {{ $acc->name }}
                                        <i class="fas fa-times cursor-pointer ms-1 tiny" wire:click.stop="$set('global_cash_accounts', {{ json_encode(array_values(array_diff($global_cash_accounts, [$selectedId]))) }})"></i>
                                    </span>
                                @endif
                            @empty
                                <span class="text-muted small">Select accounts to whitelist...</span>
                            @endforelse
                        </div>

                        <!-- The actual dropdown list -->
                        <div class="position-absolute shadow-lg border-0 rounded-3 p-3 mt-1 bg-white" 
                             style="width: 100%; max-height: 350px; overflow-y: auto; z-index: 1060 !important; display: none;" 
                             x-show="open" 
                             x-transition
                             @click.away="open = false">
                            
                            <!-- Local Search Bar -->
                            <div class="mb-3 sticky-top bg-white pt-1">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 shadow-none" placeholder="Search accounts..." x-model="searchQuery">
                                </div>
                            </div>

                            <!-- Accounts List -->
                            <div class="row g-2">
                                @foreach($all_system_accounts as $acc)
                                <div class="col-12" x-show="!searchQuery || '{{ strtolower($acc->name) }}'.includes(searchQuery.toLowerCase())">
                                    <div class="form-check form-switch p-2 border rounded-3 hover-bg-light transition-all cursor-pointer" 
                                         wire:key="system-acc-{{ $acc->id }}">
                                        <input class="form-check-input ms-0 me-2 cursor-pointer" type="checkbox" 
                                               wire:model="global_cash_accounts" value="{{ $acc->id }}" 
                                               id="global_acc_{{ $acc->id }}">
                                        <label class="form-check-label fw-bold small cursor-pointer w-100" for="global_acc_{{ $acc->id }}">
                                            {{ $acc->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-info border-0 mb-0 py-2 small ps-3">
                        <i class="fas fa-info-circle me-1"></i> Selective accounts will simplify the user creation form for staff.
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Toolbar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">Showing - {{ $users->total() }} items</span>
        <button class="btn btn-emerald text-white fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal" wire:click="resetFields">
            <i class="fas fa-plus me-1"></i> Create
        </button>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm border-0 rounded-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center border">
                <thead class="bg-light text-muted tiny fw-bold text-uppercase border-bottom">
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th class="pe-3">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <!-- Filter Row -->
                    <tr class="bg-light bg-opacity-10 border-bottom">
                        <td></td>
                        <td class="p-1"><input type="text" wire:model.live="search_id" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" wire:model.live="search_username" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1">
                            <select wire:model.live="search_status" class="form-select form-select-sm border-0 shadow-none bg-white">
                                <option value="">Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </td>
                        <td class="p-1">
                            <select wire:model.live="search_role" class="form-select form-select-sm border-0 shadow-none bg-white">
                                <option value="">Role</option>
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td class="p-1"><input type="text" class="form-control form-control-sm border-0 bg-warning bg-opacity-10 text-center shadow-none"></td>
                        <td></td>
                    </tr>

                    @forelse($users as $user)
                        <tr class="border-bottom" wire:key="user-row-{{ $user->id }}">
                            <td class="ps-3 text-muted small">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $user->id }}</td>
                            <td class="text-start ps-4 fw-bold text-dark">{{ $user->username ?: $user->name }}</td>
                            <td class="text-capitalize small fw-bold text-{{ $user->status == 'active' ? 'success' : 'danger' }}">{{ $user->status }}</td>
                            <td class="small">
                                @forelse($user->roles as $r)
                                    <span class="badge bg-primary bg-opacity-10 text-primary tiny" wire:key="user-{{ $user->id }}-role-{{ $r->id }}">{{ $r->name }}</span>
                                @empty
                                    <span class="text-muted tiny">No Role</span>
                                @endforelse
                            </td>
                            <td class="tiny text-muted">{{ $user->created_at->format('d-M-Y H:i A') }}</td>
                            <td class="tiny text-muted">{{ $user->updated_at->format('d-M-Y H:i A') }}</td>
                            <td class="small text-muted">{{ $user->created_by ? 'Admin' : 'System' }}</td>
                            <td class="small text-muted">{{ $user->updated_by ? 'Admin' : 'System' }}</td>
                            <td class="pe-3">
                                <div class="d-flex flex-column gap-1 mb-2">
                                    <a href="/assign-user-permissions/{{ $user->id }}" class="btn btn-emerald-dark btn-sm tiny text-white fw-bold py-1 text-decoration-none">Manage Permissions</a>
                                    <button class="btn btn-primary btn-sm tiny text-white fw-bold py-1">Change Password</button>
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-danger-dark btn-sm tiny text-white fw-bold py-1 text-uppercase">Change Status or Role</button>
                                </div>
                                <div class="btn-group btn-group-sm rounded shadow-sm">
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#viewModal" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></button>
                                    <button wire:click="delete({{ $user->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-5 text-muted">No user accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3 border-top">
            {{ $users->links() }}
        </div>
    </div>

    <!-- User Management Modal -->
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-xl shadow">
            <div class="modal-content border-0 rounded-3">
                <div class="modal-header border-0 pb-0 pe-4 pt-4">
                    <div class="w-100 text-center">
                        <h5 class="fw-bold text-dark">Please fill out the following fields to {{ $editing_id ? 'Update' : 'Add' }} a User:</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 pt-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">
                                Username @if($editing_id && !empty($username)) <i class="fas fa-lock tiny text-muted ms-1" title="Permanent ID"></i> @endif
                            </label>
                            <input type="text" wire:model="username" class="form-control border shadow-none {{ ($editing_id && !empty($username)) ? 'bg-light opacity-75' : '' }}" {{ ($editing_id && !empty($username)) ? 'readonly' : '' }} placeholder="e.g. admin_01">
                            @error('username') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Full Name</label>
                            <input type="text" wire:model="name" class="form-control border shadow-none" placeholder="e.g. Hamad Zafar">
                            @error('name') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">Email Address</label>
                            <input type="email" wire:model="email" class="form-control border shadow-none" placeholder="e.g. hamad@pakcr.org.pk">
                            @error('email') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">Contact Number</label>
                            <input type="text" wire:model="contact" class="form-control border shadow-none" placeholder="e.g. 0300-1234567">
                            @error('contact') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">Password</label>
                            <input type="password" wire:model="password" class="form-control border shadow-none" placeholder="{{ $editing_id ? 'Leave blank to keep current' : 'Enter Password' }}">
                            @error('password') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">User Level (Power In Integer)</label>
                            <select wire:model="power_level" class="form-select border shadow-none">
                                <option value="">Select Level</option>
                                <option value="1">Level 1 (Full Access)</option>
                                <option value="2">Level 2 (Management)</option>
                                <option value="3">Level 3 (Staff)</option>
                            </select>
                            @error('power_level') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-danger mb-0">Assigned Roles (Multi-select)</label>
                                <a href="{{ route('roles.manage') }}" class="tiny fw-bold text-primary text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i> Manage System Roles
                                </a>
                            </div>
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                            <div class="w-100" wire:ignore x-data="{
                                initSelect2() {
                                    if(typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
                                        let script = document.createElement('script');
                                        script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                                        script.onload = () => setTimeout(() => this.initSelect2(), 100);
                                        document.head.appendChild(script);
                                        return;
                                    }
                                    let select = jQuery(this.$refs.select);
                                    select.select2({
                                        placeholder: 'Select Roles...',
                                        width: '100%',
                                        allowClear: true,
                                        dropdownParent: jQuery(this.$el)
                                    });
                                    select.on('change', () => {
                                        $wire.set('user_roles', select.val() || []);
                                    });
                                    
                                    /* When user clicks Edit, update the UI input with saved roles */
                                    $wire.on('user-edit-loaded', (event) => {
                                        select.val(event.roles).trigger('change.select2');
                                    });
                                }
                            }" x-init="initSelect2()">
                                <select x-ref="select" class="form-select w-100 border border-light-subtle shadow-sm bg-white" multiple="multiple">
                                    @foreach($system_roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('user_roles') <div class="text-danger tiny mt-1 fw-bold">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="row g-4">
                        <!-- Manage Campuses Access -->
                        <div class="col-md-5">
                            <div class="card border h-100 shadow-none rounded-0">
                                <div class="card-header bg-white border-bottom py-2 text-center text-primary fw-bold">
                                    Manage Campuses Access
                                </div>
                                <div class="table-responsive p-0" style="max-height: 300px;">
                                    <table class="table table-sm table-bordered mb-0 align-middle text-center">
                                        <thead class="bg-light tiny fw-bold border-bottom">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th>Campus Name</th>
                                                <th width="20%">Allowed/Not</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campuses as $campus)
                                            <tr>
                                                <td class="small">{{ $loop->iteration }}</td>
                                                <td class="text-start ps-3 small fw-bold">{{ $campus->name }}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input type="checkbox" wire:model="allowed_campuses.{{ $campus->id }}" class="form-check-input border-secondary shadow-none">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- User Preferences & Accounts -->
                        <div class="col-md-7">
                            <div class="card border shadow-none rounded-0 mb-3">
                                <div class="card-header bg-primary py-2 text-center text-white fw-bold small">
                                    User Preferences
                                </div>
                                <div class="card-body p-3">
                                    <label class="small fw-bold d-block mb-2">Challan Payment Date Restriction</label>
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="form-check small">
                                            <input class="form-check-input" type="radio" value="custom" wire:model="payment_date_restriction">
                                            <label class="form-check-label">Custom</label>
                                        </div>
                                        <input type="text" wire:model="custom_date_restriction" class="form-control form-control-sm border shadow-none w-25" placeholder="Days">
                                        
                                        <div class="form-check small">
                                            <input class="form-check-input" type="radio" value="current_day" wire:model="payment_date_restriction">
                                            <label class="form-check-label">Current Day</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="radio" value="current_month" wire:model="payment_date_restriction">
                                            <label class="form-check-label">Current Month</label>
                                        </div>
                                    </div>
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" wire:model="two_step_approval">
                                        <label class="form-check-label fw-bold">Check to Enable Two-Step Challan Payment Approval</label>
                                    </div>
                                </div>
                            </div>

                            <div class="card border shadow-none rounded-0">
                                <div class="card-header bg-white border-bottom py-1 text-center text-dark fw-bold small uppercase">
                                    Cash Account Permissions
                                </div>
                                <div class="table-responsive p-0" style="max-height: 250px;">
                                    <table class="table table-sm table-bordered mb-0 align-middle text-center">
                                        <thead class="bg-primary bg-opacity-10 tiny fw-bold border-bottom">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th>Account</th>
                                                <th width="15%">Allow</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cash_accounts as $account)
                                            <tr class="border-bottom">
                                                <td class="tiny">{{ $loop->iteration }}</td>
                                                <td class="text-start ps-3 small fw-bold text-dark">{{ $account->name }}</td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input type="checkbox" wire:model="allowed_accounts.{{ $account->id }}" class="form-check-input border-secondary shadow-none">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-emerald text-white px-5 py-2 fw-bold shadow-sm rounded-1 btn-lg">
                            <span wire:loading.remove>{{ $editing_id ? 'Update User' : 'Create User' }}</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-shield me-2"></i>User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4 mb-3">
                        <div class="h5 fw-bold text-dark mb-1">{{ $name }}</div>
                        <div class="mb-3">
                            @php $userObj = \App\Models\User::find($editing_id); @endphp
                            @if($userObj)
                                @forelse($userObj->roles as $r)
                                    <span class="badge bg-primary bg-opacity-10 text-primary tiny">{{ $r->name }}</span>
                                @empty
                                    <span class="text-muted tiny">No Dynamic Roles</span>
                                @endforelse
                            @endif
                            <span class="text-muted mx-1">|</span>
                            <span class="small text-muted">Level {{ $power_level }}</span>
                        </div>
                        <hr class="opacity-10">
                        <div class="row g-2 small">
                            <div class="col-6">Username: <span class="text-dark fw-bold">{{ $username }}</span></div>
                            <div class="col-6">Email: <span class="text-dark fw-bold">{{ $email }}</span></div>
                            <div class="col-6">Contact: <span class="text-dark fw-bold">{{ $contact }}</span></div>
                            <div class="col-6">Status: <span class="badge bg-success bg-opacity-10 text-success">{{ $status }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<style>
    .btn-emerald { background-color: #2a9d8f; border-color: #2a9d8f; color: white; transition: 0.3s; }
    .btn-emerald:hover { background-color: #21867a; border-color: #21867a; color: white; transform: translateY(-1px); }
    
    .btn-emerald-dark { background-color: #1b746a; border-color: #1b746a; color: white; transition: 0.3s; }
    .btn-emerald-dark:hover { background-color: #145952; border-color: #145952; color: white; }

    .btn-danger-dark { background-color: #b91c1c; border-color: #b91c1c; color: white; transition: 0.3s; }
    .btn-danger-dark:hover { background-color: #991b1b; border-color: #991b1b; color: white; }

    .tiny { font-size: 0.65rem; }
</style>

<script>
    window.addEventListener('closeModal', event => {
        var el = document.getElementById('userModal');
        var modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    });
</script>
</div>

