<div class="relative mr-4">
    <x-filament::dropdown placement="bottom-end">
            <x-slot name="trigger" >
                    <div class="flex items-center gap-x-1">
                        {{ $default_title }}
                        <x-icon-arrow-down-3 class="w-4 h-4 dark:text-gray-500" />
                    </div>
            </x-slot>
            <x-filament::dropdown.list>
                @foreach(fetch_active_languages() as $lang)
                <x-filament::dropdown.list.item wire:key='header-switcher-{{ $lang->lang_code  }}' wire:click="updateLocale('{{ $lang->lang_code }}')" wire:key="switch-{{ $lang->lang_code }}" @click="$refs.panel.close(event); ">
                    <div class="flex justify-between" >
                        {{ $lang->title }}
                        @if($default_lang_code === $lang->lang_code)
                            <x-heroicon-o-check class="w-4 h-4 dark:text-gray-500" />
                        @endif
                    </div>
                </x-filament::dropdown.list.item>
                @endforeach
            </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>


