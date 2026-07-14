<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class Role extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the permissions associated with this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Get the users assigned to this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
