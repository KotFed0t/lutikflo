<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\FlowerType;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return ProductResource::collection(Product::filter($request));
    }

    public function getProduct(Product $product)
    {
        //если is_active = false на фронте все равно открыть продукт, но написать что он пока не в наличии
        return new ProductDetailResource($product);
    }
}
