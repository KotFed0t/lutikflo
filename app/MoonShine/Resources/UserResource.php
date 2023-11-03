<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class UserResource extends Resource
{
	public static string $model = User::class;

	public static string $title = 'Клиенты';

    public static bool $withPolicy = true;

    protected bool $editInModal = true;

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('имя', 'name'),
            Phone::make('телефон', 'phone'),
            Email::make('email', 'email'),
            Date::make('дата регистрации', 'created_at')->disabled(),
            HasMany::make('Заказы', 'orders')->hideOnIndex()->hideOnForm()
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
