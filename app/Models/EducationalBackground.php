<?php

namespace App\Models;

use App\Models\AlumniProfile;
use App\Models\Batch;
use App\Models\DegreeProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationalBackground extends Model
{
    protected $fillable = [
        'alumni_profile_id',
        'degree_program_id',
        'batch_id',
        'graduation_year'
    ];

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, "alumni_profile_id", "id");
    }

    public function degreeProgram(): BelongsTo
    {
        return $this->belongsTo(DegreeProgram::class, "degree_program_id", "id");
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, "batch_id", "id");
    }
}
