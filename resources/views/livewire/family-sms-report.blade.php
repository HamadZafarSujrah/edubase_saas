<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-people-roof me-2 text-primary"></i> Family SMS Report</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Family SMS Report</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="position-relative">
                <label class="small fw-bold text-muted mb-1">Search Family</label>
                <input type="text" wire:model.live.debounce.300ms="family_search" placeholder="Family no., father name or guardian phone..." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                @if(count($suggested_families) > 0)
                    <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                        @foreach($suggested_families as $f)
                            <button type="button" wire:click="selectFamily({{ $f['id'] }})" class="list-group-item list-group-item-action">
                                <strong>{{ $f['father_name'] ?? 'Family #' . $f['family_no'] }}</strong>
                                <span class="text-muted small"> — Family No. {{ $f['family_no'] }} — {{ $f['guardian_phone'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($selected_family)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2">{{ $selected_family->father_name ?? '—' }} <span class="text-muted small">Family No. {{ $selected_family->family_no }}</span></h6>
                <div class="text-muted small mb-2">Guardian Phone: {{ $selected_family->guardian_phone ?: '—' }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($selected_family->students as $child)
                        <span class="badge bg-light text-dark border px-3 py-2">{{ $child->first_name }} {{ $child->last_name }} ({{ $child->admission_no }})</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Messages Sent to This Family</span>
                <span class="badge bg-white text-dark px-3 py-2">{{ count($logs) }} message(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="ps-3" width="130">Sent At</th>
                            <th width="160">Regarding</th>
                            <th>Message</th>
                            <th class="text-center" width="100">Type</th>
                            <th class="text-center" width="90">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-3 text-muted">{{ $log->sent_at?->format('d-M-Y h:i A') ?? '—' }}</td>
                                <td class="fw-bold">{{ $log->student->full_name ?? '—' }}</td>
                                <td class="text-truncate" style="max-width: 350px;" title="{{ $log->message }}">{{ $log->message }}</td>
                                <td class="text-center"><span class="badge bg-secondary text-uppercase">{{ $log->type }}</span></td>
                                <td class="text-center">
                                    <span class="badge {{ $log->status === 'sent' ? 'bg-success' : ($log->status === 'failed' ? 'bg-danger' : 'bg-warning') }} text-uppercase">{{ $log->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">No SMS messages recorded for this family yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search fs-2 opacity-50 mb-3 d-block"></i>
            Search and select a family above to view their SMS history.
        </div>
    @endif
</div>
