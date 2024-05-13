<header x-data="{ isSticky: false, sidebarOpen: false, isMobileHidden:@entangle('isMobileHidden'), isAuthenticated: {{ auth()->user() ? 'true' : 'false' }} }" @scroll.window="isSticky = (window.pageYOffset > 50)"
    :class="{ 'sticky top-0 z-30 ': isSticky, 'hidden md:block': isMobileHidden }" class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black classic:border-t-2 classic:border-black"
    x-show="!isMobileHidden || !isMobile"
    x-cloak
>
    <div class="container mx-auto py-4 md:py-6 px-4">
        <div class="block md:flex justify-between items-center">
            <div class="flex items-center flex-wrap md:flex-row md:flex-nowrap gap-y-4">
                <!-- Logo -->
                <div class="w-auto md:order-1 md:mr-4 flex items-center gap-x-2" >
                    <button class="md:hidden"  @click="$dispatch('open-modal', { id: 'sidebar' })">
                        <x-heroicon-m-bars-3 class="w-6 h-6 text-gray-800 dark:text-white" />
                    </button>
                    <x-brand  />
                </div>

                <div class="w-auto flex-grow md:flex-grow-0 md:order-3">
                    <livewire:layout.location :$locationSlug  />
                </div>
                <div class="w-full md:w-auto md:order-2 flex justify-between items-center">
                    <div class="flex flex-grow">
                       <livewire:layout.search-bar lazy :$locationSlug />
                    </div>
                    <div class="md:hidden ml-3 flex items-center">

                        <x-theme-switcher />
                        @if($isSearch)
                            <div x-on:click="$dispatch('show-filter'); document.body.style.overflow = 'hidden'">
                               <x-icon-filter class="w-5 h-5 dark:text-gray-400" />
                            </div>
                        @else
                            @if (auth()->check())
                                <div>
                                  @livewire('database-notifications')
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="md:flex items-center hidden">
                <livewire:partials.language-switcher />
                <x-theme-switcher />
                 <!-- Login -->
                 <div x-show="!isAuthenticated" class="mr-6" x-cloak>
                    <a href="/login">{{ __('messages.t_login') }}</a>
                </div>
                <div x-show="isAuthenticated" class="flex gap-x-4 items-center mr-4" x-cloak>
                    <div class="hidden lg:block"><a href="/my-messages"><x-icon-chat-bubble-text-oval class="w-[1.675rem] h-[1.675rem] dark:text-gray-500" /></a></div>

                    @if (auth()->check())
                         @livewire('database-notifications')
                    @endif
                    <x-filament::dropdown placement="top-end">
                        <x-slot name="trigger">
                            <div class="flex items-center gap-x-1">
                                @if(auth()->check())
                                <div class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border-[0.1rem] border-black rounded-full h-8 w-8 flex items-center justify-center">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{auth()->user()->profile_image }}" alt="{{ auth()->user()->name[0] }}" class="rounded-full w-8 h-8">
                                    @else
                                        <span>{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>

                                    @endif
                                </div>
                                @endif
                                <x-icon-arrow-down-3 class="w-4 h-4 dark:text-gray-500" />
                            </div>
                        </x-slot>

                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item
                                href="\my-profile"
                                tag="a"
                                icon="user-protection-person"
                            >
                               {{ __('messages.t_my_profile') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                href="\my-ads"
                                tag="a"
                                icon="signage"
                            >
                            {{ __('messages.t_my_ads') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                href="\my-messages"
                                tag="a"
                                icon="chat-bubble-text-square"
                            >
                              {{ __('messages.t_my_messages') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                href="\my-favorites"
                                tag="a"
                                icon="heart-core"
                            >
                            {{ __('messages.t_my_favorites') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                href="\verification-center"
                                tag="a"
                                icon="user-protection-person"
                            >
                            {{ __('messages.t_verification_center') }}
                            </x-filament::dropdown.list.item>

                            @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
                                <x-filament::dropdown.list.item
                                    href="\my-packages"
                                    tag="a"
                                    icon="bag-dollar"
                                >
                                {{ __('messages.t_my_packages') }}
                                </x-filament::dropdown.list.item>
                            @endif

                            @if(app('filament')->hasPlugin('packages') && $packageSettings->status)
                                <x-filament::dropdown.list.item
                                    href="\choose-package"
                                    tag="a"
                                    icon="list"
                                >
                                {{ __('messages.t_buy_business_packages') }}
                                </x-filament::dropdown.list.item>
                            @endif


                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-filament::dropdown.list.item    icon="logout-1"  onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('messages.t_logout') }}
                                </x-filament::dropdown.list.item>
                            </form>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>


                </div>
                <!-- Post your ad -->
                <div>
                    <a href="/post-ad" class="bg-gray-900 block text-white py-2 px-4 rounded-xl dark:bg-primary-600 dark:text-black">
                        {{ __('messages.t_post_your_ad') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="md:block hidden">
            <livewire:layout.category-navigation :$context  />
        </div>
    </div>
</header>
