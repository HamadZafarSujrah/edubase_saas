<div>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Campus Management</h1>
        <button wire:click="create()" class="btn btn-primary">Add New Campus</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Campus Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campuses as $campus)
                        <tr>
                            <td>{{ $campus->id }}</td>
                            <td><strong>{{ $campus->name }}</strong></td>
                            <td>{{ $campus->address }}</td>
                            <td>{{ $campus->contact_email }}</td>
                            <td>{{ $campus->contact_phone }}</td>
                            <td>
                                @if($campus->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="edit({{ $campus->id }})" class="btn btn-sm btn-info text-white">Edit</button>
                                <button onclick="confirm('Are you sure you want to delete this campus?') || event.stopImmediatePropagation()" wire:click="delete({{ $campus->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No campuses found. Click "Add New Campus" to get started.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $campuses->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $campus_id ? 'Edit Campus' : 'Create New Campus' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal()"></button>
          </div>
          <div class="modal-body">
              <form>
                  <div class="mb-3">
                      <label>Campus Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" wire:model="name" placeholder="E.g. Main Campus">
                      @error('name') <span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="mb-3">
                      <label>Address <span class="text-danger">*</span></label>
                      <textarea class="form-control" wire:model="address" placeholder="Physical location"></textarea>
                      @error('address') <span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="row">
                      <div class="col-md-6 mb-3">
                          <label>Contact Email</label>
                          <input type="email" class="form-control" wire:model="contact_email" placeholder="admin@campus.com">
                          @error('contact_email') <span class="text-danger small">{{ $message }}</span>@enderror
                      </div>
                      <div class="col-md-6 mb-3">
                          <label>Contact Phone</label>
                          <input type="text" class="form-control" wire:model="contact_phone" placeholder="+123456789">
                          @error('contact_phone') <span class="text-danger small">{{ $message }}</span>@enderror
                      </div>
                  </div>
                  <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" wire:model="is_active" id="isActiveCheck">
                      <label class="form-check-label" for="isActiveCheck">Campus is Active</label>
                  </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
            <button type="button" class="btn btn-primary" wire:click.prevent="store()">Save Campus</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
