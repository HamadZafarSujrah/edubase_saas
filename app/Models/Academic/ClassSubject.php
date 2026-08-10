<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class ClassSubject extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'school_class_id', 'subject_id', 'marks_total'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
