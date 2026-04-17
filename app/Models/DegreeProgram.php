<?php

namespace App\Models;

use App\Models\EducationalBackground;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DegreeProgram extends Model
{
    protected $fillable = [
        'program_name',
        'department',
        'level'
    ];

    public function educationalBackgrounds(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, "degree_program_id", "id");
    }
}
