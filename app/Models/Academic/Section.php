<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Section extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'school_class_id',
        'name',          // e.g. "A", "B", "Rose", "Tulip"
        'room_number',   // optional physical room
        'capacity',      // Maximum students allowed
        'is_active',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
