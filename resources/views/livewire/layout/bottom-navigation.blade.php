
<div class="bg-white dark:bg-gray-900  fixed inset-x-0 bottom-0  shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 md:hidden classic:ring-black" x-data="{ activeRoute: window.location.pathname }">
    <nav class="flex justify-between">
      <a href="/" class="flex flex-col items-center w-1/4 py-2 text-center " :class="{ 'font-bold dark:text-primary-600': activeRoute === '/' }">
        <x-icon-home x-show="activeRoute !== '/'" class="w-5 h-5"  />
        <x-icon-home-s x-show="activeRoute === '/'" class="w-5 h-5 dark:text-primary-600"  x-cloak />
        <span>{{ __('messages.t_home') }}</span>
      </a>

      <a href="/my-messages" class="flex flex-col items-center w-1/4 py-2 text-center " :class="{ 'font-bold dark:text-primary-600': activeRoute === '/my-messages' }">
        <x-icon-chat-bubble-text-square x-show="activeRoute !== '/my-messages'" class="w-5 h-5"  />
        <x-icon-chat-bubble-text-square-s x-show="activeRoute === '/my-messages'" class="w-5 h-5 dark:text-primary-600" x-cloak />
        <span>{{ __('messages.t_message') }}</span>

      </a>

      <a href="/my-ads" class="flex flex-col items-center w-1/4 py-2 text-center" :class="{ 'font-bold dark:text-primary-600': activeRoute === '/my-ads' }">
        <x-icon-signage x-show="activeRoute !== '/my-ads'" class="w-5 h-5"  />
        <x-icon-signage-s x-show="activeRoute === '/my-ads'" class="w-5 h-5 dark:text-primary-600" x-cloak />
        <span>{{ __('messages.t_my_ads') }}</span>

      </a>

      <a href="/my-account" class="flex flex-col items-center w-1/4 py-2 text-center" :class="{ 'font-bold dark:text-primary-600': activeRoute === '/my-account' }">
        <x-icon-user-protection-person x-show="activeRoute !== '/my-account'" class="w-5 h-5"  />
        <x-icon-user-protection-person-s x-show="activeRoute === '/my-account'" class="w-5 h-5 dark:text-primary-600" x-cloak />
        <span>{{ __('messages.t_my_profile') }}</span>

      </a>
    </nav>
</div>
