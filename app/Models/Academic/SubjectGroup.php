<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class SubjectGroup extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
