<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * This model maps to the `email_templates` table since the class
     * name doesn't match Laravel's default pluralization convention.
     */
    protected $table = 'email_templates';

    protected $casts = [
        'template' => 'array',
    ];

    protected $fillable = [
        'template',
    ];
}
