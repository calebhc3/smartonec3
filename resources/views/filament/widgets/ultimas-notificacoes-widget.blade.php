<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800">Últimas Notificações</h2>

            <ul class="divide-y divide-gray-100">
                @forelse ($ultimas_notificacoes as $item)
                    <li class="py-3 flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if ($item->is_prorrogado)
                                <x-heroicon-c-arrow-path class="h-6 w-6 text-blue-500" />
                            @elseif ($item->status_atual === 'Afastado')
                                <x-heroicon-o-plus-circle class="h-6 w-6 text-green-500" />
                            @elseif ($item->status_atual === 'Retornado')
                                <x-heroicon-o-check-circle class="h-6 w-6 text-emerald-500" />
                            @else
                                <x-heroicon-o-information-circle class="h-6 w-6 text-gray-400" />
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                <span class="font-medium">{{ $item->nome }}</span> 
                                - 
                                <span class="text-gray-600">
                                    {{ $item->is_prorrogado ? 'teve prorrogação' : ($item->status_atual === 'Retornado' ? 'retornou às atividades' : 'afastamento criado') }}
                                </span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $item->created_at->diffForHumans() }} ({{ $item->created_at->format('d/m/Y H:i') }})
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-gray-400 text-sm">Nenhuma notificação recente.</li>
                @endforelse
            </ul>
        </div>
    </x-filament::card>
</x-filament::widget>
