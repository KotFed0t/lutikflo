<?php

namespace App\Models;

use App\Enums\OrderStatusesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MoonShine\Traits\Models\HasMoonShineChangeLog;

class Order extends Model
{
    use HasFactory;
    use HasMoonShineChangeLog;

    protected $fillable = [
        'user_id',
        'is_anonymous_sender',
        'is_recipient_current_user',
        'previously_call_to_recipient',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'entrance',
        'floor',
        'apartment_number',
        'comment_for_courier',
        'delivery_date_time',
        'client_wishes',
        'order_price',
        'delivery_price',
        'total_price',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatusesEnum::class,
        'delivery_date_time' => 'datetime'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'product_count',
            'flower_count',
            'price_by_one_product',
            'price_by_count_product'
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}


