<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> My Subscription</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">My Subscription</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Current Plan</div>
                            <div class="fs-3 fw-bold text-dark">{{ $tenant->plan->name ?? 'No Plan Assigned' }}</div>
                        </div>
                        <span class="badge fs-6 px-3 py-2
                            @if($tenant->subscription_status === 'active') bg-success
                            @elseif($tenant->subscription_status === 'trial') bg-info
                            @elseif($tenant->subscription_status === 'past_due') bg-warning text-dark
                            @else bg-danger @endif
                        ">{{ ucfirst(str_replace('_', ' ', $tenant->subscription_status)) }}</span>
                    </div>

                    @if($tenant->plan)
                        <div class="text-muted small mb-3">
                            {{ $tenant->plan->price == 0 ? 'Free' : number_format($tenant->plan->price, 0) . ' / ' . $tenant->plan->billing_cycle }}
                        </div>
                    @endif

                    @if($tenant->subscription_status === 'trial' && $tenant->trial_ends_at)
                        <div class="alert alert-info border-0 py-2 mb-0">
                            <i class="fas fa-clock me-2"></i>
                            @php $daysLeft = now()->diffInDays($tenant->trial_ends_at, false); @endphp
                            @if($daysLeft >= 0)
                                Your trial ends in <strong>{{ $daysLeft }}</strong> day(s) ({{ $tenant->trial_ends_at->format('d-M-Y') }}).
                            @else
                                Your trial ended on {{ $tenant->trial_ends_at->format('d-M-Y') }}.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-headset text-primary mb-3" style="font-size: 2rem;"></i>
                    <p class="text-muted small mb-3">Need a higher plan, or have a billing question?</p>
                    <a href="mailto:support@edubase.example" class="btn btn-primary rounded-3 px-4">
                        <i class="fas fa-envelope me-2"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">Usage</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>Students</span>
                            <span class="{{ $studentLimitHit ? 'text-danger' : 'text-muted' }}">
                                {{ $studentCount }} / {{ $tenant->plan?->max_students ?? '∞' }}
                            </span>
                        </div>
                        @if($tenant->plan && $tenant->plan->max_students)
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $studentLimitHit ? 'bg-danger' : 'bg-primary' }}" style="width: {{ min(100, ($studentCount / $tenant->plan->max_students) * 100) }}%"></div>
                            </div>
                        @endif
                        @if($studentLimitHit)
                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle me-1"></i> Student limit reached. Upgrade your plan to admit more students.</div>
                        @endif
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span>Campuses</span>
                            <span class="{{ $campusLimitHit ? 'text-danger' : 'text-muted' }}">
                                {{ $campusCount }} / {{ $tenant->plan?->max_campuses ?? '∞' }}
                            </span>
                        </div>
                        @if($tenant->plan && $tenant->plan->max_campuses)
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $campusLimitHit ? 'bg-danger' : 'bg-primary' }}" style="width: {{ min(100, ($campusCount / $tenant->plan->max_campuses) * 100) }}%"></div>
                            </div>
                        @endif
                        @if($campusLimitHit)
                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle me-1"></i> Campus limit reached. Upgrade your plan to add more campuses.</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($tenant->plan && is_array($tenant->plan->features))
                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold text-primary mb-0">Included Modules</h6>
                    </div>
                    <div class="card-body p-4 d-flex flex-wrap gap-2">
                        @foreach($tenant->plan->features as $feature)
                            <span class="badge bg-light text-dark border text-capitalize">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
