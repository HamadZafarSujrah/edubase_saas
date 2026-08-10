<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Subject extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'subject_group_id', 'name', 'code'];

    public function group()
    {
        return $this->belongsTo(SubjectGroup::class, 'subject_group_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
}
