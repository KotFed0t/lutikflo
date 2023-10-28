<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

class Flower extends Model
{
    use HasFactory;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'name',
        'price',
        'flower_type_id'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class, 'flower_type_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
