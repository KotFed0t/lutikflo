<?php

use App\Http\Controllers\Moonshine\DeliveryController;
use App\Http\Controllers\Moonshine\ImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('crop-and-store-image', [ImageController::class, 'test'])->middleware('auth.moonshine')->name('crop-and-store-image');

Route::get('{page}', function () {
    return view('app');
})->where('page', '^(?!admin).*');
