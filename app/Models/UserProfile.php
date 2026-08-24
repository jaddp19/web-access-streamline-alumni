<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'location',
        'batch_id',
        'is_private',
        'board_taken',
        'board_rate',
        'is_verified',
    ];

    protected $casts = [
        'location' => 'array',
        'is_private' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_course', 'user_profile_id', 'course_id');
    }
}
