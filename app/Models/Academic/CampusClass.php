<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;

class CampusClass extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'school_class_id',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
