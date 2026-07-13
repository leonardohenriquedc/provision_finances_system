<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Provisionamentos Mensais
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-lg shadow mb-6">

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h3 class="text-xl font-semibold text-gray-800">
                Total: {{ $total }}
            </h3>
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 hover:underline"
        >
            ← Voltar
        </a>

    </div>

        <!-- Filtros -->
        <form method="GET">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                <!-- Ano -->
                <div>
                    <label
                        for="year"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Ano
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

                <!-- Mês -->
                <div>
                    <label
                        for="month"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Mês
                    </label>

                    <select
                        name="month"
                        id="month"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
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

                <!-- Status -->
                <div>
                    <label
                        for="status"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Todos</option>
                        <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>
                            Em aberto
                        </option>
                        <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>
                            Cumprido
                        </option>
                        <option value="LATE" {{ request('status') == 'LATE' ? 'selected' : '' }}>
                            Atrasado
                        </option>
                    </select>
                </div>

                <!-- Tipo -->
                <div>
                    <label
                        for="transaction_type"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Tipo
                    </label>

                    <select
                        name="transaction_type"
                        id="transaction_type"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="DEBIT" {{ request('transaction_type') == 'DEBIT' ? 'selected' : '' }}>
                            Débitos
                        </option>

                        <option value="CREDIT" {{ request('transaction_type') == 'CREDIT' ? 'selected' : '' }}>
                            Créditos
                        </option>
                    </select>
                </div>

                <!-- Botão -->
                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 transition"
                    >
                        Filtrar
                    </button>
                </div>

            </div>

        </form>

    </div>

            <div class="bg-white p-6 rounded-2xl shadow h-96">
                <canvas id="salesChart"></canvas>
            </div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const labels = @json($labels);
    const total = @json($total_month);
    const paid = @json($paid);
    const late = @json($late);

    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {
        type: 'bar',

        data: {
            labels: labels,

            datasets: [
                {
                    label: 'Total',
                    data: total,
                    backgroundColor: 'rgba(59, 130, 246, 100)',
                },
                {
                    label: 'Cumprido',
                    data: paid,
                    backgroundColor: 'rgba(34, 197, 94, 100)'
                },
                {
                    label: 'Atrasado',
                    data: late,
                    backgroundColor: 'rgba(239, 68, 68, 100)',
                }
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

});
</script>

            <!-- Tabela -->
            <div class="bg-white shadow-md rounded-xl p-6">

                <h3 class="text-xl font-semibold text-gray-700 mb-6">
                    Persistencia dos Provisionamentos
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
                                            <span class="text-green-600 font-semibold">Cumprido</span>
                                        @elseif($installment->status === 'LATE')
                                            <span class="text-red-600 font-semibold">Atrasado</span>
                                        @elseif($installment->status === 'LATE_PAYMENT')
                                            <span class="text-black-600 font-semibold">Cumprido Atrasado</span>
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

                <!-- Paginação -->
                @if ($installments->hasPages())
                    <div class="flex items-center justify-between mt-6">
                        <div class="text-sm text-gray-600">
                            Exibindo {{ $installments->firstItem() }}–{{ $installments->lastItem() }} de {{ $installments->total() }}
                        </div>

                        <div class="flex gap-2">
                            @if ($installments->onFirstPage())
                                <span class="px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    ← Anterior
                                </span>
                            @else
                                <a href="{{ $installments->previousPageUrl() }}"
                                   class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    ← Anterior
                                </a>
                            @endif

                            @if ($installments->hasMorePages())
                                <a href="{{ $installments->nextPageUrl() }}"
                                   class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    Próximo →
                                </a>
                            @else
                                <span class="px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    Próximo →
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
