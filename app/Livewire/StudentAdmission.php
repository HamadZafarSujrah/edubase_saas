<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Student\Student;
use App\Models\Student\Family;
use App\Models\Student\StudentVisitor;
use App\Models\Student\StudentDocument;
use App\Models\Campus\Campus;
use App\Models\Academic\Session;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\CampusClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentAdmission extends Component
{
    use WithFileUploads, AuthorizesRequests;

    // Academic Selection
    public $session_id, $campus_id, $school_class_id, $section_id, $fee_plan_id;
    
    // Student Bio
    public $admission_no, $roll_no, $cnic_no, $first_name, $last_name, $date_of_birth, $place_of_birth, $gender = 'Male';
    public $religion = 'Islam', $blood_group, $nationality = 'Pakistani', $mother_tongue = 'Urdu', $disability, $previous_school;
    public $student_image;
    
    // Father Info
    public $father_name, $father_cnic, $father_phone, $father_occupation, $father_profession, $father_department, $father_posting_city, $father_email;
    
    // Mother Info
    public $mother_name, $mother_cnic, $mother_email;
    
    // Guardian Info
    public $guardian_name, $guardian_cnic, $guardian_phone, $guardian_relation, $guardian_occupation, $guardian_annual_income;
    public $family_setup_type = 'new', $existing_family_id, $family_no;
    
    // Student Bio (Enterprise)
    public $birth_city, $caste, $identification_mark, $bio_id;
    
    // Family (Enterprise)
    public $whatsapp_no, $father_qualification, $mother_phone, $mother_qualification;
    public $current_address, $permanent_address;
    
    // Previous Education & Board (Enterprise)
    public $prev_degree, $prev_board, $prev_roll_no, $prev_total_marks, $prev_obtained_marks, $prev_grade;
    public $board_reg_no, $board_roll_no, $board_total_marks, $board_obtained_marks;
    
    // Office / Logistics (Enterprise)
    public $house, $class_of_admission, $group_discipline, $current_status = 'Active';
    public $send_branded_sms = true, $send_whatsapp_sms = false, $send_app_notification = true;

    // Visitors Info
    public $visitors = [];

    // Address & Services
    public $address, $city, $is_transport_required = false, $is_hostel_required = false;
    public $admission_date, $remarks;

    // Attachments
    public $attachment_rows = [];

    // Search
    public $family_search = '';
    public $suggested_families = [];
    
    // Auth & Context
    public $current_tenant_id;

    // Edit mode
    public $student_id = null;
    public $system_id = null;
    public $editing    = false;

    public function mount()
    {
        $this->current_tenant_id = session('tenant_id') ?? auth()->user()->tenant_id;

        $this->attachment_rows = [
            ['title' => '', 'file' => null]
        ];

        $this->visitors = [
            ['name' => '', 'phone' => '', 'relation' => '', 'address' => '', 'notes' => '']
        ];

        $activeSession = Session::where('is_active', true)->first();
        if ($activeSession) { $this->session_id = $activeSession->id; }
        $this->admission_date = date('Y-m-d');

        // ---- Edit mode: pre-fill from existing student ----
        $editId = request()->query('edit');
        if ($editId) {
            $s = Student::find($editId);
            if ($s) {
                $this->editing    = true;
                $this->student_id = $s->id;
                // Academic
                $this->session_id      = $s->session_id;
                $this->campus_id       = $s->campus_id;
                $this->school_class_id = $s->school_class_id;
                $this->section_id      = $s->section_id;
                $this->fee_plan_id     = $s->fee_plan_id;
                // Bio
                $this->system_id      = $s->system_id;
                $this->admission_no   = $s->admission_no;
                $this->roll_no        = $s->roll_no;
                $this->cnic_no        = $s->cnic_no;
                $this->first_name     = $s->first_name;
                $this->last_name      = $s->last_name;
                $this->date_of_birth  = $s->date_of_birth?->format('Y-m-d');
                $this->place_of_birth = $s->place_of_birth;
                $this->gender         = $s->gender ?? 'Male';
                $this->religion       = $s->religion ?? 'Islam';
                $this->blood_group    = $s->blood_group;
                $this->nationality    = $s->nationality ?? 'Pakistani';
                $this->mother_tongue  = $s->mother_tongue ?? 'Urdu';
                $this->disability     = $s->disability;
                $this->previous_school= $s->previous_school;
                $this->birth_city     = $s->birth_city;
                $this->caste          = $s->caste;
                $this->identification_mark = $s->identification_mark;
                $this->bio_id         = $s->bio_id;
                // Father
                $this->father_name          = $s->father_name;
                $this->father_cnic          = $s->father_cnic;
                $this->father_phone         = $s->father_phone;
                $this->father_occupation    = $s->father_occupation;
                $this->father_qualification = $s->father_qualification;
                // Mother
                $this->mother_name          = $s->mother_name;
                $this->mother_cnic          = $s->mother_cnic;
                $this->mother_phone         = $s->mother_phone;
                $this->mother_qualification = $s->mother_qualification;
                $this->whatsapp_no          = $s->whatsapp_no;
                // Guardian
                $this->guardian_name             = $s->guardian_name;
                $this->guardian_cnic             = $s->guardian_cnic;
                $this->guardian_phone            = $s->guardian_phone;
                $this->guardian_relation         = $s->guardian_relation;
                $this->guardian_occupation       = $s->guardian_occupation;
                $this->guardian_annual_income    = $s->guardian_annual_income;
                // Address & logistics
                $this->address               = $s->address;
                $this->city                  = $s->city;
                $this->is_transport_required = $s->is_transport_required;
                $this->is_hostel_required    = $s->is_hostel_required;
                $this->admission_date        = $s->admission_date?->format('Y-m-d');
                $this->remarks               = $s->remarks;
                return; // Skip auto-generating new numbers in edit mode
            }
        }

        $this->generateAdmissionNo();
        $this->generateFamilyNo();
    }

    public function generateAdmissionNo()
    {
        $count = Student::count() + 1;
        $this->admission_no = "ADM-" . date('Y') . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function generateFamilyNo()
    {
        $count = Family::count() + 1;
        $this->family_no = "FAM-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function updatedFamilySearch()
    {
        if (strlen($this->family_search) > 2) {
            $this->suggested_families = Family::where('family_no', 'like', $this->family_search . '%')
                ->orWhere('father_name', 'like', '%' . $this->family_search . '%')
                ->orWhere('father_cnic', 'like', $this->family_search . '%')
                ->limit(5)
                ->get();
        } else {
            $this->suggested_families = [];
        }
    }

    public function selectFamily($id)
    {
        $family = Family::find($id);
        if ($family) {
            $this->existing_family_id = $family->id;
            $this->family_no = $family->family_no;
            $this->father_name = $family->father_name;
            $this->father_cnic = $family->father_cnic;
            $this->mother_name = $family->mother_name;
            $this->mother_cnic = $family->mother_cnic;
            $this->guardian_name = $family->guardian_name;
            $this->guardian_phone = $family->guardian_phone;
            $this->address = $family->address;
            $this->family_search = $family->family_no . ' (' . $family->father_name . ')';
            $this->suggested_families = [];
        }
    }

    public function render()
    {
        $availableClasses = [];
        if ($this->campus_id) {
            $classIds = CampusClass::where('campus_id', $this->campus_id)->pluck('school_class_id');
            $availableClasses = SchoolClass::whereIn('id', $classIds)->get();
        }

        $availableSections = [];
        if ($this->school_class_id) {
            $availableSections = Section::where('school_class_id', $this->school_class_id)->get();
        }

        return view('livewire.student-admission', [
            'sessions' => Session::all(),
            'campuses' => Campus::all(),
            'fee_plans' => \App\Models\Finance\FeePlan::all(),
            'classes' => $availableClasses,
            'sections' => $availableSections,
        ])->layout('layouts.app');
    }

    public function save($action = 'new')
    {
        if ($this->editing && $this->student_id) {
            $studentToUpdate = Student::findOrFail($this->student_id);
            $this->authorize('update', $studentToUpdate);
        } else {
            $this->authorize('create', Student::class);
        }

        // When editing, ignore the current student's own admission_no for uniqueness check
        $admissionNoRule = $this->editing
            ? 'required|unique:students,admission_no,' . $this->student_id
            : 'required|unique:students,admission_no';

        $this->validate([
            'session_id' => 'required',
            'campus_id' => 'required',
            'school_class_id' => 'required',
            'section_id' => 'nullable',
            'admission_no' => $admissionNoRule,
            'first_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'admission_date' => 'required|date',
            'student_image' => 'nullable|image|max:1024',
            'cnic_no' => 'nullable|digits:13',
            'father_cnic' => 'nullable|digits:13',
            'mother_cnic' => 'nullable|digits:13',
            'guardian_cnic' => 'nullable|digits:13',
            'father_phone' => 'nullable|digits:11',
            'mother_phone' => 'nullable|digits:11',
            'whatsapp_no' => 'nullable|digits:11',
        ]);

        DB::beginTransaction();
        try {
            // 1. Handle Family
            $familyId = $this->existing_family_id;
            if ($this->family_setup_type === 'new') {
                $family = Family::create([
                    'tenant_id' => $this->current_tenant_id,
                    'family_no' => $this->family_no,
                    'father_name' => $this->father_name,
                    'father_cnic' => $this->father_cnic,
                    'mother_name' => $this->mother_name,
                    'mother_cnic' => $this->mother_cnic,
                    'email' => $this->father_email,
                    'guardian_name' => $this->guardian_name,
                    'guardian_phone' => $this->guardian_phone,
                    'address' => $this->address,
                ]);
                $familyId = $family->id;
            }

            // 2. Create or Update Student
            $studentData = [
                'tenant_id' => $this->current_tenant_id,
                'family_id' => $familyId,
                'session_id' => $this->session_id,
                'campus_id' => $this->campus_id,
                'school_class_id' => $this->school_class_id,
                'section_id' => $this->section_id,
                'fee_plan_id' => $this->fee_plan_id,
                'admission_no' => $this->admission_no,
                'roll_no' => $this->roll_no,
                'cnic_no' => $this->cnic_no,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->father_email,
                'date_of_birth' => $this->date_of_birth,
                'place_of_birth' => $this->place_of_birth,
                'gender' => $this->gender,
                'religion' => $this->religion,
                'blood_group' => $this->blood_group,
                'nationality' => $this->nationality,
                'mother_tongue' => $this->mother_tongue,
                'disability' => $this->disability,
                'previous_school' => $this->previous_school,
                'father_name' => $this->father_name,
                'father_cnic' => $this->father_cnic,
                'father_phone' => $this->father_phone,
                'father_occupation' => $this->father_occupation,
                'mother_name' => $this->mother_name,
                'mother_cnic' => $this->mother_cnic,
                'guardian_name' => $this->guardian_name,
                'guardian_cnic' => $this->guardian_cnic,
                'guardian_phone' => $this->guardian_phone,
                'guardian_relation' => $this->guardian_relation,
                'guardian_occupation' => $this->guardian_occupation,
                'guardian_annual_income' => $this->guardian_annual_income ?: 0,
                'address' => $this->address,
                'city' => $this->city,
                'is_transport_required' => (bool)$this->is_transport_required,
                'is_hostel_required' => (bool)$this->is_hostel_required,
                'admission_date' => $this->admission_date,
                'remarks' => $this->remarks,
                'birth_city' => $this->birth_city,
                'caste' => $this->caste,
                'identification_mark' => $this->identification_mark,
                'bio_id' => $this->bio_id,
                'whatsapp_no' => $this->whatsapp_no,
                'father_qualification' => $this->father_qualification,
                'mother_phone' => $this->mother_phone,
                'mother_qualification' => $this->mother_qualification,
                'prev_degree' => $this->prev_degree,
                'prev_board' => $this->prev_board,
                'prev_roll_no' => $this->prev_roll_no,
                'prev_total_marks' => $this->prev_total_marks,
                'prev_obtained_marks' => $this->prev_obtained_marks,
                'prev_grade' => $this->prev_grade,
                'board_reg_no' => $this->board_reg_no,
                'board_roll_no' => $this->board_roll_no,
                'board_total_marks' => $this->board_total_marks,
                'board_obtained_marks' => $this->board_obtained_marks,
                'house' => $this->house,
                'class_of_admission' => $this->class_of_admission,
                'send_branded_sms' => $this->send_branded_sms,
                'send_whatsapp_sms' => $this->send_whatsapp_sms,
                'send_app_notification' => $this->send_app_notification,
                'is_active' => true,
            ];

            if ($this->editing && $this->student_id) {
                $student = Student::findOrFail($this->student_id);
                $student->update($studentData);
            } else {
                $tenant = \App\Models\Tenant::find($this->current_tenant_id);
                $prefix = $tenant->student_prefix ?? 'STD-';
                $number = $tenant->next_student_number ?? 1;
                
                $studentData['tenant_id'] = $this->current_tenant_id;
                $studentData['system_id'] = $prefix . $number;
                
                $student = Student::create($studentData);
                
                // Increment for next student
                $tenant->increment('next_student_number');
            }

            // 3. Clone Fee Plan if assigned (Disabled as per user request - manual creation required)
            // The fee_plan_id is still saved to the Student model above, but the actual calculation lines
            // will not be automatically generated here. They must be generated from the 'Create Fee Plans' menu.

            // 4. Save Visitors
            foreach ($this->visitors as $visitor) {
                if ($visitor['name']) {
                    StudentVisitor::create(array_merge($visitor, [
                        'tenant_id' => $this->current_tenant_id,
                        'student_id' => $student->id
                    ]));
                }
            }

            // 5. Save Attachments
            foreach ($this->attachment_rows as $row) {
                if ($row['file'] && $row['title']) {
                    $path = $row['file']->store('documents', 'public');
                    StudentDocument::create([
                        'tenant_id' => $this->current_tenant_id,
                        'student_id' => $student->id,
                        'title' => $row['title'],
                        'file_path' => $path,
                        'file_type' => $row['file']->getClientOriginalExtension(),
                    ]);
                }
            }

            // 6. Handle Student Image with MediaLibrary
            if ($this->student_image) {
                $student->addMedia($this->student_image->getRealPath())
                    ->usingFileName($this->student_image->getClientOriginalName())
                    ->toMediaCollection('profile_photos');
            }

            DB::commit();

            if ($this->editing) {
                session()->flash('message', "Student {$student->first_name}'s record updated successfully.");
                return redirect()->route('students.directory');
            }

            session()->flash('success', "Student {$student->first_name} admitted successfully.");

            if ($action === 'view') { return redirect()->route('create.fee-plan.form', ['sid' => $student->id]); }

            $this->reset();
            $this->mount();
            return redirect()->to(request()->header('Referer')); // refresh

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error admitting student: ' . $e->getMessage());
        }
    }

    public function addAttachment() { $this->attachment_rows[] = ['title' => '', 'file' => null]; }
    public function removeAttachment($index) { 
        unset($this->attachment_rows[$index]); 
        $this->attachment_rows = array_values($this->attachment_rows); 
    }

    public function addVisitor() { $this->visitors[] = ['name' => '', 'phone' => '', 'relation' => '', 'address' => '', 'notes' => '']; }
    public function removeVisitor($index) { 
        unset($this->visitors[$index]); 
        $this->visitors = array_values($this->visitors); 
    }
}
