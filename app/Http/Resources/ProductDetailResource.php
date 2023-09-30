<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'description' => $this->description,
            'main_img' => $this->main_img,
            'images' => ImageResource::collection($this->images()->orderBy('order', 'DESC')->get()),
            'monobuquiet' => $this->when($this->flowers()->count() === 1, function () {
                return new FlowerResource($this->flowers->first());
            }),
        ];
    }
}
