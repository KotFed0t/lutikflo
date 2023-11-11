<?php

namespace App\Http\Controllers\Moonshine;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class ImageController extends Controller
{
    public function test(Request $request)
    {
        $product = Product::query()->findOrFail($request->get('product_id'));
        $image = $request->file('image');
        $width = $request->get('width');
        $height = $request->get('height');
        $x = $request->get('x');
        $y = $request->get('y');
        $img = Image::make($image->getRealPath())->crop($width, $height, $x, $y)->resize(1080, 1080)->encode('jpg',80);
        $img_name = 'products/' . uniqid() . '.jpg';
        Storage::disk('public')->put( $img_name, $img);
        Storage::disk('public')->delete( $product->main_img);
        $product->main_img = $img_name;
        $product->save();
//        $request->file('image')->store('avatars');


        return response()->json('ok from server');
    }
}
