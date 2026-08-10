<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i> Assign Classes</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Assign Classes</li>
                </ol>
            </nav>
        </div>
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
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-sitemap me-2"></i> Section &rarr; Class Teacher</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Class</th>
                        <th>Section</th>
                        <th width="300">Class Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $section->schoolClass->name ?? '—' }}</td>
                            <td>{{ $section->name }}</td>
                            <td>
                                <select class="form-select form-select-sm border-0 bg-light shadow-sm" onchange="$wire.assignTeacher({{ $section->id }}, this.value)">
                                    <option value="">Unassigned</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}" @selected($section->teacher_id === $t->id)>{{ $t->first_name }} {{ $t->last_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="fas fa-sitemap fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No sections found for the selected class.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
