<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-layer-group me-2 text-primary"></i> Academic Classes</h2>
        <button wire:click="openClassModal()" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Class</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Classes Table -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Class Name</th>
                            <th>Total Sections</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $cls)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $cls->name }}</td>
                            <td><span class="badge bg-info fw-bold">{{ $cls->sections_count }}</span></td>
                            <td>
                                @if($cls->is_active)
                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check-circle me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="manageSections({{ $cls->id }})" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-sitemap"></i> Sections</button>
                                <button wire:click="editClass({{ $cls->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="deleteClass({{ $cls->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Classes Found</h5>
                                <p>Start building your academic structure by adding your first class.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $classes->links() }}
        </div>
    </div>

    <!-- Class Create/Edit Modal -->
    @if($isClassModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $class_id ? 'Edit Class Details' : 'Create New Class' }}</h5>
            <button type="button" class="btn-close" wire:click="closeClassModal()"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-4">
                  <label class="form-label fw-bold">Class Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="name" placeholder="E.g. Grade 10">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="mb-4">
                  <label class="form-label fw-bold">Numeric Value (Optional for sorting)</label>
                  <input type="number" class="form-control bg-light" wire:model="numeric_value" placeholder="E.g. 10">
              </div>
              <div class="form-check form-switch form-control-lg ps-3 m-0">
                  <input type="checkbox" class="form-check-input" wire:model="is_active" id="isActiveCheck" style="margin-left: -2em;">
                  <label class="form-check-label fw-bold ms-2" for="isActiveCheck">Class is currently Active</label>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeClassModal()">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="saveClass()">Save Class</button>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Section Management Modal -->
    @if($isSectionModalOpen && $managingSectionsFor)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title"><i class="fas fa-sitemap me-2"></i>Sections for: <span class="text-warning">{{ $managingSectionsFor->name }}</span></h5>
            <button type="button" class="btn-close btn-close-white" wire:click="closeSectionModal()"></button>
          </div>
          <div class="modal-body bg-light">
              
              @if (session()->has('section_message'))
                <div class="alert alert-success alert-dismissible fade show py-2">
                    {{ session('section_message') }}
                    <button type="button" class="btn-close pb-2" data-bs-dismiss="alert"></button>
                </div>
              @endif

              <div class="row">
                  <!-- Add New Section Form -->
                  <div class="col-md-4">
                      <div class="card shadow-sm border-0">
                          <div class="card-header bg-white border-bottom fw-bold pt-3 pb-2 text-primary">
                              Add New Section
                          </div>
                          <div class="card-body">
                              <div class="mb-3">
                                  <label class="small fw-bold">Section Name/Letter *</label>
                                  <input type="text" class="form-control" wire:model="section_name" placeholder="E.g. A, B, Blue">
                                  @error('section_name') <span class="text-danger small">{{ $message }}</span>@enderror
                              </div>
                              <div class="mb-3">
                                  <label class="small fw-bold">Student Capacity *</label>
                                  <input type="number" class="form-control" wire:model="capacity" value="30">
                              </div>
                              <div class="mb-3">
                                  <label class="small fw-bold">Room Number</label>
                                  <input type="text" class="form-control" wire:model="room_number" placeholder="Optional">
                              </div>
                              <button wire:click="saveSection()" class="btn btn-primary w-100 fw-bold">Add Section</button>
                          </div>
                      </div>
                  </div>

                  <!-- Existing Sections List -->
                  <div class="col-md-8">
                      <div class="card shadow-sm border-0 h-100">
                          <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($activeSections as $sec)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold fs-5">Section {{ $sec->name }}</h6>
                                        <small class="text-muted"><i class="fas fa-users me-1"></i> Capacity: {{ $sec->capacity }} | <i class="fas fa-door-closed me-1"></i> Room: {{ $sec->room_number ?? 'N/A' }}</small>
                                    </div>
                                    <button wire:click="deleteSection({{ $sec->id }})" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Section"><i class="fas fa-times"></i></button>
                                </li>
                                @empty
                                <div class="p-4 text-center text-muted mt-4">
                                    <i class="fas fa-puzzle-piece mb-2" style="font-size: 2rem;"></i>
                                    <h6>No Sections Built Yet</h6>
                                    <p class="small">Add sections to assign students to.</p>
                                </div>
                                @endforelse
                            </ul>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeSectionModal()">Done Customizing</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
