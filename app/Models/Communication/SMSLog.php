<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Student\Student;
use App\Models\User;

class SMSLog extends Model
{
    use HasTenant;

    protected $table = 'sms_logs';

    protected $fillable = [
        'tenant_id', 'student_id', 'phone', 'recipient_name', 'message',
        'type', 'status', 'gateway', 'sent_by', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
