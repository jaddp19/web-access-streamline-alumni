<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'batch_name',
    ];

    protected $casts = [
        'batch_name' => 'integer',
    ];

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'batch_id', 'id');
    }
}
