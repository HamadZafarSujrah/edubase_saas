<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class SmsTemplate extends Model
{
    use HasTenant;

    protected $fillable = ['tenant_id', 'name', 'category', 'body'];
}
