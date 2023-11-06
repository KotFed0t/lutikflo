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
            'is_active' => $this->is_active,
            'is_changeable_flower_count' => $this->isChangeableFlowerCount(),
            'changeable_flower' => $this->when($this->isChangeableFlowerCount(), fn() => new FlowerResource($this->getChangeableFlower())),
            'description' => $this->description,
            'main_img' => $this->main_img,
            'images' => ImageResource::collection($this->images()->orderBy('order', 'DESC')->get()),
            'flowers' => FlowerResource::collection($this->flowers),
            'packages' => PackageResource::collection($this->packages)
        ];
    }
}
