<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\Session;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\CampusClass;

class StudentAdmission extends Component
{
    use WithFileUploads;

    // Academic Selection
    public $session_id, $campus_id, $school_class_id, $section_id, $fee_plan_id;
    
    // Student Bio
    public $admission_no, $roll_no, $cnic_no, $first_name, $last_name, $date_of_birth, $place_of_birth, $gender = 'Male';
    public $religion = 'Islam', $blood_group, $nationality = 'Pakistani', $mother_tongue = 'Urdu', $disability, $previous_school;
    public $student_image;
    
    // Father Info
    public $father_name, $father_cnic, $father_phone, $father_occupation;
    
    // Mother Info
    public $mother_name, $mother_cnic;
    
    // Guardian Info
    public $guardian_name, $guardian_cnic, $guardian_phone, $guardian_relation, $guardian_occupation, $guardian_annual_income;
    
    // Student Bio (Enterprise)
    public $birth_city, $caste, $identification_mark, $bio_id;
    
    // Family (Enterprise)
    public $whatsapp_no, $father_qualification, $mother_phone, $mother_qualification;
    
    // Previous Education & Board (Enterprise)
    public $prev_degree, $prev_board, $prev_roll_no, $prev_total_marks, $prev_obtained_marks, $prev_grade;
    public $board_reg_no, $board_roll_no, $board_total_marks, $board_obtained_marks;
    
    // Office / Logistics (Enterprise)
    public $house, $class_of_admission;
    public $send_branded_sms = true, $send_whatsapp_sms = false, $send_app_notification = true;

    // Contact & Logistics
    public $address, $city, $is_transport_required = false, $is_hostel_required = false;
    public $admission_date, $remarks;

    public function mount()
    {
        $activeSession = Session::where('is_active', true)->first();
        if ($activeSession) { $this->session_id = $activeSession->id; }
        $this->admission_date = date('Y-m-d');
        $this->generateAdmissionNo();
    }

    public function generateAdmissionNo()
    {
        $count = Student::count() + 1;
        $year = date('Y');
        $this->admission_no = "ADM-" . $year . "-" . str_pad($count, 3, '0', STR_PAD_LEFT);
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
            'sessions' => \App\Models\Academic\Session::all(),
            'campuses' => \App\Models\Campus\Campus::all(),
            'fee_plans' => \App\Models\Finance\FeePlan::all(),
            'classes' => $availableClasses,
            'sections' => $availableSections,
        ])->layout('layouts.app');
    }

    public function save($action = 'new')
    {
        $this->validate([
            'session_id' => 'required',
            'campus_id' => 'required',
            'school_class_id' => 'required',
            'section_id' => 'required',
            'admission_no' => 'required|unique:students,admission_no',
            'first_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'admission_date' => 'required|date',
            'student_image' => 'nullable|image|max:1024',
        ]);

        $student = Student::create([
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
            'is_transport_required' => $this->is_transport_required,
            'is_hostel_required' => $this->is_hostel_required,
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
        ]);

        // Cloning Logic
        if ($this->fee_plan_id) {
            $masterParticulars = \App\Models\Finance\FeePlanParticular::where('fee_plan_id', $this->fee_plan_id)->get();
            $admissionMonth = strtolower(date('M', strtotime($student->admission_date)));

            foreach ($masterParticulars as $mp) {
                $data = [
                    'tenant_id' => $student->tenant_id,
                    'student_id' => $student->id,
                    'fee_particular_id' => $mp->fee_particular_id,
                    'actual_amount' => $mp->amount,
                    'jan' => $mp->jan, 'feb' => $mp->feb, 'mar' => $mp->mar, 'apr' => $mp->apr,
                    'may' => $mp->may, 'jun' => $mp->jun, 'jul' => $mp->jul, 'aug' => $mp->aug,
                    'sep' => $mp->sep, 'oct' => $mp->oct, 'nov' => $mp->nov, 'dec' => $mp->dec,
                ];

                // If it's a first-time fee, auto-check the admission month
                if ($mp->is_first_time) {
                    $data[$admissionMonth] = true;
                }

                \App\Models\Finance\StudentFeePlanItem::create($data);
            }
        }

        if ($this->student_image) {
            $student->update(['student_image' => $this->student_image->store('students', 'public')]);
        }

        session()->flash('message', "Student {$student->first_name} admitted successfully.");

        if ($action === 'back') { return redirect()->to('/manage-students'); }
        if ($action === 'view') { return redirect()->to('/student-profile/' . $student->id); }

        $this->reset();
        $this->generateAdmissionNo();
    }
}
