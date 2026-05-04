<div class="container-fluid pb-5">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i> Premier Enterprise Admission Form</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Admissions</a></li>
                    <li class="breadcrumb-item active">New Admission</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm px-3"><i class="fas fa-cloud-upload-alt me-1"></i> Bulk Upload</button>
            <a href="/students" class="btn btn-primary btn-sm px-3"><i class="fas fa-list me-1"></i> Student Directory</a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div class="mb-4">
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm animate__animated animate__shakeX">
                <h6 class="fw-bold small"><i class="fas fa-exclamation-triangle me-2"></i> Please correct the highlighted errors before saving.</h6>
            </div>
        @endif
    </div>

    <form wire:submit.prevent="save" x-data="{ tab: 'academic' }">
    <div class="row">
        <div class="col-xl-9 col-lg-8">

            <!-- WIZARD NAVIGATION -->
            <ul class="nav nav-pills nav-fill mb-4 bg-white rounded-4 shadow-sm p-2 flex-column flex-sm-row">
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold rounded-pill p-3" :class="tab === 'academic' ? 'active shadow-sm' : 'text-secondary'" @click="tab = 'academic'">
                        <i class="fas fa-graduation-cap me-2"></i> 1. Placement
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold rounded-pill p-3" :class="tab === 'personal' ? 'active shadow-sm bg-success text-white' : 'text-secondary'" @click="tab = 'personal'">
                        <i class="fas fa-user-circle me-2"></i> 2. Bio Data
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold rounded-pill p-3" :class="tab === 'family' ? 'active shadow-sm bg-warning text-dark' : 'text-secondary'" @click="tab = 'family'">
                        <i class="fas fa-users me-2"></i> 3. Parents
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold rounded-pill p-3" :class="tab === 'history' ? 'active shadow-sm bg-info text-white' : 'text-secondary'" @click="tab = 'history'">
                        <i class="fas fa-history me-2"></i> 4. History
                    </button>
                </li>
            </ul>
            
            <!-- 1. ACADEMIC PLACEMENT (TAB) -->
            <div x-show="tab === 'academic'" x-transition:enter="animate__animated animate__fadeIn">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-4 text-primary fw-bold hs-6"><i class="fas fa-graduation-cap me-2"></i> 1. Academic Placement</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model="admission_date" class="form-control bg-light border-0 shadow-sm @error('admission_date') is-invalid @enderror">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Campus <span class="text-danger">*</span></label>
                            <select wire:model.live="campus_id" class="form-select border-0 bg-light shadow-sm @error('campus_id') is-invalid @enderror">
                                <option value="">Select Campus</option>
                                @foreach($campuses as $camp) <option value="{{ $camp->id }}">{{ $camp->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Class <span class="text-danger">*</span></label>
                            <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm @error('school_class_id') is-invalid @enderror" {{ !$campus_id ? 'disabled' : '' }}>
                                <option value="">Class</option>
                                @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Section <span class="text-danger">*</span></label>
                            <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm @error('section_id') is-invalid @enderror" {{ !$school_class_id ? 'disabled' : '' }}>
                                <option value="">Section</option>
                                @foreach($sections as $sec) <option value="{{ $sec->id }}">{{ $sec->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Fee Plan</label>
                            <select wire:model="fee_plan_id" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select Plan</option>
                                @foreach($fee_plans as $plan) <option value="{{ $plan->id }}">{{ $plan->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- 2. STUDENT BIO INFORMATION (TAB) -->
            <div x-show="tab === 'personal'" x-transition:enter="animate__animated animate__fadeIn" style="display: none;">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-success"><i class="fas fa-user-circle me-2"></i> 2. Student Personal Information</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Admission No <span class="text-danger">*</span></label>
                            <input type="text" wire:model="admission_no" class="form-control bg-light border-0 fw-bold text-primary @error('admission_no') is-invalid @enderror">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Roll No</label>
                            <input type="text" wire:model="roll_no" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Gender</label>
                            <select wire:model="gender" class="form-select border-0 bg-light">
                                <option value="Male">Male</option><option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Religion</label>
                            <input type="text" wire:model="religion" class="form-control border-0 bg-light">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Student Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="first_name" class="form-control @error('first_name') is-invalid @enderror">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CNIC / B-Form No</label>
                            <input type="text" wire:model="cnic_no" class="form-control" placeholder="13 Digit Number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" wire:model="date_of_birth" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Birth City</label>
                            <input type="text" wire:model="birth_city" class="form-control border-0 bg-light">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Blood Group</label>
                            <select wire:model="blood_group" class="form-select border-0 bg-light">
                                <option value="">Select</option>
                                <option value="A+">A+</option><option value="B+">B+</option><option value="O+">O+</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Caste</label>
                            <input type="text" wire:model="caste" class="form-control border-0 bg-light">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">ID Mark</label>
                            <input type="text" wire:model="identification_mark" class="form-control border-0 bg-light">
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>

            <!-- 3. PREVIOUS QUALIFICATION GRID & BOARD (HISTORY TAB) -->
            <div x-show="tab === 'history'" x-transition:enter="animate__animated animate__fadeIn" style="display: none;">
            <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-2 small fw-bold border-0">
                    <i class="fas fa-history me-2"></i> 3. Previous Qualification
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead class="bg-light tiny fw-bold text-uppercase">
                            <tr>
                                <th>Degree/Class</th>
                                <th>Board/University</th>
                                <th>Roll No</th>
                                <th>Total Marks</th>
                                <th>Obtained</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" wire:model="prev_degree" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" wire:model="prev_board" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" wire:model="prev_roll_no" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" wire:model="prev_total_marks" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" wire:model="prev_obtained_marks" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" wire:model="prev_grade" class="form-control form-control-sm border-0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

            <!-- 5. BOARD INFORMATION (For Metric/Inter) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info bg-opacity-10 py-3 border-0">
                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-clipboard-check me-2"></i> 5. Current Board Information (If applicable)</h6>
                </div>
                <div class="card-body p-4 pt-0 mt-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold tiny">Board Reg No</label>
                            <input type="text" wire:model="board_reg_no" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold tiny">Board Roll No</label>
                            <input type="text" wire:model="board_roll_no" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold tiny">Total Marks</label>
                            <input type="text" wire:model="board_total_marks" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold tiny">Obtained Marks</label>
                            <input type="text" wire:model="board_obtained_marks" class="form-control bg-light border-0">
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- 4. FAMILY INFORMATION (TAB) -->
            <div x-show="tab === 'family'" x-transition:enter="animate__animated animate__fadeIn" style="display: none;">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users me-2 text-warning"></i> 4. Family & Parental Connectivity</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <!-- Father Info Row -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Father Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="father_name" class="form-control border-primary border-opacity-25 @error('father_name') is-invalid @enderror">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Father Contact</label>
                            <input type="text" wire:model="father_phone" class="form-control" placeholder="11 Digits">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-success"><i class="fab fa-whatsapp me-1"></i> WhatsApp No</label>
                            <input type="text" wire:model="whatsapp_no" class="form-control border-success border-opacity-25" placeholder="For notifications">
                        </div>

                        <!-- Mother Info Row -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Mother Name</label>
                            <input type="text" wire:model="mother_name" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Mother Contact</label>
                            <input type="text" wire:model="mother_phone" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Guardian Contact</label>
                            <input type="text" wire:model="guardian_phone" class="form-control bg-light border-0">
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>

            <!-- TAB NAVIGATION CONTROLS -->
            <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-3 rounded-4 shadow-sm border">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" 
                        x-show="tab !== 'academic'" 
                        @click="if(tab === 'personal') tab = 'academic'; else if(tab === 'family') tab = 'personal'; else if(tab === 'history') tab = 'family';">
                    <i class="fas fa-arrow-left me-2"></i> Previous Section
                </button>
                <div x-show="tab === 'academic'"></div> <!-- Placeholder for layout -->

                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold" 
                        x-show="tab !== 'history'" 
                        @click="if(tab === 'academic') tab = 'personal'; else if(tab === 'personal') tab = 'family'; else if(tab === 'family') tab = 'history';">
                    Next Section <i class="fas fa-arrow-right ms-2 text-warning"></i>
                </button>
                
                <span x-show="tab === 'history'" class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Form Complete. Proceed to Save on the Right!</span>
            </div>

        </div>

        <!-- RIGHT SIDEBAR: LOGISTICS & SMS SETTINGS -->
        <div class="col-xl-3 col-lg-4">
            
            <!-- PHOTO SECTION -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                <div class="card-body text-center p-4">
                    <div class="mb-4 d-flex justify-content-center">
                        @if ($student_image)
                            <div class="position-relative">
                                <img src="{{ $student_image->temporaryUrl() }}" class="rounded-4 shadow-sm border" style="width: 160px; height: 160px; object-fit: cover;">
                            </div>
                        @else
                            <div class="rounded-4 bg-light d-flex align-items-center justify-content-center border-2 border-dashed border-gray-300" style="width: 160px; height: 160px;">
                                <i class="fas fa-camera text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <label for="studentPhotoUpload" class="btn btn-primary btn-sm w-100 fw-bold"><i class="fas fa-image me-1"></i> Upload Photo</label>
                    <input type="file" id="studentPhotoUpload" wire:model="student_image" class="d-none">
                </div>
            </div>

            <!-- NOTIFICATION SETTINGS (The 'Finishing Touch') -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase"><i class="fas fa-bullhorn me-2"></i> Communications</h6>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_branded_sms" id="smsBranded">
                        <label class="form-check-label small fw-bold ms-2" for="smsBranded">Branded SMS</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_whatsapp_sms" id="smsWA">
                        <label class="form-check-label small fw-bold ms-2" for="smsWA">WhatsApp Notification</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_app_notification" id="smsApp">
                        <label class="form-check-label small fw-bold ms-2" for="smsApp">Parent App Portal</label>
                    </div>
                </div>
            </div>

            <!-- LOGISTICS -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" wire:model="is_transport_required" id="transCheck">
                        <label class="form-check-label small ms-2" for="transCheck">Transport Facility</label>
                    </div>
                    <textarea wire:model="remarks" class="form-control bg-light border-0 small" rows="3" placeholder="Additional Notes..."></textarea>
                </div>
            </div>

            <!-- TRIPLE ACTION SUBMIT BUTTONS (Matches your Screenshot) -->
            <div class="sticky-top" style="top: 90px;">
                <div class="d-grid gap-2">
                    <button type="button" wire:click="save('back')" class="btn btn-danger py-3 rounded-4 shadow-sm fw-bold border-0 transition-hover" style="background-color: #f08080;">
                        <i class="fas fa-arrow-left me-2"></i> Save and Go Back
                    </button>
                    
                    <button type="button" wire:click="save('new')" class="btn btn-primary py-3 rounded-4 shadow-sm fw-bold border-0 transition-hover" style="background-color: #6495ed;">
                        <i class="fas fa-plus-circle me-2"></i> Save and Add New
                    </button>
                    
                    <button type="button" wire:click="save('view')" class="btn btn-success py-3 rounded-4 shadow-sm fw-bold border-0 transition-hover" style="background-color: #66b2b2;">
                        <i class="fas fa-eye me-2"></i> Save and View
                    </button>

                    <button type="button" class="btn btn-light w-100 py-2 rounded-4 border text-muted small fw-bold mt-2">
                        DISCARD & EXIT
                    </button>
                </div>
            </div>

        </div>
    </div>
    </form>
    
    
</div>
