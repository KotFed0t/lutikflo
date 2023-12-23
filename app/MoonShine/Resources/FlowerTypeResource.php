<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\FlowerType;

use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class FlowerTypeResource extends Resource
{
	public static string $model = FlowerType::class;

	public static string $title = 'Типы цветов';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $showInModal = true;
    public static bool $withPolicy = true;

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('название', 'name')->required(),
            SwitchBoolean::make('показывать в фильтрах', 'show_in_filters')->autoUpdate(false),
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
