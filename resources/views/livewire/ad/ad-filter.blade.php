<div class=" bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-full md:rounded-xl flex flex-col classic:ring-black">

   <div class="flex justify-between items-center py-3 px-4 border-b border-black mb-4 md:hidden">
       <h3 class="text-lg">Filter </h3>
       <div x-on:click="$dispatch('close-filter'); document.body.style.overflow = 'auto'"><x-icon-close  class="w-4 h-4" /></div>
   </div>
   <div class="flex-grow overflow-y-auto  divide-y divide-gray-200 dark:divide-white/10 classic:divide-black">
        <!-- Category List -->
        <div x-data="{ queryString: window.location.search, startWatching() { this.interval = setInterval(() => { if(this.queryString !== window.location.search) { this.queryString = window.location.search } }, 100); }, interval: null }" x-init="startWatching()" class="py-6 px-4">

            <h3 class="mb-2 font-medium">Categories:</h3>
            <ul>
                @if($isMainCategory)
                    @foreach($mainCategories as $category)
                        <li wire:key='filter-main-{{ $category->slug  }}' class="mb-1 cursor-pointer">
                            <a :href="'{{ url(generate_category_url($category, null, $locationSlug)) }}' + (queryString ? queryString : '')" wire:navigate>
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <!-- Display selected category's parent at the top with a back arrow -->
                    <li class="font-medium mb-1 cursor-pointer flex">
                        <x-heroicon-o-arrow-left wire:click="$set('isMainCategory', true)" class="w-5 h-5 mr-1" />
                        <a :href="'{{ url(generate_category_url($selectedCategory->parent ? $selectedCategory->parent : $selectedCategory, null, $locationSlug)) }}' + (queryString ? queryString : '')" wire:navigate>
                            {{ $selectedCategory->parent ? $selectedCategory->parent->name : $selectedCategory->name }}
                        </a>
                    </li>
                    @foreach($subCategories as $subCategory)
                        <li wire:key='filter-sub-{{ $subCategory->slug  }}' class="{{ $subCategory->slug == $subcategorySlug ? 'underline' : '' }} mb-1 pl-10 cursor-pointer">
                            <a :href="'{{ url(generate_category_url($selectedCategory, $subCategory, $locationSlug)) }}' + (queryString ? queryString : '')" wire:navigate>
                                {{ $subCategory->name }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        <!-- Sort By -->
        <div class="py-6 px-4">
            <x-label for="sort-by" class="mb-2 font-medium"  value="{{ __('messages.t_sort_by') }}" />
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.blur="sortBy" id="sort-by">
                    <option value="date">{{ __('messages.t_date') }}</option>
                    <option value="price_asc">{{ __('messages.t_price_low_to_high') }}</option>
                    <option value="price_desc">{{ __('messages.t_price_high_to_low') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>


        <div class="py-4">
            <!-- Min Price -->
            <div class="py-2 px-4">
                <x-label class="mb-2 font-medium"  for="min-price" value="{{ __('messages.t_min_price') }}" />
                <x-filament::input.wrapper>
                    <x-filament::input
                        placeholder="Enter min price"
                        type="text"
                        wire:model.blur="minPrice"
                        id="min-price"
                    />
                </x-filament::input.wrapper>
            </div>

            <!-- Max Price -->
            <div class="py-2 px-4">
                <x-label class="mb-2 font-medium"  for="max-price" value="{{ __('messages.t_max_price') }}" />
                <x-filament::input.wrapper>
                    <x-filament::input
                        placeholder="Enter max price"
                        type="text"
                        wire:model.blur="maxPrice"
                        id="max-price"
                    />
                </x-filament::input.wrapper>

            </div>
        </div>
    </div>
    <div class="py-2 px-4 md:hidden">
       <x-button.secondary x-on:click="$dispatch('close-filter'); document.body.style.overflow = 'auto'" class="w-full" >{{ __('messages.t_see_filtered_results') }}</x-button.secondary>
    </div>
</div>

