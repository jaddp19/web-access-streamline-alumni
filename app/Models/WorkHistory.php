<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHistory extends Model
{
    protected $fillable = [
        'user_id',
        'work_name',
        'company_id',
        'date_hired',
        'is_current_job',
        'is_current_employed',
    ];

    protected $casts = [
        'date_hired' => 'date',
        'is_current_job' => 'boolean',
        'is_current_employed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
