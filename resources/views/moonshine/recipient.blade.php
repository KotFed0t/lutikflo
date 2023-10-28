@if($order->is_recipient_current_user)
    сам заказчик
@else
    <x-moonshine::table>
        <x-slot:thead class="text-center">
            <th>телефон получателя</th>
            <th>имя получателя</th>
            <th>предварительно позвонить получателю?</th>
        </x-slot:thead>
        <x-slot:tbody class="text-center">
            <tr>
                <th>{{ $order->recipient_phone }}</th>
                <th>{{ $order->recipient_name }}</th>
                <th>{{ $order->previously_call_to_recipient == false? 'нет' : 'да' }}</th>
            </tr>
        </x-slot:tbody>
        <x-slot:tfoot class="text-center">
        </x-slot:tfoot>
    </x-moonshine::table>
@endif

