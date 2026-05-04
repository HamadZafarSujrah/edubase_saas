<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;
use App\Models\Finance\GLAccountGroup;

class GLAccount extends Model
{
    use HasTenant;

    protected $table = 'gl_accounts';

    protected $fillable = [
        'tenant_id', 'group_id', 'code', 'name', 'type', 'is_inactive', 'parent_id', 'user_id', 'created_by', 'updated_by'
    ];

    public function group()
    {
        return $this->belongsTo(GLAccountGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(GLAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GLAccount::class, 'parent_id')->orderBy('code');
    }

    /**
     * Scope for filtering by type (Asset, Liability, etc.)
     */
    /**
     * Ensure account type is always singular before saving to match database ENUM.
     */
    public function setTypeAttribute($value)
    {
        $map = [
            'assets' => 'asset',
            'liabilities' => 'liability',
            'equities' => 'equity',
            'incomes' => 'income',
            'expenses' => 'expense'
        ];
        
        $cleanValue = strtolower(trim($value));
        $this->attributes['type'] = $map[$cleanValue] ?? $cleanValue;
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
