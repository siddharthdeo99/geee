<div>
    <x-filament::modal id="sidebar" slide-over alignment="start" sticky-header>
        <x-slot name="heading">
            <x-brand />
        </x-slot>
        <x-slot name="description">
            <div class="mt-4">
                <livewire:partials.language-switcher />
            </div>
        </x-slot>
        <div>
            <div class="space-y-8">

                <div class="">
                    <h3 class="font-semibold leading-6 text-lg dark:text-white">
                        {{ __('messages.t_popular_categories') }}
                    </h3>
                    <ul role="list" class="mt-4 space-y-4">
                        @foreach($popularCategories as $category)
                            <li>
                                @if($category->parent)
                                    <!-- This is a subcategory -->
                                    <a wire:key="popular-category-{{ $category->id }}" href="{{ route('ad-category', ['category' => $category->parent->slug, 'subcategory' => $category->slug]) }}" class="leading-6 dark:hover:text-white">
                                        {{ $category->name }}
                                    </a>
                                @else
                                    <!-- This is a main category -->
                                    <a wire:key="popular-category-{{ $category->id }}" href="{{ route('ad-category', ['category' => $category->slug]) }}" class="leading-6 dark:hover:text-white">
                                        {{ $category->name }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="">
                    <h3 class="font-semibold leading-6  dark:text-white text-lg">
                        {{ __('messages.t_company') }}
                    </h3>
                    <ul role="list" class="mt-4 space-y-4">
                        @if(app('filament')->hasPlugin('blog') && $blogSettings->enable_blog)
                            <li>
                                <a href="/blog" class="leading-6  dark:hover:text-white">
                                    Blog
                                </a>
                            </li>
                        @endif
                        @if(optional($aboutUsPage)->status === 'visible')
                        <li>
                            <a href="/pages/about-us" class="leading-6  dark:hover:text-white">
                                About us
                            </a>
                        </li>
                        @endif
                        @if(optional($careersPage)->status === 'visible')
                        <li>
                            <a href="/pages/careers" class="leading-6  dark:hover:text-white">
                                Careers
                            </a>
                        </li>
                        @endif
                        @foreach ($pages as $page)
                            <li wire:key="page-{{ $page->id }}">
                                <a href="{{ url('/pages/' . $page->slug) }}" class="leading-6 dark:hover:text-white">
                                    {{ $page->title }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="/contact" class="leading-6  dark:hover:text-white">
                                Contact us
                            </a>
                        </li>
                    </ul>
                </div>
                @if(optional($termsPage)->status === 'visible' || optional($privacyPage)->status === 'visible')
                <div class="">
                    <h3 class="font-semibold leading-6  dark:text-white text-lg">
                        {{ __('messages.t_legal') }}
                    </h3>
                    <ul role="list" class="mt-4 space-y-4">
                        @if(optional($termsPage)->status === 'visible')
                        <li>
                            <a href="/pages/terms-conditions" class="leading-6  dark:hover:text-white">
                                Terms &amp; Conditions
                            </a>
                        </li>
                        @endif
                        @if(optional($privacyPage)->status === 'visible')
                        <li>
                            <a href="/pages/privacy-policy" class="leading-6  dark:hover:text-white">
                                Privacy Policy
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                @endif
                <x-social-media />
            </div>
        </div>
        <div class="mt-8 border-t dark:border-white/10 border-gray-200 pt-6 classic:border-black">
            <p class="text-center">
                © {{ now()->year }} {{ $generalSettings->site_name }}. {{ __('All rights reserved.') }}
            </p>
        </div>
    </x-filament::modal>
</div>
