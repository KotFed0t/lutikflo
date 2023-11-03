<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class TransactionResource extends Resource
{
	public static string $model = Transaction::class;

	public static string $title = 'Транзакции';

    public static bool $withPolicy = true;

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            BelongsTo::make('заказ', 'order'),
            Text::make('статус', 'status'),
            Text::make('payment_method', 'payment_method'),
            Text::make('payment_id', 'payment_id'),
            Text::make('rrn', 'rrn'),
            Date::make('created_at', 'created_at'),
            Date::make('updated_at', 'updated_at'),

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
