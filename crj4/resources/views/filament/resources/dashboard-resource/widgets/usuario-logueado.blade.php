<x-filament-widgets::widget>
    <x-filament::section>
        <p class="text-xl w-full flex items-center gap-2">
            <x-heroicon-o-user class="w-6 h-6"/>
            Hola, {{ $user }}
        </p>
        <span class="text-sm text-gray-500">
            {{ $rol }}
        </span>
    </x-filament::section>
</x-filament-widgets::widget>
