<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'is_anonymous_sender' => $this->is_anonymous_sender,
            'is_recipient_current_user' => $this->is_recipient_current_user,
            'previously_call_to_recipient' => $this->previously_call_to_recipient,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'delivery_address' => $this->delivery_address,
            'delivery_date_time' => $this->when($this->delivery_date_time !== null, fn() => $this->delivery_date_time->format('d.m.Y') . ' c ' . $this->delivery_date_time->format('H:i') . ' до ' . $this->delivery_date_time->addMinutes(30)->format('H:i')),
            'order_price' => $this->order_price,
            'delivery_price' => $this->delivery_price,
            'total_price' => $this->total_price,
            'status' => $this->status->description(),
            'products' => ProductOrderResource::collection($this->products),
            'created_at' => $this->created_at->format('d.m.Y'),
        ];
    }
}
