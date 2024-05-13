<div class="text-center p-4">
    <div class="hidden md:block">
        <h3 class="text-xl mb-4 font-semibold">{{ __('messages.t_contact_seller_action') }}</h3>
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_is_item_available_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_is_item_available_query') }}</x-button.secondary>
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_meetup_availability_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_meetup_availability_query') }}</x-button.secondary>
        <x-button.secondary wire:click="chatWithSeller('{{ __('messages.t_price_negotiation_query') }}')" size="lg" class="w-full mb-4">{{ __('messages.t_price_negotiation_query') }}</x-button.secondary>
    </div>
    <x-button wire:click="chatWithSeller()" size="lg" class="w-full md:mb-4 bg-[#90EE90] border-black text-black">{{ __('messages.t_chat_with_seller_action') }}</x-button>
</div>
