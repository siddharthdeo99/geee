<div x-data class="pb-14 md:pb-0">

    <livewire:layout.header context="home" />

    <livewire:home.banner  />

    @if($adPlacementSettings->after_header)
        <div class="container mx-auto px-4 flex items-center justify-center md:pt-8 pt-6">
        {!! $adPlacementSettings->after_header !!}
        </div>
    @endif


    @if(!$spotlightAds->isEmpty())
        <section class="py-8" x-on:close-modal.window="location.reload()">
            <div class="container mx-auto px-4">
                <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold">{{ __('messages.t_spotlight_display') }}</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-5  max-md:gap-y-4  sm:gap-x-4">
                    @foreach($spotlightAds as $ad)
                      <livewire:ad.ad-item :$ad wire:key="ad-{{$ad->id}}" lazy />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(!$categories->isEmpty())
    <section class="py-8">
        <div class="container mx-auto px-4">
            <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold ">{{ __('messages.t_explore_by_categories') }}</h2>
            <div class="grid sm:grid-cols-3 md:grid-cols-2 grid-cols-2 gap-4 md:gap-8">
                @foreach($categories as $category)
                  <livewire:home.category-card :$category wire:key="category-{{ $category->id }}" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(!$freshAds->isEmpty())
    <section class="py-8 pb-20">
        <div class="container mx-auto px-4">
                <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold">{{ __('messages.t_fresh_recommend') }}</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-5  gap-y-4  sm:gap-x-4 ">
                    @foreach($freshAds as $ad)
                       <livewire:ad.ad-item :$ad wire:key="fresh-ad-{{$ad->id}}" lazy />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

     @if($adPlacementSettings->before_footer)
        <div class="container mx-auto px-4 flex items-center justify-center md:pb-10 pb-8">
        {!! $adPlacementSettings->before_footer !!}
        </div>
     @endif

    <livewire:layout.sidebar  />

    <livewire:layout.footer />

    <a href="/post-ad" class="bg-gray-900 dark:bg-primary-600 dark:text-black  text-white py-2 px-4 rounded-full fixed bottom-20 right-4 md:hidden flex items-center justify-center gap-x-1" wire:navigate>
      <span> <x-heroicon-o-plus class="w-4 h-4" /></span> {{ __('messages.t_post_your_ad') }}
    </a>



    <livewire:layout.bottom-navigation />



</div>
