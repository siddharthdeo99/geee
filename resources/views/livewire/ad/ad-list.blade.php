
<div  >
    <livewire:layout.header isSearch :$locationSlug />

    @if($adPlacementSettings->after_header)
        <div class="container mx-auto px-4 flex items-center justify-center md:pt-8 pt-6">
        {!! $adPlacementSettings->after_header !!}
        </div>
    @endif



    <div class="container mx-auto px-4">
    <div class="mt-6">
        <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
    </div>
     <div class="grid grid-cols-12 gap-6 py-10 pt-6 items-start">
        <div x-on:show-filter.window="show = !show" x-on:close-filter.window="show = false" x-data="{ show: false }" :class="{ 'block fixed left-0 right-0 top-0 bottom-0 z-10': show, 'hidden': !show }" class="col-span-3 md:block" x-cloak>
            <livewire:ad.ad-filter :$filters :$categorySlug :$subcategorySlug  />
        </div>


        <div class="col-span-12 md:col-span-9">
            @if(isset($filters['search']) && $filters['search'])
               <h1 class="text-xl md:text-2xl mb-4 ">{{ __('messages.t_results_for') }} <span class="font-semibold">{{ $filters['search'] }}</span> </h1>
            @endif
            @if($ads->isEmpty())
                <x-not-found description="{{ __('messages.t_no_ads_for_filter') }}" />
            @else
            <div class="grid grid-col-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($ads as $ad)
                        <livewire:ad.ad-item  :ad="$ad" wire:key="search-list-ad-{{$ad->id}}" lazy />
                @endforeach
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between my-10">
                <div>
                    <p class="text-base text-slate-700 leading-5 dark:text-slate-400">
                        <span>{{ __('messages.t_showing_results') }}</span>
                        <span class="font-medium">{{ ( ($ads->currentPage() - 1) * $ads->perPage() ) + 1 }}</span>
                        <span>{{ __('messages.t_to') }}</span>
                        <span class="font-medium">{{ min( $ads->currentPage() * $ads->perPage(),  $ads->count()  ) }}</span>
                        <span>{{ __('messages.t_of') }}</span>
                        <span class="font-medium">{{ $ads->count() }}</span>
                        <span>{{ __('messages.t_results_count') }}</span>

                    </p>
                </div>
                <div>
                    {{ $ads->links() }}
                </div>
            </div>

            @endif

        </div>
     </div>
    </div>
     @if($adPlacementSettings->before_footer)
        <div class="container mx-auto px-4 flex items-center justify-center md:pb-10 pb-10">
        {!! $adPlacementSettings->before_footer !!}
        </div>
    @endif

    <livewire:layout.sidebar  />

    <livewire:layout.footer />


    <livewire:layout.bottom-navigation />

</div>
