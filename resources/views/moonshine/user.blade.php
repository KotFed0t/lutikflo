<x-moonshine::table>
    <x-slot:thead class="text-center">
        <th>телефон</th>
        <th>имя</th>
        <th>email</th>
    </x-slot:thead>
    <x-slot:tbody class="text-center">
        <tr>
            <th>
                <x-moonshine::link
                    :filled="true"
                    href="{{ config('app_url').'/'.config('moonshine.route.prefix').'/resource/user-resource/'.$order->user->id }}"
                    icon="heroicons.arrow-top-right-on-square"
                >
                    {{ $order->user->phone }}
                </x-moonshine::link>
            </th>
            <th>{{ $order->user->name ?? '-' }}</th>
            <th>{{ $order->user->email }}</th>
        </tr>
    </x-slot:tbody>
    <x-slot:tfoot class="text-center">
    </x-slot:tfoot>
</x-moonshine::table>
