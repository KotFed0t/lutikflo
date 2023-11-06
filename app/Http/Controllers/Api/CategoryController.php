<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryWithProductsResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::where('is_active', 1)->orderBy('order', 'DESC')->get()
        );
    }

    public function getCategoriesWithProducts(Request $request)
    {
        $productsLimit = $request->has('products_limit') ? $request->get('products_limit') : 4;
        return CategoryWithProductsResource::collection(
            Category::with(['products' => function ($query) use ($productsLimit) {
                $query->where('is_active', 1)->orderBy('order', 'DESC')->limit($productsLimit);
            }, 'products.flowers'])->where('is_active', 1)->orderBy('order', 'DESC')->get()
        );
    }
}
