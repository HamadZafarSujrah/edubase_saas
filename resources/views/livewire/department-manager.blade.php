<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2 text-primary"></i> Departments</h2>
        <div>
            <button wire:click="openGroupModal()" class="btn btn-outline-secondary shadow-sm me-2"><i class="fas fa-layer-group me-1"></i> Manage Groups</button>
            <button wire:click="openDepartmentModal()" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Department</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Departments Table -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Department Name</th>
                            <th>Group</th>
                            <th>Total Designations</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $dept->name }}</td>
                            <td>{{ $dept->group->name ?? '—' }}</td>
                            <td><span class="badge bg-info fw-bold">{{ $dept->designations_count }}</span></td>
                            <td class="text-end pe-4">
                                <button wire:click="manageDesignations({{ $dept->id }})" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-id-badge"></i> Designations</button>
                                <button wire:click="editDepartment({{ $dept->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="deleteDepartment({{ $dept->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Departments Found</h5>
                                <p>Start building your HR structure by adding your first department.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $departments->links() }}
        </div>
    </div>

    <!-- Department Create/Edit Modal -->
    @if($isDepartmentModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $department_id ? 'Edit Department' : 'Create New Department' }}</h5>
            <button type="button" class="btn-close" wire:click="closeDepartmentModal()"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-4">
                  <label class="form-label fw-bold">Department Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="name" placeholder="E.g. Academics, Finance, Admin">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="mb-4">
                  <label class="form-label fw-bold">Department Group (Optional)</label>
                  <select class="form-select bg-light" wire:model="department_group_id">
                      <option value="">No group</option>
                      @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->name }}</option> @endforeach
                  </select>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeDepartmentModal()">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="saveDepartment()">Save Department</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Designation Management Modal -->
    @if($isDesignationModalOpen && $managingDesignationsFor)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title"><i class="fas fa-id-badge me-2"></i>Designations for: <span class="text-warning">{{ $managingDesignationsFor->name }}</span></h5>
            <button type="button" class="btn-close btn-close-white" wire:click="closeDesignationModal()"></button>
          </div>
          <div class="modal-body bg-light">

              @if (session()->has('designation_message'))
                <div class="alert alert-success alert-dismissible fade show py-2">
                    {{ session('designation_message') }}
                    <button type="button" class="btn-close pb-2" data-bs-dismiss="alert"></button>
                </div>
              @endif

              <div class="row">
                  <div class="col-md-5">
                      <div class="card shadow-sm border-0">
                          <div class="card-header bg-white border-bottom fw-bold pt-3 pb-2 text-primary">
                              Add New Designation
                          </div>
                          <div class="card-body">
                              <div class="mb-3">
                                  <label class="small fw-bold">Designation Name *</label>
                                  <input type="text" class="form-control" wire:model="designation_name" placeholder="E.g. Teacher, Accountant, Principal">
                                  @error('designation_name') <span class="text-danger small">{{ $message }}</span>@enderror
                              </div>
                              <button wire:click="saveDesignation()" class="btn btn-primary w-100 fw-bold">Add Designation</button>
                          </div>
                      </div>
                  </div>

                  <div class="col-md-7">
                      <div class="card shadow-sm border-0 h-100">
                          <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($designations as $des)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <h6 class="mb-0 fw-bold">{{ $des->name }}</h6>
                                    <button wire:click="deleteDesignation({{ $des->id }})" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Designation"><i class="fas fa-times"></i></button>
                                </li>
                                @empty
                                <div class="p-4 text-center text-muted mt-4">
                                    <i class="fas fa-puzzle-piece mb-2" style="font-size: 2rem;"></i>
                                    <h6>No Designations Yet</h6>
                                </div>
                                @endforelse
                            </ul>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeDesignationModal()">Done Customizing</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Department Group Management Modal -->
    @if($isGroupModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>Department Groups</h5>
            <button type="button" class="btn-close btn-close-white" wire:click="closeGroupModal()"></button>
          </div>
          <div class="modal-body bg-light">

              @if (session()->has('group_message'))
                <div class="alert alert-success alert-dismissible fade show py-2">
                    {{ session('group_message') }}
                    <button type="button" class="btn-close pb-2" data-bs-dismiss="alert"></button>
                </div>
              @endif

              <div class="input-group mb-3">
                  <input type="text" class="form-control" wire:model="group_name" placeholder="E.g. Teaching Staff, Support Staff">
                  <button class="btn btn-primary" wire:click="saveGroup()">Add Group</button>
              </div>
              @error('group_name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

              <ul class="list-group">
                  @forelse($groups as $g)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                      {{ $g->name }}
                      <button wire:click="deleteGroup({{ $g->id }})" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Group"><i class="fas fa-times"></i></button>
                  </li>
                  @empty
                  <li class="list-group-item text-center text-muted py-4">No groups yet.</li>
                  @endforelse
              </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeGroupModal()">Done</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
