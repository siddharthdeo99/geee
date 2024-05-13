
<a href="{{ route('ad-category', ['category' => $category->slug]) }}" class="block ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  md:px-5 md:py-5 p-4 bg-white rounded-xl transition-all hover:transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-md classic:ring-black classic:hover:shadow-custom">
    <div class="flex md:gap-x-3 flex-col md:flex-row items-center md:items-start">
        <img src="{{ $category->icon }}" alt="" class="h-14 w-14 md:w-20 md:h-20 pb-3">
        <div>
            <h3 class="md:text-lg md:font-bold text-center md:text-left">{{ $category->name }}</h3>
            <p class="py-2 hidden md:block">{{ $category->description }}</p>
            <div class="hidden lg:flex gap-x-7 mt-2">
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-icon-user-multiple-group class="w-5 h-5 stroke-2 dark:text-gray-500" />
                    <span class=" text-sm ">{{ __('messages.t_sellers_count', ['count' => $sellersCount]) }}</span>
                </div>
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-icon-list class="w-4 h-4 stroke-2 dark:text-gray-500" />
                    <span class=" text-sm">{{ __('messages.t_listings_count', ['count' => $listingsCount]) }}</span>
                </div>
                <div class=" flex gap-x-1.5 items-center text-muted dark:text-gray-400">
                    <x-icon-bag-dollar class="w-4 h-4 stroke-2 dark:text-gray-500" />
                    <span class="text-sm">{{ config('app.currency_symbol') }}{{ __('messages.t_sales_count', ['count' => $salesCount]) }}</span>
                </div>
            </div>
        </div>
    </div>
</a>
