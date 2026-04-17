<?php

namespace App\Models;

use App\Models\AlumniProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'batch_year'
    ];

    public function alumniProfiles(): HasMany
    {
        return $this->hasMany(AlumniProfile::class, "batch_id", "id");
    }
}
