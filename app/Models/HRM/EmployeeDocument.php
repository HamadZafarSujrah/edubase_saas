<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class EmployeeDocument extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'employee_id', 'title', 'file_path', 'file_type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
