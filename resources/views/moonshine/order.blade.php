<x-moonshine::table>
    <x-slot:thead>
        <th>товар</th>
        <th>фото</th>
        <th>количество цветов в букете</th>
        <th>количество товара</th>
        <th>цена</th>
        <th>сумма</th>
    </x-slot:thead>

    <x-slot:tbody>
        @foreach($order->products as $product)
            <tr>
                <td>
                    <x-moonshine::link
                        href="{{ config('app_url').'/'.config('moonshine.route.prefix').'/resource/product-resource/'.$product->id }}"
                        icon="heroicons.arrow-top-right-on-square"
                    >
                        @if($product->pivot->flower_count === null)
                            {{ $product->name }}
                        @else
                            {{ $product->name.' ('.$product->pivot->flower_count.' шт.)' }}
                        @endif
                    </x-moonshine::link>

                </td>
                <td>
                    <x-moonshine::thumbnails value="{{ config('app_url').'/storage/'.$product->main_img }}"/>
                </td>
                <td>{{ $product->pivot->flower_count !== null ? $product->pivot->flower_count : '-' }}</td>
                <td>{{ $product->pivot->product_count }}</td>
                <td>{{ $product->pivot->price_by_one_product }}</td>
                <td>{{ $product->pivot->price_by_count_product }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5" style="text-align:right">Итого за заказ</td>
            <td>{{ $order->order_price }}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:right">Доставка</td>
            <td>{{ $order->delivery_price }}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:right">Итого</td>
            <td>{{ $order->total_price }}</td>
        </tr>
    </x-slot:tbody>
</x-moonshine::table>
