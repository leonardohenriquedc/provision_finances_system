<x-app-layout class="dark">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-bold text-gray-800">
                        R$ {{ number_format($total, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Pago</p>
                    <p class="text-xl font-bold text-green-600">
                        R$ {{ number_format($paid, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Pendente</p>
                    <p class="text-xl font-bold text-red-600">
                        R$ {{ number_format($pending, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Registros</p>
                    <p class="text-xl font-bold text-gray-800">
                        {{ $provisions->count() }}
                    </p>
                </div>

            </div>

            <!-- Filtro -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-2">

            <div class="flex items-center gap-3">
                <label for="year" class="text-sm font-medium text-gray-700">
                    Ano selecionado
                </label>
                <input type="number" name="year" id="year" class="block w-48 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-center font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center gap-3">
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

                <!-- Filtrar (rota atual) -->
                <button 
                    type="submit"
                    formaction="{{ route('dashboard') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                >
                    Filtrar
                </button>

                <!-- Ver parcelas -->
                <button 
                    type="submit"
                    formaction="{{ route('periodinstallments') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
                >
                    Ver parcelas do mês
                </button>

                <!-- Limpar -->
                <a href="{{ route('dashboard') }}" 
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-center">
                    Limpar
                </a>

            </form>
        </div>

            <!-- Tabela -->
            <div class="bg-white shadow-md rounded-xl p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-700">
                        Provisionamentos
                    </h3>

                    <span class="text-sm text-gray-500">
                        Total: {{ $provisions->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">

                        <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Descrição</th>
                                <th class="px-6 py-3 text-right">Parcela</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Parcelas</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Próxima Parcela</th>
                                <th class="px-6 py-3 text-center">Competência</th>
                                <th class="px-6 py-3 text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @forelse ($provisions as $provision)

                                @php
                                    $installments = $provision->provisionInstallments;

                                    $parcela = $installments->first()->amount ?? $provision->base_amount;

                                    $totalLinha = $installments->sum('amount') ?: $provision->base_amount;

                                    $qtdParcelas = $installments->count() ?: 1;

                                    $next = $installments->where('status', '!=', 'PAID')->first();
                                @endphp

                                <tr class="border-t hover:bg-gray-50 transition">

                                    <!-- Descrição -->
                                    <td class="px-6 py-3 font-medium">
                                        {{ $provision->description }}
                                    </td>

                                    <!-- Parcela -->
                                    <td class="px-6 py-3 text-right text-blue-600 font-semibold">
                                        R$ {{ number_format($parcela, 2, ',', '.') }}
                                    </td>

                                    <!-- Total -->
                                    <td class="px-6 py-3 text-right text-green-600 font-semibold">
                                        R$ {{ number_format($totalLinha, 2, ',', '.') }}
                                    </td>

                                    <!-- Qtd -->
                                    <td class="px-6 py-3 text-center">
                                        {{ $qtdParcelas }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-3 text-center">
                                        @if($next)
                                            <span class="text-yellow-600 font-semibold">Pendente</span>
                                        @else
                                            <span class="text-green-600 font-semibold">Pago</span>
                                        @endif
                                    </td>

                                    <!-- Próxima parcela -->
                                    <td class="px-6 py-3 text-center">
                                        {{ $next ? \Carbon\Carbon::parse($next->due_date)->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- Data -->
                                    <td class="px-6 py-3 text-center">
                                        {{ \Carbon\Carbon::parse($provision->competence_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <a href="/provision/{{ $provision->id }}"
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs">
                                            Visualizar
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <form action="/provision/{{ $provision->id }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este provisionamento?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-xs">
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <a href="/installments/{{ $provision->id }}"
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs">
                                            Parcelas
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-gray-400">
                                        Nenhum provisionamento encontrado
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