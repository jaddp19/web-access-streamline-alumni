<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CivilStatusEmployment extends Model
{
    use HasFactory;

    public const EMPLOYMENT_STATUSES = [
        'employed',
        'unemployed',
        'self-employed',
        'other',
        'prefer-not-to-say',
    ];

    public const EMPLOYMENT_TYPES = [
        'full-time',
        'part-time',
        'contractual-project-based',
        'freelance',
        'other',
        'prefer-not-to-say',
    ];

    public const ORGANIZATION_TYPES = [
        'private-company',
        'government-agency',
        'non-government-organization',
        'educational-institution',
        'self-employed-business',
        'other',
        'prefer-not-to-say',
    ];

    protected $fillable = [
        'tracer_study_id',
        'civil_status',
        'employment_status',
        'employment_status_other',
        'current_job_position',
        'employed_related_to_degree',
        'employment_type',
        'employment_type_other',
        'organization_type',
        'organization_type_other',
        'employment_area',
        'abroad_country',
        'months_to_first_job',
    ];

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }
}