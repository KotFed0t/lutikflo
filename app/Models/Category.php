<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Traits\Models\HasMoonShineChangeLog;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Category extends Model
{
    use HasFactory;
    use HasEagerLimit;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'name',
        'slug',
        'has_flowers',
        'is_active',
        'order'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
