<?php

namespace App\Models;

use App\Models\EducationalBackground;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'batch_year'
    ];

    public function educationBackgrounds(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, "batch_id", "id");
    }
}
