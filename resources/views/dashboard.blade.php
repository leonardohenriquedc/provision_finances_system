<x-app-layout>
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
                    <p class="text-sm text-gray-500">Cumprido</p>
                    <p class="text-xl font-bold text-green-600">
                        R$ {{ number_format($paid, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Provisionado não cumprido</p>
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
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 items-end">

                    <div class="flex flex-col gap-1">
                        <label for="year" class="text-sm font-medium text-gray-700">
                            Ano selecionado
                        </label>
                        <input
                            type="number"
                            name="year"
                            id="year"
                            value="{{ request('year') }}"
                            placeholder="Todos"
                            min="1000"
                            max="9999"
                            oninput="if(this.value.length > 4) this.value = this.value.slice(0,4)"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="month" class="text-sm font-medium text-gray-700">
                            Mês selecionado
                        </label>
                        <select
                            name="month"
                            id="month"
                            onchange="this.form.submit()"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="todos" {{ $month == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="1" {{ $month == 1 ? 'selected' : '' }}>Janeiro</option>
                            <option value="2" {{ $month == 2 ? 'selected' : '' }}>Fevereiro</option>
                            <option value="3" {{ $month == 3 ? 'selected' : '' }}>Março</option>
                            <option value="4" {{ $month == 4 ? 'selected' : '' }}>Abril</option>
                            <option value="5" {{ $month == 5 ? 'selected' : '' }}>Maio</option>
                            <option value="6" {{ $month == 6 ? 'selected' : '' }}>Junho</option>
                            <option value="7" {{ $month == 7 ? 'selected' : '' }}>Julho</option>
                            <option value="8" {{ $month == 8 ? 'selected' : '' }}>Agosto</option>
                            <option value="9" {{ $month == 9 ? 'selected' : '' }}>Setembro</option>
                            <option value="10" {{ $month == 10 ? 'selected' : '' }}>Outubro</option>
                            <option value="11" {{ $month == 11 ? 'selected' : '' }}>Novembro</option>
                            <option value="12" {{ $month == 12 ? 'selected' : '' }}>Dezembro</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        formaction="{{ route('dashboard') }}"
                        class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-blue-700 transition shadow-sm"
                    >
                        Filtrar
                    </button>

                    <button
                        type="submit"
                        formaction="{{ route('periodinstallments') }}"
                        class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-green-700 transition shadow-sm"
                    >
                        Provisionamentos mensais
                    </button>

                    <a href="{{ route('dashboard') }}"
                       class="w-full bg-gray-500 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-gray-600 transition shadow-sm text-center">
                        Limpar
                    </a>

                </form>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow h-96">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Total Provisionado X Provisões Concluidas</h3>
                    <div class="h-[calc(100%-2rem)]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow h-96">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Total de Provisionamentos por datas</h3>
                    <div class="h-[calc(100%-2rem)]">
                        <canvas id="provisionsChart"></canvas>
                    </div>
                </div>
            </div>
        <script>
        document.addEventListener('DOMContentLoaded', () => {

            const paid = @json($paid);
            const pending = @json($pending);

            const ctx = document.getElementById('salesChart');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Cumprido', 'Provisionado'],
                    datasets: [{
                        data: [paid, pending],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let labels = [];
                let values = [];

                const provisions = @json($chartValues);

                for (const [date, value] of Object.entries(provisions)) {
                    labels.push(date);
                    values.push(value);
                }

                const ctx = document.getElementById('provisionsChart');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Provisionamentos",
                            data: values,
                            borderColor: 'rgba(59, 130, 246, 0.8)',
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            })
        </script>

            <!-- Tabela -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-700">
                        Provisionamentos
                    </h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                        Total: {{ $provisions->count() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Parcela</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Parcelas</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Próxima Parcela</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($provisions as $provision)
                                @php
                                    $installments = $provision->provisionInstallments;
                                    $parcela = $installments->first()->amount ?? $provision->base_amount;
                                    $totalLinha = $installments->sum('amount') ?: $provision->base_amount;
                                    $qtdParcelas = $installments->count() ?: 1;
                                    $next = $installments->whereNotIn('status', ['PAID', 'LATE_PAYMENT'])->first();
                                @endphp

                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                        {{ $provision->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-blue-600 font-semibold">
                                        R$ {{ number_format($parcela, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600 font-semibold">
                                        R$ {{ number_format($totalLinha, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $qtdParcelas }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($next)
                                            <span class="text-xs font-semibold text-yellow-600">
                                                Pendente
                                            </span>
                                        @else
                                            <span class="text-xs font-semibold text-green-600">
                                                Cumprido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $next ? \Carbon\Carbon::parse($next->due_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($provision->transaction_type === 'DEBIT')
                                            <span class="text-xs font-semibold text-red-600">
                                                Débito
                                            </span>
                                        @elseif($provision->transaction_type === 'CREDIT')
                                            <span class="text-xs font-semibold text-green-600">
                                                Crédito
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500">
                                                {{ $provision->transaction_type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="/provision/{{ $provision->id }}"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                                Visualizar
                                            </a>

                                            <a href="/installments/{{ $provision->id }}"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                                Parcelas
                                            </a>

                                            <form action="/provision/{{ $provision->id }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este provisionamento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 italic">
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
