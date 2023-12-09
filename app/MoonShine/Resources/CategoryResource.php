<?php

namespace App\MoonShine\Resources;

use App\Models\Product;
use Closure;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

use MoonShine\Fields\HasMany;
use MoonShine\Fields\Number;
use MoonShine\Fields\Slug;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\MoonShine;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class CategoryResource extends Resource
{
	public static string $model = Category::class;

	public static string $title = 'Категории';

    public static bool $withPolicy = true;

    public static array $with = ['products'];

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('Имя', 'name'),
            Slug::make('slug', 'slug'),
            SwitchBoolean::make('Активна', 'is_active')->autoUpdate(false),
            Number::make('Порядок вывода', 'order'),
            HasMany::make('Товары', 'products')
                ->resourceMode()
                ->hideOnIndex()
        ];
	}

	public function rules(Model $item): array
	{
        $countProducts = Product::query()->where('category_id', $item->id)->where('is_active', true)->count();
	    return [
            'is_active' => function (string $attribute, mixed $value, Closure $fail) use ($countProducts) {
                if ($value == false && $countProducts !== 0)
                    $fail('к данной категории привязаны активные товары, скройте сначала их');
            }
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
