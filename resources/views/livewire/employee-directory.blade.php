<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-tie me-2 text-primary"></i> Employee Directory</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Employee Directory</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('hrm.employees.create') }}" class="btn btn-primary fw-bold px-4 rounded-3 shadow-sm">
            <i class="fas fa-plus me-2"></i> Add New Employee
        </a>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light shadow-sm" placeholder="Name, employee no. or phone...">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Campus</label>
                    <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Department</label>
                    <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-users me-2"></i> Employees</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $employees->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Employee No.</th>
                        <th>Name</th>
                        <th>Department / Designation</th>
                        <th>Campus</th>
                        <th>Phone</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3" width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $emp->emp_no }}</td>
                            <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                            <td>{{ $emp->department->name ?? '—' }} <span class="text-muted">{{ $emp->designation ? '/ ' . $emp->designation->name : '' }}</span></td>
                            <td>{{ $emp->campus->name ?? '—' }}</td>
                            <td class="text-muted">{{ $emp->phone ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $emp->status === 'active' ? 'bg-success' : 'bg-secondary' }} text-uppercase">{{ $emp->status }}</span>
                            </td>
                            <td class="text-center pe-3">
                                <a href="{{ route('hrm.employees.edit', $emp->id) }}" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></a>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="deleteEmployee({{ $emp->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-user-tie fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No employees found for the selected filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $employees->links() }}
        </div>
    </div>
</div>
