<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'main_img' => $this->main_img,
            'flower_count' => $this->pivot->flower_count,
            'price_by_count_product' => $this->pivot->price_by_count_product,
            'price_by_one_product' => $this->pivot->price_by_one_product,
            'product_count' => $this->pivot->product_count
        ];
    }
}
