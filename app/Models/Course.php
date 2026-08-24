<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'course_title',
        'course_slug',
        'course_desc',
        'is_active',
        'department_id',
        'course_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function userProfiles(): BelongsToMany
    {
        return $this->belongsToMany(UserProfile::class, 'user_course', 'course_id', 'user_profile_id');
    }
}
