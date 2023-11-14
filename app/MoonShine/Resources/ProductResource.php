<?php

namespace App\MoonShine\Resources;

use App\MoonShine\CustomFields\ImageCustom;
use Closure;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

use Illuminate\Support\Facades\Storage;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Date;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Image;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Number;
use MoonShine\Fields\Slug;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class ProductResource extends Resource
{
    public static string $model = Product::class;

    public static string $title = 'Товары';

    public static bool $withPolicy = true;

    public static array $with = ['flowers', 'category'];

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Block::make('Основная информация о товаре', [
                        ID::make()->sortable(),
                        ImageCustom::make('Главная картинка', 'main_img')->dir('/products'),
                        Text::make('Название', 'name'),
                        Slug::make('slug', 'slug'),
                        BelongsTo::make('Категория', 'category_id', 'name'),
                        Number::make('Цена', 'price'),
                        SwitchBoolean::make('Активен', 'is_active')->autoUpdate(false),
                        TinyMce::make('Описание', 'description')->hideOnIndex(),
                        Number::make('Порядок вывода', 'order'),
                    ])
                ])->columnSpan(6),

                Column::make([
                    Block::make([
                        Collapse::make('состав букета', [
                            BelongsToMany::make('Цветы', 'flowers', function ($flower) {
                                return $flower->name . ' | ' . $flower->price . ' руб';
                            })
                                ->fields([
                                    Text::make('кол-во', 'count'),
                                    Number::make('изменяемый', 'is_changeable_flower_count')->max(1)
                                ])
//                            ->asyncSearch('name')
                                ->hideOnIndex()
                        ])

                    ]),
                    Block::make([
                        Collapse::make('Упаковка', [
                            BelongsToMany::make('Упаковка', 'packages', function ($package) {
                                return $package->name . ' | ' . $package->price . ' руб';
                            })
                                ->fields([
                                    Number::make('количество', 'count')->min(1)
                                ])
                                ->hideOnIndex()
                        ])
                    ])
                ])->columnSpan(3),

                Column::make([
                    Block::make('дополнительные картинки', [
                        HasMany::make('Картинки', 'images', new ImageResource())
                            ->hideOnIndex()
                            ->removable()
                            ->fullPage()
                    ])
                ])->columnSpan(3)
            ]),


        ];
    }

    public function rules(Model $item): array
    {
        $flowers = request()->has('flowers') ? request()->get('flowers') : [];
        return [
            'flowers_is_changeable_flower_count' => ['required', function (string $attribute, mixed $value, Closure $fail) use ($flowers) {
                $count = count(array_filter($value));
                $flowers_count = $flowers !== null ? count($flowers) : 0;
                if ($count > 1) {
                    $fail('Нельзя установить более 1 изменяемого цветка');
                } elseif ($count === 1 && $flowers_count !== 1) {
                    $fail('Чтобы установить более 1 цветка в букете уберите атрибут "изменяемый"');
                }
            },],
            'flowers_count' => [function (string $attribute, mixed $value, Closure $fail) use ($flowers) {
                foreach ($flowers as $flower_id) {
                    if ($value[$flower_id] < 1) {
                        $fail('Установите для каждого выбранного цветка значение в поле "количество". Значение должно быть > 0');
                    }
                }
            }]
        ];
    }

    protected function afterUpdated(Model $item): void
    {
        //delete main_img from storage if it changed
        if (request()->has('hidden_main_img')) {
            if (request()->get('hidden_main_img') !== $item->main_img) {
                Storage::disk(config('moonshine.disk'))->delete(request()->get('hidden_main_img'));
            }
        }
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
