<div>
    <livewire:layout.header isMobileHidden />
    <x-page-header title="{{ __('messages.t_my_favourites') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    <div class="container mx-auto px-4 py-10">
        @if($ads->isEmpty())
          <x-not-found description="{{ __('messages.t_no_favourite_ads') }}" />
        @else
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach($ads as $ad)
                    <livewire:ad.ad-item :$ad wire:key="$ad->id" ref="\my-favorites" lazy  />
                @endforeach
            </div>
        @endif
    </div>
</div>
