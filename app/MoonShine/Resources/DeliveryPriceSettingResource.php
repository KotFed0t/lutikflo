<?php

namespace App\MoonShine\Resources;

use App\Models\DeliveryPriceSetting;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Number;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\SingletonResource;
use MoonShine\Fields\ID;

class DeliveryPriceSettingResource extends SingletonResource
{
	public static string $model = DeliveryPriceSetting::class;

	public static string $title = 'Настройки доставки';

    public static bool $withPolicy = true;

    public function getId(): int|string
    {
        return 1;
    }

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Number::make('дистанция доставки (в км) с фиксированной ценой', 'fix_price_distance_km')->required(),
            Number::make('фиксированная цена (руб)', 'fix_price')->required(),
            Number::make('цена за каждый км свыше дистанции с фиксированной ценой', 'price_per_one_km')->required(),
        ];
	}

	public function rules(Model $item): array
    {
        return [];
    }

    public function components(): array
    {
        return [
            ChangeLogFormComponent::make('История изменений')
                ->canSee(fn() => auth()->user()->moonshine_user_role_id === MoonshineUserRole::DEFAULT_ROLE_ID),
        ];
    }
}
