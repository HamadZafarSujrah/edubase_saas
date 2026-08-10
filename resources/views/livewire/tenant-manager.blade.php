<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i> Platform Admin — Institutions</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Institutions</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openModal" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Institution</button>
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Search by name, code, or subdomain...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
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
                            <th class="ps-4">Institution</th>
                            <th>Code</th>
                            <th>Plan</th>
                            <th>Subscription</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $tenant->name }}</div>
                                <div class="tiny text-muted">{{ $tenant->subdomain }}</div>
                            </td>
                            <td class="small fw-bold">{{ $tenant->code }}</td>
                            <td class="small">{{ $tenant->plan->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border text-capitalize">{{ $tenant->subscription_status }}</span>
                                @if($tenant->trial_ends_at)
                                    <div class="tiny text-muted mt-1">Trial ends {{ $tenant->trial_ends_at->format('d-M-Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>
                                @elseif($tenant->status === 'suspended')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Suspended</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $tenant->id }})" class="btn btn-sm btn-light text-primary border" title="Edit"><i class="fas fa-edit"></i></button>
                                <button wire:click="openModulesModal({{ $tenant->id }})" class="btn btn-sm btn-light text-info border" title="Manage Modules"><i class="fas fa-puzzle-piece"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="toggleStatus({{ $tenant->id }})" class="btn btn-sm btn-light {{ $tenant->status === 'active' ? 'text-danger' : 'text-success' }} border" title="{{ $tenant->status === 'active' ? 'Suspend' : 'Activate' }}">
                                    <i class="fas {{ $tenant->status === 'active' ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                </button>
                                <a href="{{ route('switch.tenant', $tenant->id) }}" class="btn btn-sm btn-light text-dark border" title="Log In As"><i class="fas fa-sign-in-alt"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-building mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Institutions Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $tenants->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $tenant_id ? 'Edit Institution' : 'Add New Institution' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <h6 class="fw-bold text-muted small text-uppercase mb-3">Institution Details</h6>
              <div class="row g-3 mb-3">
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control bg-light" wire:model="name">
                      @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Login Code <span class="text-danger">*</span></label>
                      <input type="text" class="form-control bg-light" wire:model="code" placeholder="E.g. ALHIKMA">
                      @error('code') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Subdomain <span class="text-danger">*</span></label>
                      <input type="text" class="form-control bg-light" wire:model="subdomain" placeholder="E.g. alhikma">
                      @error('subdomain') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Email</label>
                      <input type="email" class="form-control bg-light" wire:model="email">
                      @error('email') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Phone</label>
                      <input type="text" class="form-control bg-light" wire:model="phone">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Status</label>
                      <select class="form-select bg-light" wire:model="status">
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                          <option value="suspended">Suspended</option>
                      </select>
                  </div>
                  <div class="col-12">
                      <label class="form-label fw-bold">Address</label>
                      <input type="text" class="form-control bg-light" wire:model="address">
                  </div>
              </div>

              <h6 class="fw-bold text-muted small text-uppercase mb-3">Plan &amp; Branding</h6>
              <div class="row g-3 mb-3">
                  <div class="col-md-3">
                      <label class="form-label fw-bold">Plan</label>
                      <select class="form-select bg-light" wire:model="plan_id">
                          <option value="">None</option>
                          @foreach($plans as $p) <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->price == 0 ? 'Free' : number_format($p->price, 0) }})</option> @endforeach
                      </select>
                  </div>
                  <div class="col-md-3">
                      <label class="form-label fw-bold">Subscription Status</label>
                      <select class="form-select bg-light" wire:model="subscription_status">
                          <option value="trial">Trial</option>
                          <option value="active">Active</option>
                          <option value="past_due">Past Due</option>
                          <option value="cancelled">Cancelled</option>
                      </select>
                  </div>
                  <div class="col-md-3">
                      <label class="form-label fw-bold">Trial Ends</label>
                      <input type="date" class="form-control bg-light" wire:model="trial_ends_at">
                  </div>
                  <div class="col-md-3">
                      <label class="form-label fw-bold">Primary Color</label>
                      <input type="color" class="form-control bg-light" style="height: 38px;" wire:model="primary_color">
                      @error('primary_color') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Logo URL</label>
                      <input type="text" class="form-control bg-light" wire:model="logo_url" placeholder="https://...">
                  </div>
              </div>

              @if(!$tenant_id)
                  <h6 class="fw-bold text-muted small text-uppercase mb-3">First Admin Account</h6>
                  <div class="row g-3">
                      <div class="col-md-4">
                          <label class="form-label fw-bold">Admin Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control bg-light" wire:model="admin_name">
                          @error('admin_name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                      </div>
                      <div class="col-md-4">
                          <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                          <input type="text" class="form-control bg-light" wire:model="admin_username">
                          @error('admin_username') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                      </div>
                      <div class="col-md-4">
                          <label class="form-label fw-bold">Admin Email <span class="text-danger">*</span></label>
                          <input type="email" class="form-control bg-light" wire:model="admin_email">
                          @error('admin_email') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                      </div>
                      <div class="col-md-6">
                          <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                          <input type="password" class="form-control bg-light" wire:model="admin_password">
                          @error('admin_password') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                      </div>
                  </div>
              @endif
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if($isModulesModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-puzzle-piece text-primary me-2"></i>Manage Module Access</h5>
            <button type="button" class="btn-close" wire:click="closeModulesModal"></button>
          </div>
          <div class="modal-body p-4">
              @foreach($availableModules as $slug => $label)
                  <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" wire:model="enabled_modules" value="{{ $slug }}" id="mod-{{ $slug }}">
                      <label class="form-check-label" for="mod-{{ $slug }}">{{ $label }}</label>
                  </div>
              @endforeach
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModulesModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="saveModules">Save</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    <style>
        .tiny { font-size: 0.75rem; }
    </style>
</div>
