<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 text-center">
            <h5 class="mb-0 fw-bold">Manage Fee Plan List</h5>
        </div>
        <div class="card-body p-4">

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row justify-content-center mb-5">
                <div class="col-md-10">
                    <div class="p-4 bg-light rounded-4 border shadow-sm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Fee Plan Name</label>
                                <input type="text" wire:model="plan_name" class="form-control" placeholder="e.g. Matric Fee Plan">
                                @error('plan_name') <span class="text-danger tiny fw-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Description</label>
                                <input type="text" wire:model="description" class="form-control" placeholder="Optional details...">
                            </div>
                            <div class="col-md-2">
                                @if($editing_id)
                                    <button wire:click="update" class="btn btn-warning w-100 fw-bold">Update</button>
                                @else
                                    <button wire:click="create" class="btn btn-success w-100 fw-bold"><i class="fas fa-plus me-1"></i> Create</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Fee Plan Name</th>
                            <th>Description</th>
                            <th>Created at</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $p->name }}</td>
                                <td>{{ $p->description ?: '-' }}</td>
                                <td class="small text-muted">{{ $p->created_at->format('Y-m-d') }}</td>
                                <td class="text-end pe-3">
                                    <button wire:click="edit({{ $p->id }})" class="btn btn-outline-primary btn-sm rounded-circle"><i class="fas fa-edit"></i></button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="btn btn-outline-danger btn-sm rounded-circle ms-1"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
