<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use MoonShine\Traits\Models\HasMoonShineChangeLog;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Product extends Model
{
    use HasFactory;
    use HasEagerLimit;
    use HasMoonShineChangeLog;

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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function flowers(): BelongsToMany
    {
        return $this->belongsToMany(Flower::class)->withPivot(['count', 'is_changeable_flower_count']);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isChangeableFlowerCount(): bool
    {
        $flowers = $this->flowers;
        if ($flowers->count() === 1 && $flowers->first()->pivot->is_changeable_flower_count) {
            return true;
        }
        return false;
    }

    public function getChangeableFlower() : Flower|null
    {
        if ($this->isChangeableFlowerCount())
        {
            return $this->flowers->first();
        }
        return null;
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class)->withPivot('count');
    }

    public static function filter(Request $request)
    {
        $query = Product::with(['flowers'])->where('is_active', 1);

        //TODO стоит ли загрузить relation category и через него отфильтровать?
        if ($request->has('category_slug')) {
            $category = Category::query()
                ->where('slug', $request->get('category_slug'))
                ->where('is_active', 1)
                ->firstOrFail();
            if ($category) {
                $query->where('category_id', $category->id);
            }
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

        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if ($sort === 'price-desc') {
                $query->orderBy('products.price', 'DESC');
            } elseif ($sort === 'price-asc') {
                $query->orderBy('products.price', 'ASC');
            } elseif ($sort === 'novelty') {
                $query->orderBy('products.created_at', 'DESC');
            } else {
                $query->orderBy('products.order', 'DESC');
            }
        } else {
            $query->orderBy('products.order', 'DESC');
        }

        return $query->paginate(28);
    }
}
