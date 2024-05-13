<div>
    <livewire:layout.header isMobileHidden />
    <x-page-header title="{{ __('messages.t_edit_profile') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    <div class="container mx-auto px-4 py-10">
        <div class="rounded-xl md:bg-white md:shadow-sm  md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  classic:ring-black">
            <div class="justify-between px-6 py-3 hidden md:flex border-b border-gray-200  classic:border-black dark:border-white/10 ">
                <h1 class="text-xl font-semibold">{{ __('messages.t_edit_profile') }}</h1>
                <a href="{{ route('view-profile', ['id' => auth()->id(), 'slug' => $user->slug]) }}" class="underline">{{ __('messages.t_view_profile') }}</a>
            </div>
            <form wire:submit="create" novalidate>
                <div class=" pb-8 md:px-6 md:py-8 ">
                    <div>
                            {{ $this->form }}
                    </div>
                </div>

                <div class="px-6 py-4 bg-white rounded-b-xl fixed md:static bottom-0 left-0 right-0 text-right border-t border-gray-200 classic:border-black dark:border-white/10 dark:bg-gray-900">
                    <x-button.secondary type="submit" size="lg" class="w-full md:w-auto min-w-[10rem]" >{{ __('messages.t_save_changes') }}</x-button.secondary>
                </div>
            </form>
        <x-filament-actions::modals />
        </div>
    </div>

</div>
