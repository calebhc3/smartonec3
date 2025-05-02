<x-filament::page>
    <x-filament::card>
        {{ $this->table }}
    </x-filament::card>
    <script>
    window.addEventListener('dispatch-filtro-afastamentos', event => {
        Livewire.emit('filtrosAtualizados', {
            dataInicial: event.detail.dataInicial,
            dataFinal: event.detail.dataFinal,
        });
    });
</script>
</x-filament::page>
