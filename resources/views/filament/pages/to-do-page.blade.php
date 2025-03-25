<div class="space-y-4">
    <div class="text-lg font-semibold">Lembretes de Tarefas</div>

    @foreach ($todos as $todo)
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">{{ $todo['label'] }}</div>
                <div class="font-bold text-xl">{{ $todo['due'] }} pendente(s)</div>
            </div>
        </div>
    @endforeach
</div>
