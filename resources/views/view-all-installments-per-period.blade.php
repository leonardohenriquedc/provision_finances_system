<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Parcelas do Mês
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Header info -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">


        <div>
                <h3 class="text-lg font-semibold text-gray-700">
                    Total: {{ $total }}
                </h3>
            </div>  

        <form method="GET" class="flex flex-col sm:flex-row items-center gap-2">
        <div class="flex items-center gap-3">

            <div class="flex items-center gap-3">
                <label for="year" class="text-sm font-medium text-gray-700">
                    Ano selecionado
                </label>
                <input type="number" name="year" id="year" class="block w-48 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-center font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <label for="month" class="text-sm font-medium text-gray-700">
                Mês selecionado
            </label>

            <select
                name="month"
                id="month"
                onchange="this.form.submit()"
                class="block w-48 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-center font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="todos"  {{ $month == "todos" ? 'selected' : '' }}>Todos</option>
                <option value="1"  {{ $month == 1 ? 'selected' : '' }}>Janeiro</option>
                <option value="2"  {{ $month == 2 ? 'selected' : '' }}>Fevereiro</option>
                <option value="3"  {{ $month == 3 ? 'selected' : '' }}>Março</option>
                <option value="4"  {{ $month == 4 ? 'selected' : '' }}>Abril</option>
                <option value="5"  {{ $month == 5 ? 'selected' : '' }}>Maio</option>
                <option value="6"  {{ $month == 6 ? 'selected' : '' }}>Junho</option>
                <option value="7"  {{ $month == 7 ? 'selected' : '' }}>Julho</option>
                <option value="8"  {{ $month == 8 ? 'selected' : '' }}>Agosto</option>
                <option value="9"  {{ $month == 9 ? 'selected' : '' }}>Setembro</option>
                <option value="10" {{ $month == 10 ? 'selected' : '' }}>Outubro</option>
                <option value="11" {{ $month == 11 ? 'selected' : '' }}>Novembro</option>
                <option value="12" {{ $month == 12 ? 'selected' : '' }}>Dezembro</option>
            </select>
        </div>    

    
            <select 
                name="status"
                onchange="this.form.submit()"
                class="block w-48 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-center font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="">Todos</option>
                <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>
                    Em aberto
                </option>
                <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>
                    Pago
                </option>
                <option value="LATE" {{ request('status') == 'LATE' ? 'selected' : '' }}>
                    Atrasado
                </option>
            </select>

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            >
                Filtrar
            </button>
        </form>

        <a 
            href="{{ route('dashboard') }}"
            class="text-gray-600 hover:underline text-sm"
        >
            ← Voltar
        </a>
    </div>

            <!-- Tabela -->
            <div class="bg-white shadow-md rounded-xl p-6">

                <h3 class="text-xl font-semibold text-gray-700 mb-6">
                    Lista de Parcelas
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">

                        <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Descrição</th>
                                <th class="px-6 py-3 text-center">Parcela</th>
                                <th class="px-6 py-3 text-right">Valor</th>
                                <th class="px-6 py-3 text-center">Vencimento</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Ação</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @forelse ($installments as $installment)

                                <tr class="border-t hover:bg-gray-50 transition">

                                    <!-- Descrição -->
                                    <td class="px-6 py-3 font-medium">
                                        {{ $installment->provision->description }}
                                    </td>

                                    <!-- Nº parcela -->
                                    <td class="px-6 py-3 text-center">
                                        {{ $installment->installment_number }}
                                    </td>

                                    <!-- Valor -->
                                    <td class="px-6 py-3 text-right text-blue-600 font-semibold">
                                        R$ {{ number_format($installment->amount, 2, ',', '.') }}
                                    </td>

                                    <!-- Data -->
                                    <td class="px-6 py-3 text-center">
                                        {{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-3 text-center">
                                        @if($installment->status === 'PAID')
                                            <span class="text-green-600 font-semibold">Pago</span>
                                        @elseif($installment->status === 'LATE')
                                            <span class="text-red-600 font-semibold">Atrasado</span>
                                        @else
                                            <span class="text-yellow-600 font-semibold">Em aberto</span>
                                        @endif
                                    </td>

                                    <!-- Ação -->
                                    <td class="px-6 py-3 text-center">
                                        <a href={{ route('installment', $installment->id) }}
                                           class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs">
                                            Atualizar Status
                                        </a>
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-400">
                                        Nenhuma parcela encontrada para este mês
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>