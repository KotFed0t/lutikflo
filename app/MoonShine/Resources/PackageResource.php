<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Package;

use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class PackageResource extends Resource
{
	public static string $model = Package::class;

	public static string $title = 'Упаковки';

    public static bool $withPolicy = true;

    protected bool $showInModal = true;

    protected bool $editInModal = true;

    protected bool $createInModal = true;

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('название', 'name')->required(),
            Number::make('цена', 'price')->required(),
        ];
	}

	public function rules(Model $item): array
	{
	    return [
            'name' => 'required',
            'price' => 'required'
        ];
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
