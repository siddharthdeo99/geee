<div class="classic:border-black classic:border-y"  style="background-image: url({{ asset('/images/banner-bg.png') }}); background-size: cover;">
    <div class="container mx-auto px-4">
        <div class="flex flex-row items-center py-10 md:py-16">
                <!-- Left Section -->
                <div class="flex flex-col space-y-4 flex-1">
                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ __('messages.t_discover_chat_buy', ['siteName' => $generalSettings->site_name]) }}</h1>
                    <!-- Description -->
                    <p class="text-gray-700 text-base md:text-lg">{{ __('messages.t_explore_listings_chat', ['siteName' => $generalSettings->site_name]) }}</p>
                    <!-- Search Bar -->
                    <div class="flex items-center rounded-md home-search">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model.live="search"
                                wire:keydown.enter="performSearch"
                                placeholder="{{ __('messages.t_search_items_cars_jobs') }}"
                            />
                            <x-slot name="suffix">
                                <x-filament::icon-button
                                    icon="search"
                                    wire:click="performSearch"
                                    label="New label"
                                />
                            </x-slot>
                        </x-filament::input.wrapper>


                    </div>
                </div>

                <!-- Right Section -->
                <div class="items-center justify-center flex-1 hidden md:flex">
                    <img src="{{ asset('/images/banner.svg') }}" alt="Your image description" class="w-80 mx-auto object-cover"> <!-- Replace the src attribute with your image source -->
                </div>
        </div>
    </div>
  </div>
