<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Provisionamento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-8">

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

                <form method="POST" action="/provision/{{ $provision->id }}">
                    @csrf
                    @method('PUT')

                    <!-- DESCRIÇÃO -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Descrição
                        </label>
                        <input type="text" name="description"
                               value="{{ old('description', $provision->description) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>

                    <!-- VALOR -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Valor da Parcela
                        </label>
                        <input type="number" step="0.01" name="base_amount"
                               value="{{ old('base_amount', $provision->base_amount) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>

                    <!-- JUROS -->
                    <div class="grid grid-cols-2 gap-4 mb-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Taxa de Juros (%)
                            </label>
                            <input type="number" step="0.01" name="interest_rate"
                                   value="{{ old('interest_rate', $provision->interest_rate) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tipo de Juros
                            </label>
                            <select name="interest_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Sem juros</option>
                                <option value="SIMPLE"
                                    {{ old('interest_type', $provision->interest_type) == 'SIMPLE' ? 'selected' : '' }}>
                                    Simples
                                </option>
                                <option value="COMPOUND"
                                    {{ old('interest_type', $provision->interest_type) == 'COMPOUND' ? 'selected' : '' }}>
                                    Composto
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- BASE -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Base da Taxa
                        </label>
                        <select name="interest_period"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Selecionar</option>
                            <option value="DAY"
                                {{ old('interest_period', $provision->interest_period) == 'DAY' ? 'selected' : '' }}>
                                Dia
                            </option>
                            <option value="MONTH"
                                {{ old('interest_period', $provision->interest_period) == 'MONTH' ? 'selected' : '' }}>
                                Mês
                            </option>
                            <option value="YEAR"
                                {{ old('interest_period', $provision->interest_period) == 'YEAR' ? 'selected' : '' }}>
                                Ano
                            </option>
                        </select>
                    </div>

                    <!-- PARCELAS -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Quantidade de Parcelas
                        </label>
                        <input type="number" name="installments" min="1"
                               value="{{ old('installments', $provision->installments) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>

                    <!-- DATAS -->
                    <div class="grid grid-cols-2 gap-4 mb-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Data de Competência
                            </label>
                            <input type="date" name="competence_date"
                                   value="{{ old('competence_date', $provision->competence_date) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Primeiro Vencimento
                            </label>
                            <input type="date" name="first_due_date"
                                   value="{{ old('first_due_date', $provision->first_due_date) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                        </div>

                    </div>

                    <!-- BOTÕES -->
                    <div class="flex justify-between">

                        <a href="/provisions"
                           class="text-gray-600 hover:underline">
                            ← Voltar
                        </a>

                        <button type="submit"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            Atualizar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>