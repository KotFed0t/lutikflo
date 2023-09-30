<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//просто вывод категорий для меню
Route::get('categories', [CategoryController::class, 'index']);

// это для главной вывод категорий вместе с товарами. Можно указать параметр (?product_limit=10)
Route::get('categories/products', [CategoryController::class, 'getCategoriesWithProducts']);

// в get параметрах принимает category_id, и фильтры
Route::get('products', [ProductController::class, 'index']);

//товар детально
Route::get('products/{product}', [ProductController::class, 'getProduct']);

//выдает список фильтров. На вход может принимать параметр category_id
Route::get('filters', [FilterController::class, 'getFiltersList']);
