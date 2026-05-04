<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Academic\Session;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;

class Student extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'session_id',
        'campus_id',
        'school_class_id',
        'section_id',
        'system_id',
        'admission_no',
        'roll_no',
        'cnic_no',
        'first_name',
        'last_name',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'religion',
        'blood_group',
        'nationality',
        'mother_tongue',
        'disability',
        'previous_school',
        'father_name',
        'father_cnic',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_cnic',
        'guardian_name',
        'guardian_cnic',
        'guardian_phone',
        'guardian_relation',
        'guardian_occupation',
        'guardian_annual_income',
        'address',
        'city',
        'is_transport_required',
        'is_hostel_required',
        'admission_date',
        'is_active',
        'student_image',
        'remarks',
        'fee_plan_id',
        'birth_city', 'caste', 'identification_mark', 'bio_id',
        'whatsapp_no', 'father_qualification', 'mother_phone', 'mother_qualification',
        'prev_degree', 'prev_board', 'prev_roll_no', 'prev_total_marks', 'prev_obtained_marks', 'prev_grade',
        'board_reg_no', 'board_roll_no', 'board_total_marks', 'board_obtained_marks',
        'house', 'class_of_admission', 'attachments',
        'send_branded_sms', 'send_whatsapp_sms', 'send_app_notification',
        'fee_plan_effect_from', 'fee_plan_increment', 'fee_plan_year', 'fee_plan_discount_type', 'fee_plan_notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'is_active' => 'boolean',
        'is_transport_required' => 'boolean',
        'is_hostel_required' => 'boolean',
        'guardian_annual_income' => 'decimal:2',
    ];

    public function session() { return $this->belongsTo(Session::class); }
    public function academicSession() { return $this->belongsTo(Session::class, 'session_id'); }
    public function campus() { return $this->belongsTo(Campus::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function section() { return $this->belongsTo(Section::class); }
    public function feePlan() { return $this->belongsTo(\App\Models\Finance\FeePlan::class); }
    public function feePlanItems() { return $this->hasMany(\App\Models\Finance\StudentFeePlanItem::class); }
    public function challans() { return $this->hasMany(\App\Models\Finance\Challan::class); }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
