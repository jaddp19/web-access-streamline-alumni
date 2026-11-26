<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormHistory extends Model
{
    protected $fillable = [
        'user_id',
        'approved_by',
        'version',
        'approved_at',
        'snapshot',
        'snapshot_hash',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'snapshot' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function label(): string
    {
        return 'Form #'.$this->version;
    }
}
