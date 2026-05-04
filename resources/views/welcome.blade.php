<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Sistema de Provisionamento Financeiro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- HERO -->
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    Planeje suas obrigações financeiras com precisão
                </h1>
                <p class="text-gray-600 text-lg">
                    Uma ferramenta simples e eficiente para estimar valores futuros,
                    simular parcelamentos e entender o impacto de juros ao longo do tempo.
                </p>
            </div>

            <!-- ABOUT -->
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Sobre o Sistema
                </h2>

                <p class="text-gray-600 mb-4">
                    O Sistema de Provisionamento Financeiro foi desenvolvido para auxiliar
                    no planejamento e na visualização de obrigações financeiras futuras,
                    permitindo simulações detalhadas de dívidas e parcelamentos.
                </p>

                <p class="text-gray-600 mb-4">
                    A aplicação permite calcular automaticamente o valor de parcelas com base
                    em diferentes tipos de juros (simples ou composto), além de projetar
                    cenários financeiros ao longo do tempo.
                </p>

                <p class="text-gray-600">
                    O foco do sistema é fornecer uma visão clara e antecipada dos compromissos,
                    sem atuar como meio de pagamento ou controle bancário.
                </p>
            </div>

            <!-- FEATURES -->
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Principais Funcionalidades
                </h2>

                <div class="grid md:grid-cols-2 gap-6">

                    <div>
                        <h3 class="font-semibold text-gray-700">📊 Simulação de Parcelas</h3>
                        <p class="text-gray-600">
                            Gere automaticamente parcelas futuras com base em valores, juros e prazos definidos.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">📈 Cálculo de Juros</h3>
                        <p class="text-gray-600">
                            Suporte a juros simples e compostos com diferentes bases de tempo.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">🗓️ Projeção Temporal</h3>
                        <p class="text-gray-600">
                            Visualize compromissos futuros com base nas datas de vencimento.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">📌 Status de Parcelas</h3>
                        <p class="text-gray-600">
                            Classificação das parcelas como em aberto, pagas ou atrasadas para fins analíticos.
                        </p>
                    </div>

                </div>
            </div>

            <!-- DISCLAIMER -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    Importante
                </h3>
                <p class="text-yellow-700 text-sm">
                    Este sistema tem caráter exclusivamente estimativo e não realiza transações financeiras,
                    pagamentos ou integração com instituições bancárias.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>