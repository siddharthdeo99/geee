<div>
    <x-filament::dropdown placement="top-start">
        <x-slot name="trigger">
            <x-filament::button color="danger" icon="heroicon-m-chevron-down"  icon-position="after">
                Cache
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item wire:click="clearCache" @click="$refs.panel.close(event)" icon="heroicon-o-server-stack">
                Clear System Cache
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item wire:click="clearViews" @click="$refs.panel.close(event)" icon="heroicon-o-view-columns">
                Clear Compiled Views Cache
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item wire:click="clearLogs" @click="$refs.panel.close(event)" icon="heroicon-o-document">
                Clear Log Files
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
