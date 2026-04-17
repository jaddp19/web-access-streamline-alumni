<?php

namespace App\Models;

use App\Models\EducationalBackground;
use App\Models\EmploymentHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlumniProfile extends Model
{
    protected $fillable = [
        'user_id',
        'batch_id',
        'gender',
        'phone_number_1',
        'phone_number_2',
        'permanent_address',
        'current_address'
    ];

    public function educationalBackgrounds(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, "alumni_profile_id", "id");
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class, "alumni_profile_id", "id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, "batch_id", "id");
    }
}
