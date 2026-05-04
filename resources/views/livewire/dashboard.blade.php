<div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-5 border-primary">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Institution Management Dashboard</h1>
            <p class="text-muted mb-0 small"><i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, jS F Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <p class="fw-bold mb-0 text-dark">{{ auth()->user()->name }}</p>
                <span class="badge bg-soft-primary text-primary px-3 rounded-pill">Administrator</span>
            </div>
            <div class="bg-primary text-white p-3 rounded-circle shadow-sm">
                <i class="fas fa-user-shield fs-4"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Students Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-primary text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <p class="text-white text-opacity-75 small fw-bold mb-1 uppercase letter-spacing-1">Total Students</p>
                    <h2 class="display-6 fw-bold mb-0">1,248</h2>
                    <div class="mt-3 small text-white text-opacity-75">
                        <i class="fas fa-arrow-up me-1"></i> 12% from last month
                    </div>
                </div>
                <i class="fas fa-user-graduate position-absolute end-0 bottom-0 p-3 fs-1 opacity-25"></i>
            </div>
        </div>

        <!-- Financial Status Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-success">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">Monthly Collections</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">PKR 850k</h2>
                    <div class="mt-3 small text-success">
                        <i class="fas fa-check-circle me-1"></i> Target Achieved
                    </div>
                </div>
                <i class="fas fa-money-bill-wave position-absolute end-0 bottom-0 p-3 fs-1 text-success opacity-10"></i>
            </div>
        </div>

        <!-- Pending Challans Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-warning">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">Active Campuses</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">04</h2>
                    <div class="mt-3 small text-warning font-mono">
                        System Online
                    </div>
                </div>
                <i class="fas fa-school position-absolute end-0 bottom-0 p-3 fs-1 text-warning opacity-10"></i>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-info">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">Total Staff</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">86</h2>
                    <div class="mt-3 small text-info">
                        Active Personnel
                    </div>
                </div>
                <i class="fas fa-users-cog position-absolute end-0 bottom-0 p-3 fs-1 text-info opacity-10"></i>
            </div>
        </div>
    </div>

    <!-- Second Row: Quick Actions & Recent Activity -->
    <div class="row g-4">
        <!-- Quick Actions Grid -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Operational Quick Links</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('finance.journal-entry') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-success text-success p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-book-open fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Journal Entry</h6>
                                <p class="small text-muted mb-0">Post financial vouchers</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('students.admission') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-primary text-primary p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-user-plus fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">New Admission</h6>
                                <p class="small text-muted mb-0">Register new student</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('finance.generate-challan') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-warning text-warning p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-file-invoice-dollar fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Fee Challan</h6>
                                <p class="small text-muted mb-0">Generate monthly fees</p>
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="my-4 border-bottom opacity-10"></div>

                    <!-- Chart Placeholder -->
                    <div class="bg-light rounded-4 p-5 text-center">
                        <i class="fas fa-chart-line fs-1 text-muted opacity-25 mb-3 d-block"></i>
                        <h6 class="text-muted fw-bold">Financial Growth Analytics</h6>
                        <p class="small text-muted">Analytics are initializing for the new academic session.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Notifications / Logs -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-4 border-0">
                    <h5 class="fw-bold mb-0">Security Logs</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item p-4 border-0 border-bottom">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <h6 class="mb-1 fw-bold text-primary">System Upgrade</h6>
                                <small class="text-muted">Just Now</small>
                            </div>
                            <p class="mb-1 small text-dark">Successfully migrated to Laravel 13 & PHP 8.3.</p>
                            <span class="badge bg-success-soft text-success rounded-pill px-2 py-1 x-small">STABLE</span>
                        </div>
                        <div class="list-group-item p-4 border-0 border-bottom bg-light">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <h6 class="mb-1 fw-bold text-dark">Tenant Sync</h6>
                                <small class="text-muted">1hr ago</small>
                            </div>
                            <p class="mb-1 small text-muted">Institution data synchronized with master node.</p>
                        </div>
                        <div class="list-group-item p-4 border-0">
                            <div class="d-none d-lg-block p-4 text-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-4">View All Logs</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); }
        .bg-soft-success { background-color: rgba(28, 200, 138, 0.1); }
        .bg-soft-warning { background-color: rgba(246, 194, 62, 0.1); }
        .bg-light-hover:hover { background-color: rgba(0,0,0,0.02) !important; }
        .transition-all { transition: all 0.3s ease; }
        .shadow-sm-hover:hover { shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
        .uppercase { text-transform: uppercase; }
        .letter-spacing-1 { letter-spacing: 1px; }
        .x-small { font-size: 0.75rem; }
        .bg-success-soft { background-color: #d1e7dd; }
    </style>
</div>
