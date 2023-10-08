<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPriceSetting;
use App\Services\TomTomApiService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getDeliveryPrice(Request $request, TomTomApiService $tomTomApiService)
    {
        $distance = $tomTomApiService->getRouteDistanceKm($request->get('latitude'), $request->get('longitude'));
        if ($distance !== null) {
            $deliveryPriceSettings = DeliveryPriceSetting::first();
            $deliveryPrice = $deliveryPriceSettings->getPriceByDistance($distance);
            return response()->json(['price' => $deliveryPrice, 'distance' => $distance]);
        }
        return response()->json(['error_message' => 'Что-то пошло не так...'], 500);
    }
}
