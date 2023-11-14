<?php

namespace App\MoonShine\Resources;

use App\MoonShine\CustomFields\ImageCustom;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;

use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class ImageResource extends Resource
{
	public static string $model = Image::class;

	public static string $title = 'Images';

	public function fields(): array
	{
		return [
		    ID::make(),
            ImageCustom::make('image', 'image')->dir('products'),
            Number::make('order', 'order')
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
}
