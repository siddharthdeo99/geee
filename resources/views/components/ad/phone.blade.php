@props(['phoneNumber'])

<div @click="revealed = true" class="flex items-center bg-gray-50 mt-4 cursor-pointer justify-between rounded-xl w-full py-1 px-2 border border-gray-200 dark:border-white/20 classic:border-black" x-data="{ revealed: false }">
    <!-- Phone Icon -->
    <div class="p-2 flex gap-x-2 items-center">
         <x-heroicon-o-phone class="w-5 h-5" />
          <!-- Phone Number Display -->
        <div class="font-medium">
            <template x-if="!revealed">
                <span>{{ substr($phoneNumber, 0, -4) . 'XXXX' }}</span>
            </template>
            <template x-if="revealed">
                <span>{{ $phoneNumber }}</span>
            </template>
        </div>
    </div>

    <!-- Reveal Button -->
    <button @click="revealed = true" class="px-3 py-1 text-sm text-primary-600 underline" x-show="!revealed">
        {{ __('messages.t_reveal') }}
    </button>
</div>
