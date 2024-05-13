@props(['isFavourited'])
<div @click.prevent="$wire.addToFavourites" class="cursor-pointer">
    @if($isFavourited)
     <x-icon-solid-heart-circle class="md:w-12 md:h-12" />
    @else
     <x-icon-heart class="md:w-11 md:h-11 dark:text-gray-400" />
    @endif
</div>
