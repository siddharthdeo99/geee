<li class="group" >
    <a href="{{ generate_category_url($category, null, $locationSlug) }}" class="block text-sm py-2 px-3  border border-transparent dark:border-gray-900 rounded-full  group-hover:border-black dark:group-hover:border-white/10 transition-all group-hover:transform group-hover:-translate-x-1 group-hover:-translate-y-1 classic:border-b-4 classic:border-r-4 classic:border-transparent classic:border-black" wire:navigate>{{ $category->name }}</a>

    <!-- Subcategories Dropdown -->
    <div class="group-hover:block hidden absolute z-10 mt-0 bg-white shadow-lg py-2 rounded-xl ring-1 ring-gray-950/5  dark:bg-gray-900 dark:ring-white/10 classic:ring-black  classic:group-hover:shadow-custom" >
        @foreach($category->subcategories as $subcategory)
            <a wire:key="subcategory-nav-{{ $subcategory->id }}" href="{{ generate_category_url($category, $subcategory, $locationSlug) }}" class="block px-4 py-3 text-sm underline hover:bg-gray-50  dark:hover:bg-white/5 classic:hover:bg-black classic:hover:text-white cursor-pointer" wire:navigate>{{ $subcategory->name }}</a>
        @endforeach
    </div>
</li>
