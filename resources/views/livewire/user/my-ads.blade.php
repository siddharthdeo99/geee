<div>
    <livewire:layout.header isMobileHidden />
    <x-page-header title="{{ __('messages.t_my_ads') }}" isMobileHidden :$referrer />

    <x-user-navigation />


    <div class="container mx-auto px-4 py-10">
        @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
        <x-package-prompt />
        @endif
        <div>
            {{ $this->table }}
        </div>
    </div>
</div>

