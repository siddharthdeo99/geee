<div x-data="{ isCopied: false, copy() {
        const url = '{{ url()->current() }}';
        var _this = this;
        navigator.clipboard.writeText(url).then(function() {
            _this.isCopied = true;
            setTimeout(() => {
                _this.isCopied = false;
            }, 2000);
        }, function(err) {
            console.error('Failed to copy text: ', err);
        });
    }}">

    <x-page-header title="{{ $ad->title }}" isMobileHidden   />

    <livewire:layout.header isMobileHidden />

    @if($adPlacementSettings->after_header)
        <div class="container mx-auto px-4 flex items-center justify-center md:py-8 py-6">
        {!! $adPlacementSettings->after_header !!}
        </div>
    @endif


    <div class="py-6 border-y border-gray-200 dark:border-white/20 classic:border-black">
        <div class="container mx-auto px-4">

            <div class="flex flex-col md:flex-row justify-between">
                <div class="flex-none md:flex items-center gap-x-2">
                    <div class="flex justify-between mb-4 md:mb-0">
                        <div>
                            <x-price
                                value="{{ config('app.currency_symbol') }}{{ number_format($ad->price, 0) }}"
                                type_id="{{ $ad->price_type_id }}"
                            />
                        </div>
                        <div class="md:hidden">
                            <x-ad.share-report :$isFavourited :$ad  />
                        </div>
                    </div>
                    <h1 class="md:text-2xl text-xl mb-4 md:mb-0 font-semibold hidden md:block">{{ $ad->title }}</h1>
                </div>

                <div class="flex md:flex-col md:items-end ">
                    <div class="md:flex items-center md:gap-x-2 w-full">
                        <x-icon-location class="w-6 h-6 hidden md:block" />
                        <div class="flex md:flex-col justify-between ">
                            <div class="flex"> <x-icon-location class="w-6 h-6 md:hidden" /> {{ $ad->location_name }}</div>
                            <div class="text-gray-600">{{ __('messages.t_posted_on') }} {{ $ad->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto md:px-4 px-2">
        <div class="mt-6">
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>
    </div>

    <div class="md:pb-10 pt-6 pb-32">
        <div class="container mx-auto md:px-4">

            @if($ownerView)
            <div class="flex items-center p-4 mb-8 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
                <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                <div class="ml-3 text-sm font-medium">
                    @if($this->ad->status->value == 'expired')
                        {{ __("Your listing for ':title' has expired as of :date.", ['title' => $this->ad->title, 'date' => $this->ad->expires_at->format('F j, Y')]) }}
                    @elseif($this->ad->status->value == 'inactive')
                        {{ __("Your item ':title' has been deactivated by our administration due to non-compliance with our terms and policies or detection of prohibited content.", ['title' => $this->ad->title]) }}
                    @else
                        {{ __("Your item ':title' has been marked as :status.", ['title' => $this->ad->title, 'status' => $this->ad->status->label()]) }}
                    @endif
                    <a href="{{ route('post-ad') }}" class="font-semibold underline hover:no-underline">Post a new ad</a>.
                </div>
            </div>
           @endif

            <div class="grid grid-cols-7 bg-white md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl classic:ring-black ">
                <div class="md:col-span-5 col-span-7 md:border-r border-gray-200 dark:border-white/20 classic:border-black relative">

                    <x-ad.gallery  :images="$ad->images()" :videoLink="$ad->video_link"  />

                    <div class="absolute top-4 right-4 text-white"><x-ad.favourite-ad :$isFavourited  /></div>
                    @if($isUrgent)
                        <div class="px-2 py-1 text-sm font-medium border border-black absolute {{ $isUrgent && $isFeatured ? 'top-16' : 'top-6' }} left-6 bg-red-600 z-10 text-black">
                            Urgent Ad
                        </div>
                    @endif

                    @if($isFeatured)
                        <div class="px-2 py-1 text-sm font-medium border border-black absolute top-6 left-6 bg-yellow-400 z-10 text-black">
                            Featured Ad
                        </div>
                    @endif

                    <x-ad.description :description="$descriptionHtml" />

                    <div class="space-y-4 px-4 mb-8">
                        @if($ad->condition)
                            <div class="flex items-center">
                                <span class="font-medium text-lg w-1/3">{{ __('messages.t_condition') }}</span>
                                <span class="text-base w-2/3">{{ $ad->condition->name }}</span>
                            </div>
                        @endif

                        @foreach($fieldDetails as $fieldDetail)
                            <div class="flex items-center">
                                <span class="font-medium text-lg w-1/3">{{ $fieldDetail['field_name'] }}:</span>
                                <span class="text-base w-2/3">{{ $fieldDetail['value'] }}</span>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="md:col-span-2 col-span-7 ">
                    <div class="hidden md:block">
                        <x-ad.contact />
                    </div>
                    <div>
                        <livewire:user.seller-info :$isWebsite :$ad extraClass="border-l-0 border-r-0 rounded-none" />
                    </div>
                    <div class="py-6 px-4 hidden md:block">
                        <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_ad_actions') }}</h3>
                        <x-ad.share-report :$isFavourited :$ad  />
                    </div>
                    
                    @if($tags)
                        <div class="py-6 px-4 border-t classic:border-black">
                            <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_tags') }}</h3>
                            <div>
                                @foreach ($tags as $tag)
                                    <a wire:key="tag-{{ $tag['name'] }}" href="{{ $tag['link'] }}" class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">{{ $tag['name'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        @if(!$relatedAds->isEmpty())
        <section class="py-6" x-on:close-modal.window="location.reload()">
            <div class="container mx-auto px-4">
                <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold">{{ __('messages.t_related_ads') }}</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-5  max-md:gap-y-4  sm:gap-x-4">
                    @foreach($relatedAds as $ad)
                       <livewire:ad.ad-item :$ad wire:key="related-{{$ad->id}}" lazy />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    </div>



    <div class="md:hidden fixed bottom-14 left-0 right-0 border-t border-gray-200 bg-white dark:bg-gray-900 dark:border-white/10 classic:border-black">
        <x-ad.contact />
    </div>

     @if($adPlacementSettings->before_footer)
        <div class="container mx-auto px-4 flex items-center justify-center md:pb-8 pb-6">
        {!! $adPlacementSettings->before_footer !!}
        </div>
     @endif

     <livewire:layout.sidebar  />

     <livewire:layout.footer />



    {{-- Modals (Share ad) --}}
    <x-filament::modal id="share-ad" width="xl">

        {{-- Header --}}
        <x-slot name="heading">{{ __('messages.t_share_this_ad') }}</x-slot>

        {{-- Content --}}
        <div>
            <div class="items-center justify-center md:flex md:space-y-0 space-y-4">

                {{-- Facebook --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://www.facebook.com/share.php?u={{ urlencode(url()->current()) }}&t={{ $ad->title }}" target="_blank" class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#3b5998] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M374.244,285.825l14.105,-91.961l-88.233,0l0,-59.677c0,-25.159 12.325,-49.682 51.845,-49.682l40.116,0l0,-78.291c0,0 -36.407,-6.214 -71.213,-6.214c-72.67,0 -120.165,44.042 -120.165,123.775l0,70.089l-80.777,0l0,91.961l80.777,0l0,222.31c16.197,2.541 32.798,3.865 49.709,3.865c16.911,0 33.511,-1.324 49.708,-3.865l0,-222.31l74.128,0Z"/></svg>
                    </a>
                    <span class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_facebook') }}</span>
                </div>

                {{-- Twitter --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://twitter.com/intent/tweet?text={{ $ad->title }}%20-%20{{ urlencode(url()->current()) }}%20" target="_blank" class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#1da1f2] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M161.014,464.013c193.208,0 298.885,-160.071 298.885,-298.885c0,-4.546 0,-9.072 -0.307,-13.578c20.558,-14.871 38.305,-33.282 52.408,-54.374c-19.171,8.495 -39.51,14.065 -60.334,16.527c21.924,-13.124 38.343,-33.782 46.182,-58.102c-20.619,12.235 -43.18,20.859 -66.703,25.498c-19.862,-21.121 -47.602,-33.112 -76.593,-33.112c-57.682,0 -105.145,47.464 -105.145,105.144c0,8.002 0.914,15.979 2.722,23.773c-84.418,-4.231 -163.18,-44.161 -216.494,-109.752c-27.724,47.726 -13.379,109.576 32.522,140.226c-16.715,-0.495 -33.071,-5.005 -47.677,-13.148l0,1.331c0.014,49.814 35.447,93.111 84.275,102.974c-15.464,4.217 -31.693,4.833 -47.431,1.802c13.727,42.685 53.311,72.108 98.14,72.95c-37.19,29.227 -83.157,45.103 -130.458,45.056c-8.358,-0.016 -16.708,-0.522 -25.006,-1.516c48.034,30.825 103.94,47.18 161.014,47.104" style="fill-rule:nonzero;"/></svg>
                    </a>
                    <span class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_twitter') }}</span>
                </div>

                {{-- Linkedin --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ $ad->title }}&summary={{ $ad->title }}" target="_blank" class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#0a66c2] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M473.305,-1.353c20.88,0 37.885,16.533 37.885,36.926l0,438.251c0,20.393 -17.005,36.954 -37.885,36.954l-436.459,0c-20.839,0 -37.773,-16.561 -37.773,-36.954l0,-438.251c0,-20.393 16.934,-36.926 37.773,-36.926l436.459,0Zm-37.829,436.389l0,-134.034c0,-65.822 -14.212,-116.427 -91.12,-116.427c-36.955,0 -61.739,20.263 -71.867,39.476l-1.04,0l0,-33.411l-72.811,0l0,244.396l75.866,0l0,-120.878c0,-31.883 6.031,-62.773 45.554,-62.773c38.981,0 39.468,36.461 39.468,64.802l0,118.849l75.95,0Zm-284.489,-244.396l-76.034,0l0,244.396l76.034,0l0,-244.396Zm-37.997,-121.489c-24.395,0 -44.066,19.735 -44.066,44.047c0,24.318 19.671,44.052 44.066,44.052c24.299,0 44.026,-19.734 44.026,-44.052c0,-24.312 -19.727,-44.047 -44.026,-44.047Z" style="fill-rule:nonzero;"/></svg>
                    </a>
                    <span class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_linkedin') }}</span>
                </div>

                {{-- Whatsapp --}}
                <div class="grid items-center justify-center mx-4">
                    <a href="https://api.whatsapp.com/send?text={{ $ad->title }}%20{{ urlencode(url()->current()) }}" target="_blank" class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#25d366] focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M373.295,307.064c-6.37,-3.188 -37.687,-18.596 -43.526,-20.724c-5.838,-2.126 -10.084,-3.187 -14.331,3.188c-4.246,6.376 -16.454,20.725 -20.17,24.976c-3.715,4.251 -7.431,4.785 -13.8,1.594c-6.37,-3.187 -26.895,-9.913 -51.225,-31.616c-18.935,-16.89 -31.72,-37.749 -35.435,-44.126c-3.716,-6.377 -0.397,-9.824 2.792,-13c2.867,-2.854 6.371,-7.44 9.555,-11.16c3.186,-3.718 4.247,-6.377 6.37,-10.626c2.123,-4.252 1.062,-7.971 -0.532,-11.159c-1.591,-3.188 -14.33,-34.542 -19.638,-47.298c-5.171,-12.419 -10.422,-10.737 -14.332,-10.934c-3.711,-0.184 -7.963,-0.223 -12.208,-0.223c-4.246,0 -11.148,1.594 -16.987,7.969c-5.838,6.377 -22.293,21.789 -22.293,53.14c0,31.355 22.824,61.642 26.009,65.894c3.185,4.252 44.916,68.59 108.816,96.181c15.196,6.564 27.062,10.483 36.312,13.418c15.259,4.849 29.145,4.165 40.121,2.524c12.238,-1.827 37.686,-15.408 42.995,-30.286c5.307,-14.882 5.307,-27.635 3.715,-30.292c-1.592,-2.657 -5.838,-4.251 -12.208,-7.44m-116.224,158.693l-0.086,0c-38.022,-0.015 -75.313,-10.23 -107.845,-29.535l-7.738,-4.592l-80.194,21.037l21.405,-78.19l-5.037,-8.017c-21.211,-33.735 -32.414,-72.726 -32.397,-112.763c0.047,-116.825 95.1,-211.87 211.976,-211.87c56.595,0.019 109.795,22.088 149.801,62.139c40.005,40.05 62.023,93.286 62.001,149.902c-0.048,116.834 -95.1,211.889 -211.886,211.889m180.332,-392.224c-48.131,-48.186 -112.138,-74.735 -180.335,-74.763c-140.514,0 -254.875,114.354 -254.932,254.911c-0.018,44.932 11.72,88.786 34.03,127.448l-36.166,132.102l135.141,-35.45c37.236,20.31 79.159,31.015 121.826,31.029l0.105,0c140.499,0 254.87,-114.366 254.928,-254.925c0.026,-68.117 -26.467,-132.166 -74.597,-180.352" id="WhatsApp-Logo"/></svg>
                    </a>
                    <span class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_whatsapp') }}</span>
                </div>

                {{-- Copy link --}}
                <div class="grid items-center justify-center mx-4">
                    <button type="button" x-on:click="copy" class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-gray-400 focus:outline-none focus:ring-0 mx-auto">
                        <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title/><path d="M17.3,13.35a1,1,0,0,1-.7-.29,1,1,0,0,1,0-1.41l2.12-2.12a2,2,0,0,0,0-2.83L17.3,5.28a2.06,2.06,0,0,0-2.83,0L12.35,7.4A1,1,0,0,1,10.94,6l2.12-2.12a4.1,4.1,0,0,1,5.66,0l1.41,1.41a4,4,0,0,1,0,5.66L18,13.06A1,1,0,0,1,17.3,13.35Z" /><path d="M8.11,21.3a4,4,0,0,1-2.83-1.17L3.87,18.72a4,4,0,0,1,0-5.66L6,10.94A1,1,0,0,1,7.4,12.35L5.28,14.47a2,2,0,0,0,0,2.83L6.7,18.72a2.06,2.06,0,0,0,2.83,0l2.12-2.12A1,1,0,1,1,13.06,18l-2.12,2.12A4,4,0,0,1,8.11,21.3Z" /><path d="M8.82,16.18a1,1,0,0,1-.71-.29,1,1,0,0,1,0-1.42l6.37-6.36a1,1,0,0,1,1.41,0,1,1,0,0,1,0,1.42L9.52,15.89A1,1,0,0,1,8.82,16.18Z" /></svg>
                    </button>
                    <template x-if="!isCopied">
                        <span class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_copy_link') }}</span>
                    </template>
                    <template x-if="isCopied">
                        <span class="uppercase font-normal text-xs text-green-500 mt-4 tracking-widest">{{ __('messages.t_copied') }}</span>
                    </template>
                </div>

            </div>
        </div>

    </x-filament::modal>

     {{-- Modals (Report ad) --}}
     <x-filament::modal id="report-ad" width="xl">
        {{-- Header --}}
        <x-slot name="heading">{{ __('messages.t_report_this_ad') }}</x-slot>
        <div>
            <form wire:submit="reportAd" novalidate>
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit">
                        Report Ad
                    </x-filament::button>
                </div>
            </form>
        </div>
     </x-filament::modal>


     <livewire:layout.bottom-navigation />

</div>
