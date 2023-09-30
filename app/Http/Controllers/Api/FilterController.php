<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlowerType;
use App\Models\Product;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFiltersList(Request $request)
    {
        $query = Product::where('is_active', 1);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        $minPrice = $query->min('price');
        $maxPrice = $query->max('price');

        $flowerTypes = FlowerType::whereHas('flowers.products', function ($query) use ($request) {
            if ($request->has('category_id')) {
                $query->where('products.is_active', 1)->where('products.category_id', $request->get('category_id'));
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
