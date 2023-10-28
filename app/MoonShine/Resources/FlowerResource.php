<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Flower;

use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class FlowerResource extends Resource
{
	public static string $model = Flower::class;

	public static string $title = 'Цветы';

    public static bool $withPolicy = true;

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('название', 'name'),
            Number::make('цена', 'price'),
            BelongsTo::make('тип цветка', 'type', 'name')
        ];
	}

	public function rules(Model $item): array
	{
	    return [];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [
            FiltersAction::make(trans('moonshine::ui.filters')),
        ];
    }

    public function components(): array
    {
        return [
            ChangeLogFormComponent::make('История изменений')
                ->canSee(fn() => auth()->user()->moonshine_user_role_id === MoonshineUserRole::DEFAULT_ROLE_ID),
        ];
    }
}
