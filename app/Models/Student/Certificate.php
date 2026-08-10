<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class Certificate extends Model
{
    use HasTenant;

    protected $table = 'certificates_log';

    protected $fillable = [
        'tenant_id', 'student_id', 'certificate_type', 'title', 'body_text', 'issued_date', 'issued_by',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
