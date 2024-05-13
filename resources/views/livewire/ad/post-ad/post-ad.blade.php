<div>
    <x-slot:title>
        Post Your Ad on {{ $generalSettings->site_name }} - Reach Thousands of Buyers
    </x-slot:title>

    <x-slot:description>
       List your items quickly and effortlessly. Post your ad on {{ $generalSettings->site_name }} today and connect with thousands of potential buyers.
    </x-slot:description>

    <livewire:ad.post-ad.header  :$id  :$current :title="$this->getTitle()"  :stepIndex="$this->getCurrentStepIndex()" :isLastStep="$this->isLastStep()"  />

    <div class="md:w-3/4 lg:w-1/2 mx-auto px-4 pt-6 pb-20 md:pb-6">
       <livewire:dynamic-component  :key="$current" :$ad :$id  :component="$current" :$promotionIds  />
       @if($current === 'ad.post-ad.ad-detail')
            <livewire:ad.post-ad.dynamic-field :$id />
        @endif
    </div>
</div>
