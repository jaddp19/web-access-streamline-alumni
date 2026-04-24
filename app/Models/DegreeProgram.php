<?php

namespace App\Models;

use App\Models\Department;
use App\Models\EducationalBackground;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DegreeProgram extends Model
{
    protected $fillable = [
        'program_name',
        'department_id',
    ];

    public function educationalBackgrounds(): HasMany
    {
        return $this->hasMany(EducationalBackground::class, "degree_program_id", "id");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id", "id");
    }
}
