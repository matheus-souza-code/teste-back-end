<div>
    <div class="overflow-x-auto">
        <table class="w-full bg-white border border-gray-200 rounded-lg">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-center text-gray-600 font-semibold uppercase">
                            {{ $column }}
                        </th>
                    @endforeach
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-center text-gray-600 font-semibold uppercase">
                        Edit
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        @foreach ($columns as $column)
                            <td class="py-2 px-4 border-b border-gray-200 text-center">{{ $item->$column }}</td>
                        @endforeach
                        <td class="py-2 px-4 border-b border-gray-200">
                            <div class="flex justify-center gap-4">
                                <button class="text-green-600 hover:text-blue-900">Editar</button>
                                <button class="text-red-600 hover:text-red-900">Excluir</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>