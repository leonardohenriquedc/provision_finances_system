<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Atualizar Parcela
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-lg shadow">

                <!-- ERROS -->
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/installment/{{ $installment->id }}">
                    @csrf
                    @method('PUT')

                    <!-- PROVISION -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Descrição
                        </label>
                        <input type="text"
                               value="{{ $installment->provision->description }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                               readonly>
                    </div>

                    <!-- Nº PARCELA -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Número da Parcela
                        </label>
                        <input type="text"
                               value="{{ $installment->installment_number }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                               readonly>
                    </div>

                    <!-- VALOR -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Valor
                        </label>
                        <input type="text"
                               value="R$ {{ number_format($installment->amount, 2, ',', '.') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                               readonly>
                    </div>

                    <!-- VENCIMENTO -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Data de Vencimento
                        </label>
                        <input type="text"
                               value="{{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                               readonly>
                    </div>

                    <!-- STATUS (EDITÁVEL) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <option value="OPEN"
                                {{ $installment->status == 'OPEN' ? 'selected' : '' }}>
                                Em aberto
                            </option>

                            <option value="PAID"
                                {{ $installment->status == 'PAID' ? 'selected' : '' }}>
                                Pago
                            </option>

                            <option value="LATE"
                                {{ $installment->status == 'LATE' ? 'selected' : '' }}>
                                Atrasado
                            </option>

                        </select>
                    </div>

                    <!-- BOTÕES -->
                    <div class="flex justify-between">

                        <a href="/installments/{{ $installment->provision_id }}"
                           class="text-gray-600 hover:underline">
                            ← Voltar
                        </a>

                        <button type="submit"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            Atualizar Status
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>