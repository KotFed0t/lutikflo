<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryWithProductsResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::whereHas('products', function ($query) {
                $query->where('is_active', true);
            })->where('is_active', true)->orderBy('order', 'DESC')->get()
        );
    }

    public function getCategoriesWithProducts(Request $request)
    {
        $productsLimit = $request->has('products_limit') ? $request->get('products_limit') : 4;
        return CategoryWithProductsResource::collection(
            Category::with(['products' => function ($query) use ($productsLimit) {
                $query->where('is_active', true)->orderBy('order', 'DESC')->limit($productsLimit);
            }, 'products.flowers'])->whereHas('products', function ($query) {
                $query->where('is_active', true);
            })->where('is_active', true)->orderBy('order', 'DESC')->get()
        );
    }

    public function getCategory(Category $category): CategoryResource|JsonResponse
    {
        if ($category->is_active) {
            return new CategoryResource($category);
        }
        return response()->json(['error' => 'Not Found'], 404);
    }
}
