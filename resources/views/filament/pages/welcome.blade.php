<x-filament-panels::page.simple class="bg-white dark:bg-gray-900 flex justify-center items-center flex-col h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
            @if (app(App\Filament\Pages\WelcomePage::class)->hasLogo())
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="block h-9 w-auto" />
            @endif
            Seja bem-vinde ao sistema SmartOne C3
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300">
            Você é da equipe de <span class="font-semibold">{{ auth()->user()?->getRoleNames()->first() ?? 'Sem função' }}</span>
        </p>
    </div>

    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-300">Aproveite a plataforma!</p>
    </div>
</x-filament-panels::page.simple>
