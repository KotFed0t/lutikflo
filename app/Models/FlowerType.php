<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

class FlowerType extends Model
{
    use HasFactory;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'name'
    ];

    public function flowers()
    {
        return $this->hasMany(Flower::class);
    }
}
