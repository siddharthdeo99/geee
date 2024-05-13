<div x-data="{ ref: window.location.pathname.substring(1) + window.location.search }">
    <div class="flex items-center p-4 bg-white dark:bg-black border-b classic:border-black sticky top-0 z-10">
        <h1 class="text-xl font-medium">{{ auth()->user()->name }}{{ __('messages.t_user_account') }}</h1>
    </div>

    <!-- Account options list -->
    <div class="divide-y classic:divide-black pb-20">
        <a :href="'/my-profile?ref=' + ref"  class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800" wire:navigate>
            <div class="flex items-center gap-x-2">
                <x-icon-user-protection-person class="w-5 h-5" />
                {{ __('messages.t_my_profile') }}
            </div>
            <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
        </a>

        <a :href="'/my-ads?ref=' + ref" class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800" wire:navigate>
            <div class="flex items-center gap-x-2">
                <x-icon-signage class="w-5 h-5" />
                {{ __('messages.t_my_ads') }}
            </div>
            <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
        </a>

        <a :href="'/my-messages?ref=' + ref" class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800" wire:navigate>
            <div class="flex items-center gap-x-2">
                <x-icon-chat-bubble-text-square class="w-5 h-5" />
                {{ __('messages.t_my_messages') }}
            </div>
            <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
        </a>

        <a :href="'/verification-center?ref=' + ref" class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800">
            <div class="flex items-center gap-x-2">
                <x-icon-user-protection-person class="w-5 h-5" />
                {{ __('messages.t_verification_center') }}
            </div>
            <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
        </a>

        @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
            <a :href="'/choose-package?ref=' + ref" class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                <div class="flex items-center gap-x-2">
                    <x-icon-list class="w-5 h-5" />
                    {{ __('messages.t_buy_packages') }}
                </div>
                <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
            </a>

            <a :href="'/my-packages?ref=' + ref" class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                <div class="flex items-center gap-x-2">
                    <x-icon-bag-dollar class="w-5 h-5" />
                    {{ __('messages.t_my_packages') }}
                </div>
                <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
            </a>
        @endif

        <a :href="'/my-favorites?ref=' + ref"  class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800" wire:navigate>
            <div class="flex items-center gap-x-2">
                <x-icon-heart-core class="w-5 h-5" />
                {{ __('messages.t_my_favourites') }}
            </div>
            <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
        </a>


        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a class="flex items-center justify-between gap-x-2 px-4 py-4 hover:bg-gray-100 dark:hover:bg-gray-800" onclick="event.preventDefault(); this.closest('form').submit();" >
                <div class="flex items-center gap-x-2">
                    <x-icon-logout-1 class="w-5 h-5" />
                    {{ __('messages.t_logout') }}
                </div>
                <x-icon-arrow-down-3 class="w-5 h-5 transform -rotate-90" />
            </a>
        </form>
    </div>

    <livewire:layout.bottom-navigation />
</div>
