<x-filament::dropdown placement="top-end" x-cloak>
    <x-slot name="trigger">
        <button type="button"
            class="flex h-7 w-7 mr-4 items-center justify-center rounded-full ring-[0.1rem] ring-black dark:bg-black dark:ring-inset dark:ring-white/5">
            <x-icon-light x-show="theme === 'light'" class="w-5 h-5 dark:text-gray-500" />
            <x-icon-dark x-show="theme === 'dark'" class="w-6 h-6 dark:text-primary-600" />
            <x-heroicon-o-square-3-stack-3d x-show="theme === 'classic'" class="w-6 h-6 dark:text-primary-600" />
        </button>
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item icon="light" @click="theme = 'light';  $refs.panel.close(event); ">
            Light Mode
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item icon="dark" @click="theme = 'dark';  $refs.panel.close(event); ">
            Dark Mode
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item icon="heroicon-o-square-3-stack-3d" @click="theme = 'classic';  $refs.panel.close(event); ">
            Classic Mode
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
