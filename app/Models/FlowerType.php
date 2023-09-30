<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function flowers()
    {
        return $this->hasMany(Flower::class);
    }
}
