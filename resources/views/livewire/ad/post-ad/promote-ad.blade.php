<div>
    <p class="mb-6">{{ __('messages.t_boost_ad_visibility') }}</p>

    <div>
        @foreach($promotions as $promotion)
            <div wire:click="togglePromotion({{ $promotion->id }})"
                wire:key="promotion-{{ $promotion->id }}"
                class="bg-white flex justify-between items-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 px-3 py-2 rounded-xl mb-6 transition-all hover:transform hover:-translate-x-1 hover:-translate-y-1 cursor-pointer classic:ring-black classic:hover:shadow-custom"
                x-data="{
                    selectedPromotions: @entangle('selectedPromotions'),
                    isActive: function() {
                        return Object.keys(this.selectedPromotions).includes('{{ $promotion->id }}');
                    }
                }"
                :class="{ 'bg-white classic:shadow-custom': isActive() }">
                @php
                    $promotionStatus = $this->isActivePromotion($promotion->id);
                @endphp
                <div>
                    <div class="flex items-center gap-x-3">
                        <img src="{{ asset('images/' . $promotion->image) }}" alt="{{ $promotion->name }}" class="mx-auto w-10 h-10">
                        <div>
                            <p class="font-semibold">
                               {{ $promotion->name }} - {{ $promotion->duration }} days - {{ config('app.currency_symbol') }}{{ $promotion->price }}
                            </p>
                            <p class="text-sm ">{{ $promotion->description }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    @if($promotionStatus['isActive'])
                        <div class="text-green-800 font-semibold">
                            <span>Active: {{ $promotionStatus['start_date']->format('M d') }} - {{ $promotionStatus['end_date']->format('M d') }}</span>
                        </div>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input wire:click="togglePromotion({{ $promotion->id }})" type="checkbox" value="{{ $promotion->id }}" class="sr-only peer" x-bind:checked="isActive()">
                            <div class="w-11 h-6 bg-gray-200 border  peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-black"></div>
                            <span class="ml-3 text-sm md:text-base  text-gray-900 dark:text-gray-300"></span>
                        </label>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if(array_key_exists(4, $selectedPromotions))
        <form wire:submit>
            <div class="mb-5">
                {{ $this->form }}
            </div>
        </form>
    @endif
</div>
