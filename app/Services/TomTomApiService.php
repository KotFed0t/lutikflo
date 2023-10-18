<?php

namespace App\Services;

use App\Models\DeliveryPriceSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TomTomApiService
{
    private const SHOP_LATITUDE = 55.059198;

    private const SHOP_LONGITUDE = 82.938917;

    private const TOMTOM_URL = 'https://api.tomtom.com/routing/1/calculateRoute/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tomtom.apiKey');
    }

    public function getRouteDistanceKm($destination_lat, $destination_long): ?float
    {
        $url = self::TOMTOM_URL . self::SHOP_LATITUDE . ',' . self::SHOP_LONGITUDE . ':' . $destination_lat . ',' . $destination_long . '/json';
        $response = Http::get($url, ['key' => $this->apiKey]);
        if ($response->successful()) {
            $meters = $response['routes'][0]['summary']['lengthInMeters'];
            return round($meters / 1000);
        }
        Log::error('error while calling TomTom.com', ['requested_url' => $url, 'response' => $response->json()]);
        return null;
    }

    public function getDeliveryPrice($destination_lat, $destination_long): ?int
    {
        $distance = $this->getRouteDistanceKm($destination_lat, $destination_long);
        if ($distance !== null) {
            $deliveryPriceSettings = DeliveryPriceSetting::first();
            if ($distance <= $deliveryPriceSettings->fix_price_distance_km) {
                return $deliveryPriceSettings->fix_price;
            }
            return ($distance - $deliveryPriceSettings->fix_price_distance_km)
                * $deliveryPriceSettings->price_per_one_km
                + $deliveryPriceSettings->fix_price;
        }
        return null;
    }
}
