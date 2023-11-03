<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlowerType;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFiltersList(Request $request): JsonResponse
    {
        $query = Product::where('is_active', 1);
        $categoryId = null;

        //TODO стоит ли загрузить relation category и через него отфильтровать?
        if ($request->has('category_slug')) {
            $category = Category::query()
                ->where('slug', $request->get('category_slug'))
                ->where('is_active', 1)
                ->firstOrFail();
            if ($category) {
                $categoryId = $category->id;
                $query->where('category_id', $categoryId);
            }
        }

        $minPrice = $query->min('price');
        $maxPrice = $query->max('price');

        $flowerTypes = FlowerType::where('show_in_filters', 1)->whereHas('flowers.products', function ($query) use ($categoryId) {
            if ($categoryId !== null) {
                $query->where('products.is_active', 1)->where('products.category_id', $categoryId);
            } else {
                $query->where('products.is_active', 1);
            }
        })->select('id', 'name')->get();

        $result = [
            'flowerTypes' => $flowerTypes,
            'price' => [
                'min' => $minPrice,
                'max' => $maxPrice
            ]
        ];

        return response()->json($result);
    }
}
