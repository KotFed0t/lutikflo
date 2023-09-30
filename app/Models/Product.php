<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Product extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'price',
        'is_active',
        'description',
        'main_img',
        'order'
    ];

    public function flowers(): BelongsToMany
    {
        return $this->belongsToMany(Flower::class)->withPivot('count');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public static function filter(Request $request)
    {
        $query = Product::where('is_active', 1);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->has('price_from')) {
            $query->where('products.price', '>=', $request->get('price_from'));
        }

        if ($request->has('price_to')) {
            $query->where('products.price', '<=', $request->get('price_to'));
        }

        if ($request->has('flower_types_id')) {
            $query->whereHas('flowers.type', function ($query) use ($request) {
                $query->whereIn('flower_types.id', $request->get('flower_types_id'));
            });
        }

        $query->orderBy('products.order', 'DESC');

        return $query->get();
    }
}
