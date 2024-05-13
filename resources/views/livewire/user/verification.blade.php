<div>
    <livewire:layout.header isMobileHidden />
    <x-page-header title="{{ __('messages.t_verification_center') }}" isMobileHidden :$referrer />


    <x-user-navigation />

    <div class="container mx-auto px-4 py-10">
        @if (isset($this->record) && $this->record->status != 'declined')

            <div
                class="rounded-xl md:bg-white md:shadow-sm  md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
                <div
                    class="justify-between px-6 py-3 hidden md:flex border-b border-gray-200  classic:border-black dark:border-white/10 ">
                    <h1 class="text-xl font-semibold">{{ __('messages.t_verification_center') }}</h1>
                </div>
                <form wire:submit="{{ $this->getCurrentAction() }}" novalidate>
                    <div class=" pb-8 md:px-6 md:py-8 ">
                        <div>
                            {{ $this->form }}
                        </div>
                    </div>
                    @if (!isset($this->record) || $this->record->status == 'declined')
                        <div
                            class="px-6 py-4 bg-white rounded-b-xl fixed md:static bottom-0 left-0 right-0 text-right border-t border-gray-200 classic:border-black dark:border-white/10 dark:bg-gray-900">
                            <x-button.secondary type="submit" size="lg" class="w-full md:w-auto min-w-[10rem]">
                                {{ isset($this->record) && $this->record->status == 'declined' ? __('messages.t_reupload_docs') : __('messages.t_verify') }}
                            </x-button.secondary>
                        </div>
                    @endif
                </form>
                <x-filament-actions::modals />
            </div>
        @else
            <form wire:submit="{{ $this->getCurrentAction() }}" novalidate>
                {{ $this->form }}
            </form>
        @endif
    </div>

</div>
