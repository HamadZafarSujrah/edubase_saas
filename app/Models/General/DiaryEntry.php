<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Academic\Section;
use App\Models\User;

class DiaryEntry extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'section_id', 'date', 'subject', 'content', 'created_by'];

    protected $casts = [
        'date' => 'date',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
