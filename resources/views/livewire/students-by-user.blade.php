<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-check me-2 text-primary"></i> Students By User</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Students By User</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Filter by User</label>
                    <select wire:model.live="user_id" class="form-select border-0 bg-light shadow-sm">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($counts as $c)
                            <span class="badge bg-light text-dark border">
                                {{ $c->creator->name ?? 'Unknown' }}: <strong>{{ $c->total }}</strong>
                            </span>
                        @endforeach
                        @if($counts->isEmpty())
                            <span class="text-muted small">No students have a recorded creator yet.</span>
                        @endif
                    </div>
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
                            <th class="ps-4">Student</th>
                            <th>Admission No.</th>
                            <th>Class / Section</th>
                            <th>Created By</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->admission_no }}</td>
                            <td>{{ $student->schoolClass->name ?? '—' }} {{ $student->section->name ?? '' }}</td>
                            <td>{{ $student->creator->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $student->created_at->format('d-M-Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-user-check mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Students Found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $students->links() }}
        </div>
    </div>
</div>
