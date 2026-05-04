<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class GLAccountGroup extends Model
{
    use HasTenant;

    protected $table = 'gl_groups';

    protected $fillable = [
        'tenant_id', 'name', 'account_class', 'is_inactive', 'created_by', 'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function accounts()
    {
        return $this->hasMany(GLAccount::class, 'group_id');
    }
}
