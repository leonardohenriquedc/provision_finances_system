<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Provisionamento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-8">

                <form method="POST" action="/provision">
                    @csrf

                    <!-- DESCRIÇÃO -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Descrição
                        </label>
                        <input type="text" name="description"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>

                    <!-- VALOR -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Valor da Parcela
                        </label>
                        <input type="number" step="0.01" name="base_amount"
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
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Tipo de Juros
                            </label>
                            <select name="interest_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Sem juros</option>
                                <option value="SIMPLE">Simples</option>
                                <option value="COMPOUND">Composto</option>
                            </select>
                        </div>

                    </div>

                    <!-- BASE DO JUROS -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Base da Taxa
                        </label>
                        <select name="interest_period"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Selecionar</option>
                            <option value="DAY">Dia</option>
                            <option value="MONTH">Mês</option>
                            <option value="YEAR">Ano</option>
                        </select>
                    </div>

                    <!-- PARCELAS -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Quantidade de Parcelas
                        </label>
                        <input type="number" name="installments" min="1" value="1"
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
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Primeiro Vencimento
                            </label>
                            <input type="date" name="first_due_date"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                        </div>

                    </div>

                    <!-- BOTÃO -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Criar Provisionamento
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>