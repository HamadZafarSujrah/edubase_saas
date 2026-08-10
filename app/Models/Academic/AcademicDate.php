<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class AcademicDate extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'title', 'start_date', 'end_date', 'type', 'description'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
