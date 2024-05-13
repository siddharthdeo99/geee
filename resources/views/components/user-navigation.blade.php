<div x-data="{ activeRoute: window.location.pathname }" class="border-b border-gray-950/5 dark:border-white/10 hidden md:block classic:border-black">
    <div class="container mx-auto px-4">
        <div class="flex">
            <a href="/my-ads"
               :class="activeRoute === '/my-ads' ? 'bg-black text-white dark:bg-primary-600' : ''"
               class="py-3 px-6 border-l border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
               {{ __('messages.t_my_ads') }}
            </a>

            <a href="/my-messages"
               :class="activeRoute === '/my-messages' ? 'bg-black text-white dark:bg-primary-600' : ''"
               class="py-3 px-6 border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
               {{ __('messages.t_my_messages') }}
            </a>

            <a href="/my-favorites"
               :class="activeRoute === '/my-favorites' ? 'bg-black text-white dark:bg-primary-600' : ''"
               class="py-3 px-6 border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
                My Favourites
            </a>

            <a href="/my-profile"
               :class="activeRoute === '/my-profile' ? 'bg-black  text-white dark:bg-primary-600' : ''"
               class="py-3 px-6 border-r border-gray-950/5 dark:border-white/10 classic:border-black" wire:navigate>
               {{ __('messages.t_my_profile') }}
            </a>
            <a href="/verification-center"
                :class="activeRoute === '/verification-center' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                class="py-3 px-6 border-gray-950/5 dark:border-white/10 classic:border-black  border-r" >
                {{ __('messages.t_verification_center') }}
            </a>
            @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
                <a href="/my-packages"
                    :class="activeRoute === '/my-packages' ? 'bg-black  text-white dark:bg-primary-600' : ''"
                    class="py-3 px-6 border-gray-950/5  dark:border-white/10 classic:border-black" >
                    {{ __('messages.t_my_packages') }}
                </a>
            @endif
        </div>
    </div>
</div>

