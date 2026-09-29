<?php

namespace App\Models;

use App\Models\WorkHistory;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, LogsActivity;
    protected array $auditExclude = ['id', 'company_logo'];
    protected $fillable = [
        'company_name',
        'company_logo',
        'company_address',
        'company_desc',
    ];

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class, 'company_id', 'id');
    }
}
