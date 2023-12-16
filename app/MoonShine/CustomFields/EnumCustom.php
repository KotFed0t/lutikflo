<?php

namespace App\MoonShine\CustomFields;

use App\Enums\OrderStatusesEnum;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Enum;
use UnitEnum;

class EnumCustom extends Enum
{
    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = $item->{$this->field()};
        $enumValuesWithDescription = OrderStatusesEnum::valuesWithDescription();
        if (isset($enumValuesWithDescription[$value?->value])) {
            return (string) ($enumValuesWithDescription[$value->value] ?? '');
        }
        return '-';
    }

    public function attach(string $class): static
    {
        /* @var UnitEnum $class ; */
//        dd($class::valuesWithDescriptionForAdmin());
        $this->options($class::valuesWithDescriptionForAdmin());

        return $this;
    }
}
