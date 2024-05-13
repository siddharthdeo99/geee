<div>

    <livewire:layout.header />

    <div class="flex flex-col items-center justify-center min-h-screen md:min-h-full md:mt-20">
        <!-- Success Image -->
        <img src="{{ asset('/images/tick.svg') }}" alt="success" class="w-16 h-16 mb-4">

        <!-- Congratulations Text -->
        <h2 class="text-xl mb-2 font-semibold">{{ __('messages.t_ad_upgrade_success') }}</h2>

        <p class="text-center mb-6">{{ __('messages.t_ad_upgrade_message') }}</p>

        <div class="flex justify-center gap-x-4">
            @if($ad)
                <x-filament::button
                    href="{{ route('ad-details', ['slug' => $ad->slug ]) }}"
                    tag="a"
                    outlined
                >
                {{ __('messages.t_preview_ad') }}
                </x-filament::button>
            @endif

            <x-filament::button
                href="{{ route('home') }}"
                tag="a"
                color="gray"
            >
            {{ __('messages.t_back_to_home') }}
            </x-filament::button>

        </div>
    </div>
    <livewire:layout.sidebar  />
</div>

