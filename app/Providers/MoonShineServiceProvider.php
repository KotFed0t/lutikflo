<?php

namespace App\Providers;

use App\Models\DeliveryPriceSetting;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\DeliveryPriceSettingResource;
use App\MoonShine\Resources\FlowerResource;
use App\MoonShine\Resources\FlowerTypeResource;
use App\MoonShine\Resources\OrderResource;
use App\MoonShine\Resources\PackageResource;
use App\MoonShine\Resources\ProductResource;
use App\MoonShine\Resources\SettingTestResource;
use App\MoonShine\Resources\TransactionResource;
use App\MoonShine\Resources\UserResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\MoonShine;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Resources\CustomPage;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app(MoonShine::class)->menu([
//            MenuGroup::make('moonshine::ui.resource.system', [
//                MenuItem::make('moonshine::ui.resource.admins_title', new MoonShineUserResource())
//                    ->translatable()
//                    ->icon('users'),
//                MenuItem::make('moonshine::ui.resource.role_title', new MoonShineUserRoleResource())
//                    ->translatable()
//                    ->icon('bookmark'),
//            ])->canSee(fn() => auth()->guard('moonshine')->user()->moonshine_user_role_id === 1)->translatable(),

            MenuItem::make('Категории', new CategoryResource())->icon('heroicons.outline.queue-list'),
            MenuItem::make('Товары', new ProductResource())->icon('heroicons.outline.building-storefront'),


            MenuItem::make('Заказы', new OrderResource())->icon('heroicons.shopping-bag'),

            MenuGroup::make('Цветы', [
                MenuItem::make('Упаковки', new PackageResource())->icon('heroicons.gift'),
                MenuItem::make('Типы цветов', new FlowerTypeResource())->icon('heroicons.sparkles'),
                MenuItem::make('Цветы', new FlowerResource())->icon('heroicons.fire'),
            ])->icon('heroicons.list-bullet'),
            MenuItem::make('Транзакции', new TransactionResource())
                ->canSee(fn() => auth()->guard('moonshine')->user()->moonshine_user_role_id === 1)
                ->icon('heroicons.credit-card'),
            MenuItem::make('Клиенты', new UserResource())->icon('heroicons.outline.users'),
            MenuItem::make('Настройки доставки', new DeliveryPriceSettingResource())
                ->canSee(fn() => auth()->guard('moonshine')->user()->moonshine_user_role_id === 1)
                ->icon('heroicons.truck'),

            MenuItem::make('moonshine::ui.resource.admins_title', new MoonShineUserResource())
                ->translatable()
                ->icon('users')
                ->canSee(fn() => auth()->guard('moonshine')->user()->moonshine_user_role_id === 1),
//            CustomPage::make('Настройки доставки', 'delivery_settings','moonshine.pages.deliverySettings', function () {
//                return ['delivery_price_settings' => DeliveryPriceSetting::first()];
//            })
        ]);
    }
}
