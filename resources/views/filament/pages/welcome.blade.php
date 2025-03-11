<x-filament-panels::page.simple class="bg-white flex justify-center items-center flex-col h-screen">
    <x-slot name="heading">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
            @if (app(App\Filament\Pages\WelcomePage::class)->hasLogo())
                <img src="{{ asset('path_to_logo/logo.png') }}" alt="Logo" class="w-40 h-40 mx-auto mb-4">
            @endif
            Seja bem-vinde ao sistema SmartOne C3
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Você é da equipe de <span class="font-semibold">{{ auth()->user()->role }}</span>
        </p>
    </x-slot>

    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">Aproveite a plataforma!</p>
    </div>
</x-filament-panels::page.simple>
