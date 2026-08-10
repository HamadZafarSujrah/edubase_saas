<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-percentage me-2 text-primary"></i> Grading Policy</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Grading Policy</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openModal" class="btn btn-primary rounded-pill px-4 btn-sm"><i class="fas fa-plus me-2"></i> Add Grade Band</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <label class="small fw-bold text-muted mb-1">Scope</label>
            <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm" style="max-width: 300px;">
                <option value="">Institution-wide Default</option>
                @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }} (override)</option> @endforeach
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> Grade Bands — {{ $school_class_id ? $classes->firstWhere('id', $school_class_id)->name ?? '' : 'Institution-wide Default' }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Grade Label</th>
                        <th>Min %</th>
                        <th>Max %</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $p)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $p->grade_label }}</td>
                            <td>{{ $p->min_percent }}%</td>
                            <td>{{ $p->max_percent }}%</td>
                            <td class="text-end pe-3">
                                <button wire:click="edit({{ $p->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-5 text-muted">No grade bands defined for this scope yet. E.g. A+ = 90-100%, A = 80-89%.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $policy_id ? 'Edit Grade Band' : 'Add Grade Band' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Grade Label <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light border-0" wire:model="grade_label" placeholder="E.g. A+">
                  @error('grade_label') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Min %  <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" class="form-control bg-light border-0" wire:model="min_percent">
                      @error('min_percent') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Max % <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" class="form-control bg-light border-0" wire:model="max_percent">
                      @error('max_percent') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
