<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'cat_name',
        'cat_slug',
        'cat_desc',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }
}
