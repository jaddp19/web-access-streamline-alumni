<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TracerStudy extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function civilStatusEmployment(): HasOne
    {
        return $this->hasOne(CivilStatusEmployment::class);
    }

    public function furtherStudy(): HasOne
    {
        return $this->hasOne(FurtherStudy::class);
    }
}
