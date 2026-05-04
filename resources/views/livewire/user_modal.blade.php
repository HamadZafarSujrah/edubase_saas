<div wire:ignore.self class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-0 pb-0 pe-4 pt-4">
                <div class="w-100 text-center">
                    <h5 class="fw-bold text-dark">Please fill out the following fields to {{ $editing_id ? 'Update' : 'Add' }} a User:</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">Username</label>
                        <input type="text" wire:model="username" class="form-control border shadow-none">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" wire:model="name" class="form-control border shadow-none">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">Email</label>
                        <input type="email" wire:model="email" class="form-control border shadow-none">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">Contact</label>
                        <input type="text" wire:model="contact" class="form-control border shadow-none">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">Password</label>
                        <input type="password" wire:model="password" class="form-control border shadow-none" placeholder="{{ $editing_id ? 'Leave blank to keep current' : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">User Level (Power In Integer)</label>
                        <select wire:model="power_level" class="form-select border shadow-none">
                            <option value="">Select User Level</option>
                            <option value="1">Level 1 (Super Admin)</option>
                            <option value="2">Level 2 (Manager)</option>
                            <option value="3">Level 3 (Staff)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-danger">Role</label>
                        <select wire:model="role" class="form-select border shadow-none">
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
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
                                    <thead class="bg-light tiny fw-bold">
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
                                                <input type="checkbox" wire:model="allowed_campuses.{{ $campus->id }}" class="form-check-input border-secondary shadow-none">
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
                            <div class="table-responsive p-0" style="max-height: 200px;">
                                <table class="table table-sm table-bordered mb-0 align-middle text-center">
                                    <thead class="bg-light tiny fw-bold">
                                        <tr>
                                            <th width="10%">#</th>
                                            <th>Account</th>
                                            <th width="15%">Allow</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cash_accounts as $account)
                                        <tr>
                                            <td class="tiny">{{ $loop->iteration }}</td>
                                            <td class="text-start ps-3 small fw-bold">{{ $account->name }}</td>
                                            <td>
                                                <input type="checkbox" wire:model="allowed_accounts.{{ $account->id }}" class="form-check-input border-secondary shadow-none">
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
