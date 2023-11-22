<?php

namespace App\MoonShine\Resources;

use App\Enums\OrderStatusesEnum;
use App\Events\OrderStatusUpdated;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Date;
use MoonShine\Fields\Enum;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Image;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Number;
use MoonShine\Fields\Phone;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Filters\BelongsToFilter;
use MoonShine\Filters\DateFilter;
use MoonShine\Filters\SelectFilter;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Resources\Resource;
use MoonShine\Fields\ID;
use MoonShine\Actions\FiltersAction;

class OrderResource extends Resource
{
    public static string $model = Order::class;

    public static string $title = 'Заказы';

    public static bool $withPolicy = true;

    public static array $with = [
        'user',
        'products'
    ];
    protected bool $editInModal = true;

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            NoInput::make('Заказчик', '', fn(Order $order) => view('moonshine.user', compact('order'))->render())
                ->hideOnIndex()
                ->hideOnForm(),
            NoInput::make('Получатель', '', fn(Order $order) => view('moonshine.recipient', compact('order'))->render())
                ->hideOnForm()
                ->hideOnIndex(),
            Text::make('доставить анонимно?', 'is_anonymous_sender', function ($order) {
                return $order->is_anonymous_sender ? 'да' : 'нет';
            })->hideOnIndex()->hideOnForm(),
            BelongsTo::make('Заказчик', 'user_id', 'phone')->hideOnDetail()->hideOnForm(),
//            SwitchBoolean::make('получатель - текущий клиент?', 'is_recipient_current_user')->hideOnForm(),
//            SwitchBoolean::make('previously_call_to_recipient', 'previously_call_to_recipient')->hideOnForm(),
//            Text::make('recipient_name', 'recipient_name')->hideOnForm(),
//            Phone::make('recipient_phone', 'recipient_phone')->hideOnForm(),
            Text::make('адрес доставки', 'delivery_address', fn($order) => $order->delivery_address ?? '-')->hideOnForm(),
            Text::make('подъезд', 'entrance', fn($order) => $order->entrance ?? '-')->hideOnForm(),
            Text::make('этаж', 'floor', fn($order) => $order->floor ?? '-')->hideOnForm(),
            Text::make('квартира', 'apartment_number', fn($order) => $order->apartment_number ?? '-')->hideOnForm(),
            Text::make('комментарий для курьера', 'comment_for_courier', fn($order) => $order->comment_for_courier ?? '-')
                ->hideOnIndex()
                ->hideOnForm(),
            Date::make('дата доставки', 'delivery_date_time')
                ->sortable()
                ->hideOnForm(),
            Text::make('пожелания клиента', 'client_wishes', fn($order) => $order->client_wishes ?? '-')
                ->hideOnIndex()
                ->hideOnForm(),
            HasMany::make('Транзакции по заказу', 'transactions')->hideOnIndex()->hideOnForm(),
            NoInput::make('статус оплаты', '', function (Order $order) {
                $transaction = $order->transactions->last();
                return match ($transaction->status) {
                    'PENDING' => 'Ожидает оплаты',
                    'SUCCEEDED' => 'Оплачено',
                    'CANCELED' => 'Отменено',
                };
            })->badge('purple')->hideOnForm(),
            Enum::make('статус заказа', 'status')->attach(OrderStatusesEnum::class)
                ->sortable()
                ->hint("CREATED - создан; PAID - оплачен; IN_PROCESSING - в обработке; IN_DELIVERY - передан в доставку; DELIVERED - доставлен; CANCELED - Платеж отменен; REFUND - Произведен возврат денежных средств клиенту"),
            Date::make('заказ создан', 'created_at')->hideOnForm()->sortable(),
            NoInput::make('Состав заказа', '', fn(Order $order) => view('moonshine.order', compact('order'))->render())
                ->hideOnIndex()
                ->hideOnForm()


        ];
    }

//    public function query(): Builder
//    {
        //все заказы кроме статуса created
//        return parent::query()->where('status', '>', 1);
//    }

    protected function afterUpdated(Model $item): void
    {
        event(new OrderStatusUpdated($item));
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
        return [
            DateFilter::make('Дата создания заказа', 'created_at'),
        ];
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
