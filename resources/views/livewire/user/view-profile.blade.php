<div>
    <livewire:layout.header isMobileHidden />
    <x-page-header title="View Profile" isMobileHidden :$referrer />


    <div class="container mx-auto px-4 pt-6 pb-10">
        <div class="mb-6">
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>
        <div class="md:grid md:grid-cols-3  gap-x-8">
            <div class="md:col-span-1 mb-10 md:mb-0">
                <livewire:user.seller-info :userId="$user->id"  />
            </div>
            <div class="col-span-2">
                <h3 class="mb-4 text-lg">{{ $ads->count() }} Listings </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($ads as $ad)
                         <livewire:ad.ad-item :$ad wire:key="list-{{$ad->id}}" :ref="'/view-profile/' . auth()->id()" lazy   />
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <livewire:layout.bottom-navigation />
    
</div>
