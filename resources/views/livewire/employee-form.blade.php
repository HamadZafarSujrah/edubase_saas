<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-tie me-2 text-primary"></i> {{ $editing ? 'Edit Employee' : 'Add New Employee' }}</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hrm.employees.directory') }}" class="text-decoration-none">Employee Directory</a></li>
                    <li class="breadcrumb-item active">{{ $editing ? 'Edit' : 'Add New' }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('hrm.employees.directory') }}" class="btn btn-outline-secondary rounded-3"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">Personal Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Employee No. <span class="text-danger">*</span></label>
                            <input type="text" wire:model="emp_no" class="form-control border-0 bg-light shadow-sm">
                            @error('emp_no') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">First Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="first_name" class="form-control border-0 bg-light shadow-sm">
                            @error('first_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Last Name</label>
                            <input type="text" wire:model="last_name" class="form-control border-0 bg-light shadow-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">CNIC</label>
                            <input type="text" wire:model="cnic" class="form-control border-0 bg-light shadow-sm" placeholder="XXXXX-XXXXXXX-X">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Gender</label>
                            <select wire:model="gender" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Join Date</label>
                            <input type="date" wire:model="join_date" class="form-control border-0 bg-light shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Phone</label>
                            <input type="text" wire:model="phone" class="form-control border-0 bg-light shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Email</label>
                            <input type="email" wire:model="email" class="form-control border-0 bg-light shadow-sm">
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Address</label>
                            <textarea wire:model="address" rows="2" class="form-control border-0 bg-light shadow-sm"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">HR Assignment</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Campus</label>
                            <select wire:model="campus_id" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select...</option>
                                @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Department</label>
                            <select wire:model.live="department_id" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select...</option>
                                @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Designation</label>
                            <select wire:model="designation_id" class="form-select border-0 bg-light shadow-sm" @if(!$department_id) disabled @endif>
                                <option value="">Select...</option>
                                @foreach($designations as $des) <option value="{{ $des->id }}">{{ $des->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Shift</label>
                            <select wire:model="shift_id" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select...</option>
                                @foreach($shifts as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Employment Type</label>
                            <select wire:model="employment_type_id" class="form-select border-0 bg-light shadow-sm">
                                <option value="">Select...</option>
                                @foreach($employmentTypes as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Status</label>
                            <select wire:model="status" class="form-select border-0 bg-light shadow-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($customFieldDefs) > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">Custom Fields</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach($customFieldDefs as $def)
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">{{ $def->label }}</label>
                                <input type="text" wire:model="custom_fields.{{ $def->label }}" class="form-control border-0 bg-light shadow-sm">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Documents -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">Security / Personal &amp; Resume Documents</h6>
                </div>
                <div class="card-body p-4">
                    @if($editing && count($existing_documents) > 0)
                        <div class="mb-3">
                            <div class="small fw-bold text-muted mb-2">Existing Documents on File</div>
                            <div class="list-group">
                                @foreach($existing_documents as $doc)
                                    <a href="{{ $doc['url'] }}" target="_blank" class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-paperclip me-2 text-muted"></i>{{ $doc['title'] }}</span>
                                        <i class="fas fa-external-link-alt tiny text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th width="45%">Document Title (e.g. CNIC, Resume, Police Clearance)</th>
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
                                    <div wire:loading wire:target="attachment_rows.{{ $index }}.file" class="small text-primary fw-bold mt-1">Uploading...</div>
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
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-primary mb-0">Photo</h6>
                </div>
                <div class="card-body p-4 text-center">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="rounded-3 mb-3 shadow-sm" style="width:150px;height:150px;object-fit:cover;">
                    @elseif($existing_photo_url)
                        <img src="{{ $existing_photo_url }}" class="rounded-3 mb-3 shadow-sm" style="width:150px;height:150px;object-fit:cover;">
                    @else
                        <div class="rounded-3 mb-3 bg-light d-flex align-items-center justify-content-center mx-auto" style="width:150px;height:150px;">
                            <i class="fas fa-user fa-3x text-muted opacity-25"></i>
                        </div>
                    @endif
                    <input type="file" wire:model="photo" class="form-control border-0 bg-light shadow-sm">
                    <div wire:loading wire:target="photo" class="small text-primary fw-bold mt-1">Uploading...</div>
                    @error('photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="button" wire:click="save" class="btn btn-primary w-100 fw-bold py-3 rounded-3 shadow-sm">
                <i class="fas fa-save me-2"></i> {{ $editing ? 'Update Employee' : 'Save Employee' }}
            </button>
        </div>
    </div>
</div>
