<div x-data="{ search: '', open: false, categories: @entangle('categories'), ads: @entangle('ads')  }" @click.away="open = false" class="relative md:mr-4 w-full md:w-auto">
    <x-icon-search class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-0 top-0 mt-[.6rem] md:mt-[.6rem] ml-2 classic:text-black"/>
    <input wire:keydown.enter="performSearch" wire:model.live.debounce.300ms="search" x-model="search" type="text" @input="open = search.length > 0" class="shadow-sm ring-1 ring-gray-950/10 border-none bg-white h-10 md:h-10 pl-10 pr-4 w-full md:min-w-[300px] lg:min-w-[350px] rounded-xl focus:outline-none placeholder-muted focus-within:ring-2 dark:bg-white/5  focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500 dark:placeholder:text-gray-500  classic:ring-black" placeholder="{{ __('messages.t_search_ads') }}">
    <x-icon-close @click="search = ''; open = false" class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute right-0 top-0 mt-[.8rem] mr-3 cursor-pointer  classic:text-black" x-show="search.length > 0" x-cloak />

    <!-- Dropdown -->
    <div x-show="open" class="absolute w-full mt-1 bg-white shadow-lg ring-1 ring-gray-950/5 dark:ring-white/10  rounded-xl z-10 h-[70vh] md:h-auto overflow-auto  classic:ring-black  classic:shadow-custom dark:bg-gray-800" x-cloak>

        <!-- Check if both categories and ads are empty -->
        @if($categories && $categories->isEmpty() &&$ads && $ads->isEmpty())
            <div class="p-10">
               <x-not-found description="{{ __('messages.t_no_search_results') }}" size="sm" />
            </div>
        @endif

        <!-- Categories Section -->
        @if($categories && !$categories->isEmpty())
            <div>
                <h3 class="px-4 py-2 text-sm font-semibold capitalize border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-gray-900 classic:border-black classic:bg-white">{{ __('messages.t_categories') }}</h3>
                <div class="grid grid-cols-1 divide-y divide-gray-200 dark:divide-white/10 classic:divide-none">
                    @foreach($categories as $category)
                        <div wire:key='search-category-{{ $category->id }}'>
                            @if($category->parent)
                                <!-- This is a subcategory -->
                                <a  href="{{ generate_category_url($category->parent, $category, $locationSlug) }}" class="flex items-center px-4 py-3 cursor-pointer transition duration-75 focus-within:bg-gray-50 hover:bg-gray-50 dark:focus-within:bg-white/5 dark:hover:bg-white/5">
                            @else
                                <!-- This is a main category -->
                                <a href="{{ generate_category_url($category, null, $locationSlug) }}" class="flex items-center px-4 py-3 cursor-pointer transition duration-75 focus-within:bg-gray-50 hover:bg-gray-50 dark:focus-within:bg-white/5 dark:hover:bg-white/5">
                            @endif
                                <img src="{{ $category->parent ? $category->parent->icon : $category->icon }}" alt="Category Image" class="w-10 h-10 rounded object-cover">
                                <div class="ml-3 group-hover:text-white">
                                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $category->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $category->parent ? $category->parent->name : '' }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        <!-- Ads Section -->
        @if($ads && !$ads->isEmpty())
            <div>
                <h3 class="px-4 py-2 text-sm font-semibold capitalize border-y border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-gray-900  classic:border-black  classic:bg-white">{{ __('messages.t_ads') }}</h3>
                <div class="grid grid-cols-1 divide-y divide-gray-200 dark:divide-white/10 classic:divide-none">
                    @foreach($ads as $ad)
                        <a wire:key='search-ad-{{ $ad->id }}' href="{{ route('ad-details', ['slug' => $ad->slug ]) }}" class="flex items-center px-4 py-3 transition duration-75  hover:bg-gray-50  dark:hover:bg-white/5 cursor-pointer">
                            <img src="{{ $ad->primaryImage ?? asset('/images/placeholder.jpg') }}" alt="Ad Image" class="w-10 h-10 rounded object-cover">
                            <div class="ml-3 group-hover:text-white">
                                <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $ad->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ad->location_name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
