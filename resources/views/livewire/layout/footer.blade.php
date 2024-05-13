<footer class="hidden md:block ">
    @if(!auth()->user())
    <div class="bg-black py-5 dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="container mx-auto py-10">
            <div class="md:flex flex-col md:items-center space-y-5">
                <h3 class="text-white text-2xl font-bold">{{ __('messages.t_post_connect_deal') }}</h3>

                <p class="text-white text-lg">
                    {{ __('Post your ad, connect with buyers, and close deals smoothly. Experience the ease with :siteName today!', ['siteName' => $generalSettings->site_name]) }}
                </p>

                <a href="/post-ad"
                    class="bg-primary-600 text-black flex gap-x-1.5 justify-center items-center px-6 py-2 cursor-pointer rounded-xl">
                    <span class="text-lg font-medium">{{ __('messages.t_post_first_ad') }}</span>
                    <x-icon-arrow-right />
                </a>
            </div>
        </div>
    </div>
    @endif
    <div class="bg-white pt-14 pb-8 dark:bg-gray-950 border-t border-gray-200 classic:border-black dark:border-white/10">
        <div class="container mx-auto px-4 ">
            <div class="grid grid-cols-3 gap-8">
                <div class="space-y-8">
                    <a href="/">
                        <x-brand />
                    </a>
                    <p class="leading-6 ">
                        {{ $generalSettings->site_description }}
                    </p>

                    <x-social-media />

                </div>
                <div class="grid grid-cols-3 gap-8 col-span-2 ">
                    <div class="">
                        <h3 class="font-semibold leading-6 text-lg dark:text-white">
                            {{ __('messages.t_popular_categories') }}
                        </h3>
                        <ul role="list" class="mt-6 space-y-4">
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
                        <ul role="list" class="mt-6 space-y-4">
                            @if(optional($aboutUsPage)->status === 'visible')
                                <li>
                                    <a href="/pages/about-us" class="leading-6  dark:hover:text-white">
                                        About us
                                    </a>
                                </li>
                            @endif

                            @if(app('filament')->hasPlugin('blog') && $blogSettings->enable_blog)
                                <li>
                                    <a href="/blog" class="leading-6  dark:hover:text-white">
                                        Blog
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
                            @foreach($pages as $page)
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
                        <ul role="list" class="mt-6 space-y-4">
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

                </div>
            </div>
            <div class="mt-16 border-t dark:border-white/10 border-gray-200 pt-8 classic:border-black">
                <p class="text-center">
                    © {{ now()->year }} {{ $generalSettings->site_name }}. {{ __('messages.t_all_rights_reserved') }}
                </p>
            </div>
        </div>
    </div>
</footer>
