<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FurtherStudy extends Model
{
    protected $fillable = [
        'tracer_study_id',
        'level_of_study',
        'is_pursued_further_studies',
    ];

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }

    protected $casts = [
    'is_pursued_further_studies' => 'boolean',
    ];
}
