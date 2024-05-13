
<a href="{{ route('ad-details', ['slug' => $ad->slug, 'ref' => $ref]) }}"  class="w-full shadow-sm flex-none bg-white rounded-xl hover:shadow-md hover:border-black transition-all hover:transform hover:-translate-x-1 hover:-translate-y-1 dark:bg-gray-900 classic:border-black classic:border classic:hover:shadow-custom relative flex md:flex-col md:pb-0 pb-[3.2rem]" >
    <div class="p-3  md:pb-0 relative  md:w-auto md:h-auto flex-none">
        <div class="absolute top-4 right-4 hidden md:block">
            <x-ad.favourite-ad :$isFavourited  />
        </div>

        @if($isUrgent)
            <div class="px-2 py-1 whitespace-nowrap text-xs md:text-sm font-medium border classic:border-black border-transparent  dark:border-white/10 absolute top-3 rounded-t-xl md:rounded-none md:top-6 left-3 right-3 md:right-auto md:left-6 bg-red-600 text-black">
                Urgent Ad
            </div>
        @endif

        @if($isFeatured)
            <div class="px-2 py-1 whitespace-nowrap text-xs md:text-sm font-medium border classic:border-black border-transparent  dark:border-white/10 absolute bottom-3 rounded-b-xl md:rounded-none md:bottom-4 md:right-auto left-3 right-3 md:left-6 bg-yellow-400 text-black">
                Featured Ad
            </div>
        @endif

       <img src="{{ $ad->primaryImage ?? asset('/images/placeholder.jpg') }}" alt="Ad Title" class="aspect-square	object-cover w-20 h-20 md:flex md:w-full md:h-[12rem] rounded-xl" >
    </div>

    <div class="flex-grow flex flex-col">
        <div class="flex-grow md:border-b border-gray-200  dark:border-white/10 classic:border-black ">
            <div class="px-2 md:px-3 py-3 h-full flex flex-col">
                <h3 class="mb-1 flex-grow line-clamp-3 font-medium">{{ $ad->title }}</h3>
                <div class="flex items-center mb-2 text-sm mt-3">
                    <x-icon-pin-location class="w-5 h-5 dark:text-gray-500"/>
                    <span class="font-light ml-1">{{ $ad->location_name }}</span>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center px-3 py-2 md:py-3 md:border-none border-t md:static absolute left-0 right-0 bottom-0 border-gray-200  dark:border-white/10 classic:border-black">
            <div class="flex items-center gap-x-2">
                <x-price
                   value="{{ config('app.currency_symbol') }}{{ number_format($ad->price, 0) }}"
                   type_id="{{ $ad->price_type_id }}"
                />
                <div class="md:hidden"><x-ad.favourite-ad :$isFavourited  /></div>
            </div>
            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $ad->created_at->format('M j') }}</span>
        </div>
    </div>
</a>
