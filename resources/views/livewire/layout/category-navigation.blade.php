<div>
@if($categories->isNotEmpty())
    <nav class="pt-6 overflow-x-auto" x-data="{ context: @entangle('context') }">
        <ul class="flex space-x-4 whitespace-nowrap">
            <li>
                <a
                    href="/"
                    class="block text-sm rounded-full py-2 px-3 "
                    x-bind:class="context === 'home' ? 'border border-black dark:border-primary-600 dark:text-primary-600' : ''"
                    wire:navigate
                >
                {{ __('messages.t_all') }}
                </a>
            </li>

            @foreach($categories as $category)
                <livewire:layout.category-navigation-item wire:key="category-nav-{{ $category->id }}" :$locationSlug  :$category lazy />
            @endforeach
        </ul>
    </nav>
@endif
</div>
