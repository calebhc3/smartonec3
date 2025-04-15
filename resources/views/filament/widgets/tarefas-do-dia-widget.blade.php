<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800">Tarefas do Dia</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Afastamentos com 15 dias -->
                <div class="bg-white shadow rounded-xl p-4 border border-blue-100">
                    <h3 class="font-semibold text-blue-600">Prorrogação - 15 dias</h3>
                    @forelse ($afastamentos_15_dias as $afastamento)
                        <div class="mt-2 p-2 bg-blue-50 rounded text-sm text-gray-700">
                            <span class="font-medium">{{ $afastamento->nome }}</span><br>
                            <span class="text-xs text-gray-500">Previsto para: {{ \Carbon\Carbon::parse($afastamento->termino_previsto_beneficio)->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm mt-2">Sem tarefas nessa categoria.</p>
                    @endforelse
                </div>

                <!-- Afastamentos com 10 dias -->
                <div class="bg-white shadow rounded-xl p-4 border border-yellow-100">
                    <h3 class="font-semibold text-yellow-600">Notificar Shopee - 10 dias</h3>
                    @forelse ($afastamentos_10_dias as $afastamento)
                        <div class="mt-2 p-2 bg-yellow-50 rounded text-sm text-gray-700">
                            <span class="font-medium">{{ $afastamento->nome }}</span><br>
                            <span class="text-xs text-gray-500">Previsto para: {{ \Carbon\Carbon::parse($afastamento->termino_previsto_beneficio)->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm mt-2">Nada pra hoje aqui.</p>
                    @endforelse
                </div>

                <!-- Notificações para hoje -->
                <div class="bg-white shadow rounded-xl p-4 border border-green-100">
                    <h3 class="font-semibold text-green-600">Notificações de hoje</h3>
                    @forelse ($notificacoes_hoje as $afastamento)
                        <div class="mt-2 p-2 bg-green-50 rounded text-sm text-gray-700">
                            <span class="font-medium">{{ $afastamento->nome }}</span><br>
                            <span class="text-xs text-gray-500">Data: {{ \Carbon\Carbon::parse($afastamento->notificar_shopee_retorno)->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm mt-2">Tudo em dia por aqui!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
