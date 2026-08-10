<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\User;

class PtmSchedule extends Model
{
    use HasTenant;

    protected $table = 'ptm_schedules';

    protected $fillable = [
        'tenant_id', 'campus_id', 'school_class_id', 'title', 'meeting_date', 'start_time', 'end_time', 'description', 'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
