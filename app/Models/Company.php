<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'company_logo',
        'company_address',
        'company_desc',
    ];

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class, 'company_id', 'id');
    }
}
