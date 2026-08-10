<div class="container-fluid pb-5 h-100">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm border-bottom">
        <div>
            <h2 class="h4 mb-0 fw-bold text-dark">
                <i class="fas fa-{{ $editing ? 'user-edit' : 'id-card' }} me-2 {{ $editing ? 'text-warning' : 'text-primary' }}"></i>
                {{ $editing ? 'Edit Student Record' : 'Premier Enterprise Admission Form' }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Admissions</a></li>
                    <li class="breadcrumb-item active">{{ $editing ? 'Edit Student' : 'New Admission' }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            @if(!$editing)
                <button class="btn btn-outline-primary btn-sm px-3"><i class="fas fa-cloud-upload-alt me-1"></i> Bulk Upload</button>
            @endif
            <a href="{{ route('students.directory') }}" class="btn btn-primary btn-sm px-3"><i class="fas fa-list me-1"></i> Student Directory</a>
        </div>
    </div>

    <!-- Global Notifications -->
    <div class="mb-4">
        @if (session()->has('success'))
            <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm animate__animated animate__shakeX">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm animate__animated animate__shakeX pb-2">
                <h6 class="fw-bold small mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Please correct the following errors before saving:</h6>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif    </div>

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
                            <select wire:model.live="school_class_id" class="form-select border-0 bg-light shadow-sm @error('school_class_id') is-invalid @enderror">
                                <option value="">Class</option>
                                @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Section</label>
                            <select wire:model.live="section_id" class="form-select border-0 bg-light shadow-sm @error('section_id') is-invalid @enderror">
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

            <!-- OFFICE INFORMATION -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-dark text-white py-2 small fw-bold border-0 text-center text-uppercase">
                    Office Information
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold tiny">House</label>
                            <select wire:model="house_id" class="form-select bg-light border-0">
                                <option value="">No House</option>
                                @foreach($houses as $h) <option value="{{ $h->id }}">{{ $h->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold tiny">Current Status</label>
                            <select wire:model="is_active" class="form-select bg-light border-0">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold tiny">Session</label>
                            <select wire:model="session_id" class="form-select bg-light border-0">
                                @foreach($sessions as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
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
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Student ID</label>
                                <input type="text" value="{{ $editing ? $system_id : 'Auto-Generated' }}" class="form-control bg-secondary bg-opacity-10 border-0 fw-bold text-muted" readonly disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Admission No <span class="text-danger">*</span></label>
                                <input type="text" wire:model="admission_no" class="form-control bg-light border-0 fw-bold text-primary @error('admission_no') is-invalid @enderror">
                                @error('admission_no') <div class="invalid-feedback d-block tiny">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
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
                                <input type="text" wire:model="cnic_no" class="form-control @error('cnic_no') is-invalid @enderror" placeholder="13 Digit Number" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                                    <option value="A+">A+</option><option value="A-">A-</option>
                                    <option value="B+">B+</option><option value="B-">B-</option>
                                    <option value="O+">O+</option><option value="O-">O-</option>
                                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
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

                <!-- VISITORS INFO -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-2 small fw-bold border-0 text-center text-uppercase">
                        Authorized Visitors / Emergency Contacts
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-primary text-white tiny text-center text-uppercase">
                                <tr>
                                    <th>Name</th><th>Phone</th><th>Relation</th><th>Address</th><th>Notes</th>
                                    <th width="50px"><button type="button" wire:click="addVisitor" class="btn btn-success btn-xs"><i class="fas fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visitors as $vIndex => $visitor)
                                <tr>
                                    <td><input type="text" wire:model="visitors.{{ $vIndex }}.name" class="form-control form-control-sm border-0 bg-light" placeholder="Name"></td>
                                    <td><input type="text" wire:model="visitors.{{ $vIndex }}.phone" class="form-control form-control-sm border-0 bg-light" placeholder="Phone" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                    <td><input type="text" wire:model="visitors.{{ $vIndex }}.relation" class="form-control form-control-sm border-0 bg-light" placeholder="Relation"></td>
                                    <td><input type="text" wire:model="visitors.{{ $vIndex }}.address" class="form-control form-control-sm border-0 bg-light" placeholder="Address"></td>
                                    <td><input type="text" wire:model="visitors.{{ $vIndex }}.notes" class="form-control form-control-sm border-0 bg-light" placeholder="Remarks"></td>
                                    <td class="text-center">
                                        @if(count($visitors) > 1)
                                            <button type="button" wire:click="removeVisitor({{ $vIndex }})" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. FAMILY INFORMATION (TAB) -->
            <div x-show="tab === 'family'" x-transition:enter="animate__animated animate__fadeIn" style="display: none;">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users me-2 text-warning"></i> 3. Family & Parental Connectivity</h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Family / Guardian Setup</label>
                                <select wire:model.live="family_setup_type" class="form-select border-0 bg-light">
                                    <option value="new">Add New Family (Default)</option>
                                    <option value="existing">Link with Existing Sibling/Family</option>
                                </select>
                            </div>
                            
                            <!-- Search UI for Existing Family -->
                            <div class="col-md-4 position-relative">
                                <label class="form-label small fw-bold">Search Existing Family</label>
                                <input type="text" wire:model.live="family_search" 
                                       class="form-control border-0 bg-light shadow-sm" 
                                       placeholder="ID, Father Name or CNIC..."
                                       {{ $family_setup_type !== 'existing' ? 'disabled' : '' }}>
                                
                                @if(!empty($suggested_families))
                                    <div class="position-absolute w-100 bg-white shadow-lg rounded-3 z-3 mt-1 overflow-hidden" style="left: 0; top: 100%;">
                                        @foreach($suggested_families as $f)
                                            <button type="button" wire:click="selectFamily({{ $f->id }})" class="btn btn-link w-100 text-start text-decoration-none border-bottom p-2 hover-bg-light">
                                                <div class="fw-bold small text-dark">{{ $f->family_no }} - {{ $f->father_name }}</div>
                                                <div class="tiny text-muted">CNIC: {{ $f->father_cnic }} | Students: {{ $f->students_count ?? 0 }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                @if($existing_family_id)
                                    <div class="tiny text-success fw-bold position-absolute" style="bottom: -20px;"><i class="fas fa-link me-1"></i> Family Linked: {{ $family_no }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Assigned Family No</label>
                                <input type="text" wire:model="family_no" class="form-control border-0 bg-light" readonly>
                            </div>

                            <!-- FATHER / MOTHER FIELDS -->
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-primary">Father Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="father_name" class="form-control border-primary border-opacity-25 @error('father_name') is-invalid @enderror">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-primary">Father CNIC</label>
                                <input type="text" wire:model="father_cnic" class="form-control border-primary border-opacity-10" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Mobile <span class="text-danger">*</span></label>
                                <input type="text" wire:model="father_phone" class="form-control bg-light border-0" placeholder="03xxxxxxxxx" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-success"><i class="fab fa-whatsapp me-1"></i> WhatsApp No</label>
                                <input type="text" wire:model="whatsapp_no" class="form-control border-success border-opacity-25" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger">Mother Name</label>
                                <input type="text" wire:model="mother_name" class="form-control border-danger border-opacity-10">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger">Mother CNIC</label>
                                <input type="text" wire:model="mother_cnic" class="form-control border-danger border-opacity-10" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger">Mother Mobile</label>
                                <input type="text" wire:model="mother_phone" class="form-control border-danger border-opacity-10" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Father Qualification</label>
                                <input type="text" wire:model="father_qualification" class="form-control bg-light border-0">
                            </div>

                            <div class="col-md-6 mt-4">
                                <label class="form-label small fw-bold"><i class="fas fa-map-marker-alt text-danger me-1"></i> Residential Address</label>
                                <textarea wire:model="address" class="form-control bg-light border-0" rows="3" placeholder="Residential Street Address..."></textarea>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label class="form-label small fw-bold"><i class="fas fa-user-shield text-info me-1"></i> Guardian Information (Optional)</label>
                                <div class="row g-2">
                                    <div class="col-6"><input type="text" wire:model="guardian_name" class="form-control form-control-sm bg-light" placeholder="Guardian Name"></div>
                                    <div class="col-6"><input type="text" wire:model="guardian_phone" class="form-control form-control-sm bg-light" placeholder="Guardian Phone" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                                    <div class="col-12"><input type="text" wire:model="guardian_cnic" class="form-control form-control-sm bg-light" placeholder="Guardian CNIC" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. HISTORY & ATTACHMENTS (TAB) -->
            <div x-show="tab === 'history'" x-transition:enter="animate__animated animate__fadeIn" style="display: none;">
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-2 small fw-bold border-0">
                        <i class="fas fa-history me-2"></i> 4. Previous Qualification
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="bg-light tiny fw-bold text-uppercase text-center">
                                <tr><th>Degree/Class</th><th>Board/University</th><th>Roll No</th><th>Total Marks</th><th>Obtained</th><th>Grade</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" wire:model="prev_degree" class="form-control form-control-sm border-0 text-center"></td>
                                    <td><input type="text" wire:model="prev_board" class="form-control form-control-sm border-0 text-center"></td>
                                    <td><input type="text" wire:model="prev_roll_no" class="form-control form-control-sm border-0 text-center" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                    <td><input type="text" wire:model="prev_total_marks" class="form-control form-control-sm border-0 text-center" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                    <td><input type="text" wire:model="prev_obtained_marks" class="form-control form-control-sm border-0 text-center" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                    <td><input type="text" wire:model="prev_grade" class="form-control form-control-sm border-0 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BOARD INFO -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-info bg-opacity-10 py-3 border-0">
                        <h6 class="mb-0 fw-bold text-info"><i class="fas fa-clipboard-check me-2"></i> 5. Board Information (Current)</h6>
                    </div>
                    <div class="card-body p-4 pt-0 mt-3">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label small fw-bold tiny">Board Reg No</label><input type="text" wire:model="board_reg_no" class="form-control bg-light border-0 shadow-sm" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-3"><label class="form-label small fw-bold tiny">Board Roll No</label><input type="text" wire:model="board_roll_no" class="form-control bg-light border-0 shadow-sm" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-3"><label class="form-label small fw-bold tiny">Total Marks</label><input type="text" wire:model="board_total_marks" class="form-control bg-light border-0 shadow-sm" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-3"><label class="form-label small fw-bold tiny">Obtained Marks</label><input type="text" wire:model="board_obtained_marks" class="form-control bg-light border-0 shadow-sm" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                        </div>
                    </div>
                </div>

                <!-- EXISTING ATTACHMENTS (READ-ONLY, EDIT MODE) -->
                @if($editing && count($existing_documents) > 0)
                    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-secondary text-white py-2 small fw-bold border-0 text-center text-uppercase">Existing Attachments on File</div>
                        <div class="list-group list-group-flush">
                            @foreach($existing_documents as $doc)
                                <a href="{{ $doc['url'] }}" target="_blank" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-paperclip me-2 text-muted"></i>{{ $doc['title'] }}</span>
                                    <i class="fas fa-external-link-alt tiny text-muted"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ATTACHMENTS (DYNAMIC ROWS) -->
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-2 small fw-bold border-0 text-center text-uppercase">
                        {{ $editing ? 'Add New Attachments' : 'Enrollment Attachments' }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 align-middle">
                            <thead class="bg-primary text-white tiny fw-bold text-uppercase text-center">
                                <tr>
                                    <th width="45%">Document Title (e.g. SLC, B-Form)</th>
                                    <th width="45%">Choose File</th>
                                    <th width="10%"><button type="button" wire:click="addAttachment" class="btn btn-success btn-sm w-100 rounded-0"><i class="fas fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attachment_rows as $index => $attachment)
                                <tr>
                                    <td><input type="text" wire:model="attachment_rows.{{ $index }}.title" class="form-control form-control-sm border-0 bg-light" placeholder="Enter title..."></td>
                                    <td>
                                        <input type="file" wire:model="attachment_rows.{{ $index }}.file" class="form-control form-control-sm border-0 bg-light">
                                        <div wire:loading wire:target="attachment_rows.{{ $index }}.file" class="tiny text-primary fw-bold mt-1">Uploading...</div>
                                    </td>
                                    <td class="text-center bg-light">
                                        @if(count($attachment_rows) > 1)
                                            <button type="button" wire:click="removeAttachment({{ $index }})" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-light p-2 tiny text-muted"><i class="fas fa-info-circle me-1"></i> Acceptable formats: JPG, PNG, PDF (Max 1MB per file)</div>
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
                
                <span x-show="tab === 'history'" class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Data Capture Complete. Proceed to Save!</span>
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
                                <button type="button" wire:click="$set('student_image', null)" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle"><i class="fas fa-times"></i></button>
                            </div>
                        @else
                            <div class="rounded-4 bg-light d-flex align-items-center justify-content-center border-2 border-dashed border-gray-300 pointer-event" style="width: 160px; height: 160px;">
                                <i class="fas fa-camera text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <label for="studentPhotoUpload" class="btn btn-primary btn-sm w-100 fw-bold"><i class="fas fa-image me-1"></i> Upload Profile Photo</label>
                    <input type="file" id="studentPhotoUpload" wire:model="student_image" class="d-none">
                    <div wire:loading wire:target="student_image" class="tiny text-primary mt-1">Processing image...</div>
                </div>
            </div>

            <!-- COMMUNICATIONS -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase"><i class="fas fa-bullhorn me-2 text-primary"></i> Notifications</h6>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_branded_sms" id="smsBranded">
                        <label class="form-check-label small fw-bold ms-2" for="smsBranded">Branded SMS Alerts</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_whatsapp_sms" id="smsWA">
                        <label class="form-check-label small fw-bold ms-2" for="smsWA">WhatsApp Updates</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="send_app_notification" id="smsApp">
                        <label class="form-check-label small fw-bold ms-2" for="smsApp">Parent Mobile App</label>
                    </div>
                </div>
            </div>

            <!-- LOGISTICS -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase"><i class="fas fa-bus me-2 text-primary"></i> Services</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" wire:model="is_transport_required" id="transCheck">
                        <label class="form-check-label small ms-2" for="transCheck">Transport Facility Required</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" wire:model="is_hostel_required" id="hostelCheck">
                        <label class="form-check-label small ms-2" for="hostelCheck">Hostel Residency Required</label>
                    </div>
                    <textarea wire:model="remarks" class="form-control bg-light border-0 small" rows="2" placeholder="Internal Admission Notes..."></textarea>
                </div>
            </div>

            <!-- TRIPLE ACTION SUBMIT BUTTONS -->
            <div class="sticky-top" style="top: 90px; z-index: 10;">
                <div class="d-grid gap-2 shadow-lg p-2 bg-white rounded-4 border">

                    @if($editing)
                        {{-- Edit mode: single update button --}}
                        <button type="button" wire:click="save('back')" class="btn btn-warning py-3 rounded-4 fw-bold border-0">
                            <i class="fas fa-save me-2"></i> Update Student Record
                        </button>
                        <a href="{{ route('students.directory') }}" class="btn btn-light py-2 rounded-4 border text-muted small fw-bold">
                            <i class="fas fa-arrow-left me-1"></i> Cancel & Go Back
                        </a>
                    @else
                        {{-- Create mode: original triple-action buttons --}}
                        <button type="button" wire:click="save('back')" class="btn btn-danger py-3 rounded-4 fw-bold border-0 transition-hover" style="background-color: #f08080;">
                            <i class="fas fa-save me-2"></i> Save &amp; Go Back
                        </button>

                        <button type="button" wire:click="save('new')" class="btn btn-primary py-3 rounded-4 fw-bold border-0 transition-hover" style="background-color: #6495ed;">
                            <i class="fas fa-plus-circle me-2"></i> Save &amp; Add New
                        </button>

                        <button type="button" wire:click="save('view')" class="btn btn-success py-3 rounded-4 fw-bold border-0 transition-hover" style="background-color: #66b2b2;">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Save &amp; Create Fee Plan
                        </button>

                        <button type="button" class="btn btn-light w-100 py-2 rounded-4 border text-muted small fw-bold mt-2" onclick="confirm('Discard all changes?') && window.location.reload()">
                            DISCARD &amp; RESET
                        </button>
                    @endif

                    <div wire:loading wire:target="save" class="text-center tiny fw-bold text-primary animate__animated animate__pulse animate__infinite">
                        <i class="fas fa-spinner fa-spin me-2"></i> SECURING ENROLLMENT...
                    </div>
                </div>
            </div>


            </div>
        </div>
    </form>
    <style>
        .hs-6 { font-size: 1.1rem; }
        .tiny { font-size: 0.75rem; }
        .is-invalid { border: 1px solid #dc3545 !important; background-color: #fff8f8; }
        .hover-bg-light:hover { background-color: #f8f9fa; }
        [x-cloak] { display: none !important; }
        .pointer-event { cursor: pointer; }
        .transition-hover:hover { filter: brightness(90%); transform: translateY(-1px); }
    </style>
</div>
