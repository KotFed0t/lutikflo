<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flower extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'flower_type_id'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class, 'flower_type_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
