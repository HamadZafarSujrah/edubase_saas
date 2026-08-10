<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark text-center">Manage Fee Particulars</h5>
        </div>
        <div class="card-body p-4">
            
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <div class="p-4 bg-light rounded-4 border">
                        <label class="form-label small fw-bold">Particular Name</label>
                        <div class="input-group">
                            <input type="text" wire:model="particular_name" class="form-control border-0 shadow-none px-3 py-2" placeholder="e.g. Tuition Fee, Admission Fee...">
                            @if($editing_id)
                                <button wire:click="update" class="btn btn-warning px-4 fw-bold">Update</button>
                                <button wire:click="$reset(['editing_id', 'particular_name'])" class="btn btn-light px-3 border">Cancel</button>
                            @else
                                <button wire:click="create" class="btn btn-success px-4 fw-bold"><i class="fas fa-plus me-1"></i> Create</button>
                            @endif
                        </div>
                        @error('particular_name') <span class="text-danger tiny fw-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm ps-4 py-2">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm ps-4 py-2">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Particular Name</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($particulars as $index => $p)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $p->name }}</td>
                                <td class="small text-muted">{{ $p->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="small text-muted">{{ $p->updated_at->format('Y-m-d H:i:s') }}</td>
                                <td class="text-end pe-3">
                                    <button wire:click="edit({{ $p->id }})" class="btn btn-outline-primary btn-sm rounded-circle"><i class="fas fa-edit"></i></button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="btn btn-outline-danger btn-sm rounded-circle ms-1"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No particulars defined yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
