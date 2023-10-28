<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

class DeliveryPriceSetting extends Model
{
    use HasFactory;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'fix_price_distance_km',
        'fix_price',
        'price_per_one_km'
    ];

    public function getPriceByDistance($distance)
    {
        if ($distance <= $this->fix_price_distance_km) {
            return $this->fix_price;
        }
        return ($distance - $this->fix_price_distance_km) * $this->price_per_one_km + $this->fix_price;
    }
}
