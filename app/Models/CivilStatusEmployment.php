<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CivilStatusEmployment extends Model
{
    protected $fillable = [
        'tracer_study_id',
        'civil_status',
        'employed_related_to_degree',
        'employment_type',
        'organization_type',
        'employment_area',
        'abroad_country',
        'months_to_first_job',
    ];

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }
}
