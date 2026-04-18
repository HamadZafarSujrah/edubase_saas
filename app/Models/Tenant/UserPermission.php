<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',       // e.g. 'Student Management', 'Fee Management' (matching the image categories)
        'permission',   // e.g. 'setup_campus', 'add_student', 'edit_student'
        'is_granted',   // boolean to allow specific granular control per user
    ];

    /**
     * Relationships 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
