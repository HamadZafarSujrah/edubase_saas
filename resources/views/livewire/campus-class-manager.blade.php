<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-network-wired me-2 text-primary"></i> Assign Classes to Campuses</h2>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Campus Selector Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-building me-2"></i> Select Campus
                </div>
                <div class="list-group list-group-flush">
                    @forelse($campuses as $campus)
                        <button 
                            wire:click="selectCampus({{ $campus->id }})"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedCampus && $selectedCampus->id === $campus->id ? 'active bg-primary text-white border-primary' : '' }}">
                            <span class="fw-bold">{{ $campus->name }}</span>
                            <i class="fas fa-chevron-right small"></i>
                        </button>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <p>No active campuses found.</p>
                            <a href="/campuses" class="btn btn-sm btn-outline-primary">Create Campus First</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Class Allocation Panel -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-bold py-3 text-primary">
                    <i class="fas fa-layer-group me-2 text-dark"></i> Classes Taught at: 
                    <span class="text-dark bg-warning px-2 py-1 rounded ms-1">{{ $selectedCampus ? $selectedCampus->name : 'None Selected' }}</span>
                </div>
                <div class="card-body bg-light">
                    
                    @if(!$selectedCampus)
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-hand-pointer mb-3" style="font-size: 3rem;"></i>
                            <h5>Select a campus from the left panel</h5>
                            <p>You can then assign which grades/classes are available at that physical location.</p>
                        </div>
                    @else
                        <!-- The Grid of Classes -->
                        <div class="row g-3">
                            @forelse($allClasses as $cls)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border-0 shadow-sm h-100 transition-hover {{ in_array($cls->id, $assignedClasses) ? 'bg-white border-success' : 'bg-white opacity-75' }}" 
                                         role="button" 
                                         wire:click="toggleClassAssignment({{ $cls->id }})"
                                         style="cursor: pointer; border-left: 4px solid {{ in_array($cls->id, $assignedClasses) ? '#198754' : '#dee2e6' }} !important; transform: transition 0.2s;">
                                        <div class="card-body p-3 text-center position-relative">
                                            
                                            <!-- Checkbox Visual -->
                                            <div class="position-absolute top-0 end-0 p-2">
                                                @if(in_array($cls->id, $assignedClasses))
                                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                                @else
                                                    <i class="far fa-circle text-muted fs-5"></i>
                                                @endif
                                            </div>
                                            
                                            <h6 class="fw-bold mt-2 mb-0 {{ in_array($cls->id, $assignedClasses) ? 'text-dark' : 'text-muted' }}">{{ $cls->name }}</h6>
                                            <p class="small text-muted mb-0">Code: {{ $cls->numeric_value ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <p class="text-muted">No global classes found in the system.</p>
                                    <a href="/classes" class="btn btn-outline-primary">Create Classes First</a>
                                </div>
                            @endforelse
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
