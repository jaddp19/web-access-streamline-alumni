<?php

namespace App\Models;

use App\Models\EducationalBackground;
use App\Models\EmploymentHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AlumniProfile extends Model
{
    protected $fillable = [
        'user_id',
        'gender',
        'phone_number_1',
        'phone_number_2',
        'permanent_address',
        'current_address'
    ];

    public function educationalBackground(): HasOne
    {
        return $this->hasOne(EducationalBackground::class, "alumni_profile_id", "id");
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class, "alumni_profile_id", "id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
