<?php

namespace App\Models;

use App\Models\AlumniProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentHistory extends Model
{
    protected $fillable = [
        'alumni_profile_id',
        'company_name',
        'job_title',
        'employment_sector',
        'start_date',
        'end_date',
        'is_current'
    ];

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, "alumni_profile_id", "id");
    }
}
