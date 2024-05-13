<div class="text-sm" x-data="{ showMainCategories: @entangle('showMainCategories'), id: @entangle('id'), parent_category: @entangle('parent_category') }">
    <form wire:submit>
        <div class="mb-5">
            {{ $this->titleInput }}
        </div>
        <!-- Main Categories Section -->
        <div x-show="showMainCategories && id !== ''">
            <h3 class="mb-2">{{ __('messages.t_select_category') }}</h3>
            <x-input-error :messages="$errors->get('parent_category')" class="mb-2" />

            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 mt-2">
                @foreach ($categories as $category)
                    <div wire:key="main-{{ $category->id }}" wire:click="selectCategory({{ $category->id }})"
                        class="ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  text-center p-4 rounded-xl cursor-pointer transition-all hover:transform hover:-translate-x-1 hover:-translate-y-1 hover:bg-white classic:ring-black classic:hover:shadow-custom"
                        @click="showMainCategories = false"
                        :class="parent_category === {{ $category->id }} ?
                            'bg-white border border-black border-b-4 border-r-4 ' : ''">
                        <!-- Display the category's icon or a default one. -->
                        <img src="{{ $category->icon ? asset($category->icon) : asset('/images/category-icon.svg') }}"
                            alt="{{ $category->name }}" class="mx-auto w-10 h-10">
                        <h5 class="font-bold py-3">{{ $category->name }}</h5>
                        <span class="text-sm hidden md:inline-block">{{ $category->description }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Selected Category & Subcategories Section -->
        @if (!is_null($parent_category))
            <div class="mb-5" x-show="!showMainCategories">
                <!-- Display the chosen category notification -->
                <div class="mt-6 mb-4 font-medium">
                    <div>{{ __('messages.t_you_have_chosen') }}
                        <strong>{{ $categories->firstWhere('id', $parent_category)->name }}</strong>.

                        <span class="underline cursor-pointer"
                            @click="showMainCategories = true">{{ __('messages.t_change_category') }}</span>
                    </div>
                </div>

                <!-- Subcategories Dropdown -->
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="category_id">
                            <option value="" disabled selected>{{ __('messages.t_select_subcategory') }}</option>
                            @foreach ($categories->firstWhere('id', $parent_category)->subcategories as $subcategory)
                                <option wire:key='category-{{ $subcategory->id }}' value="{{ $subcategory->id }}">
                                    {{ $subcategory->name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div class="mb-5">
                {{ $this->detailForm }}
            </div>


        @endif

    </form>
</div>
