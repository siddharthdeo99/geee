
    <div class="chat-screen md:h-auto flex flex-col md:flex-none">
        @if($conversation)
            <div class="flex items-center border-b border-gray-200 px-4 py-3 classic:border-black dark:border-white/10">
                <img src="{{ $conversation->ad->primaryImage ?? asset('/images/placeholder.jpg') }}"   alt="User Photo" class="w-12 h-12 rounded-xl">
                <div class="ml-3">
                    <h2 class="text-lg">{{ $conversation->ad->title }}</h2>
                    <div class="flex">
                        <x-price
                            value="{{ config('app.currency_symbol') }}{{ number_format($conversation->ad->price, 0) }}"
                            type_id="{{ $conversation->ad->price_type_id }}"
                        />
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-gray-50 rounded-xl-lg md:h-[25rem] min-h-[25rem] relative flex flex-col flex-grow classic:bg-gray-100 dark:bg-gray-800">
        <div wire:poll.10s x-ref="messagesContainer"  class="flex-grow overflow-y-auto p-4 " x-data="{ init() { this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight; } }"  x-init="init">
            @foreach($messages as $message)
                @if($message->sender_id === Auth::id())
                    <div class="mb-2 text-right">
                        <p class="bg-black text-white p-2 rounded inline-block prose prose-slate ">{!! nl2br($message->content) !!}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $message->created_at->format('h:i a') }}</p>
                    </div>
                @else
                    <div class="mb-2">
                        <p class=" bg-primary-400 p-2 rounded   inline-block prose prose-slate ">{!! nl2br($message->content) !!}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $message->created_at->format('h:i a') }}</p>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="bottom-0 left-0 right-0">
            <div x-data="{height: 56, newMessage: @entangle('newMessage'), handleEnter: function(event) {
                if (event.shiftKey) {
                    this.height += 20;
                } else if (this.newMessage.trim() !== '') {
                    $wire.sendMessage();
                    newMessage = '';
                    this.height = 56;
                }
            }}" class="relative md:-m-[0.01rem] shadow-sm send-message">

                <textarea
                    x-model="newMessage"
                    :style="'height: ' + height + 'px'"
                    x-on:keydown.enter="handleEnter($event)"
                    placeholder="Type a message"
                    class="border-none block w-full shadow-inner  dark:text-white text-sm sm:text-base  disabled:bg-slate-100 disabled:cursor-wait ring-1 transition outline-none duration-75 bg-white  dark:bg-gray-900 ring-gray-950/10 focus-within:ring-gray-950/10  dark:ring-white/10 dark:focus-within:ring-primary-500 classic:ring-black md:rounded-br-xl">
                </textarea>

                <div wire:click="sendMessage" x-on:click="height=56"  class="absolute bottom-1.5  right-2 rounded-xl flex items-center p-2 cursor-pointer z-10 " :class="{'text-black bg-primary-600': newMessage.trim() != '', 'text-gray-200 pointer-events-none': newMessage.trim() == ''}" class="">
                    <x-icon-send-email class="w-7 h-7"  />
                </div>
            </div>
        </div>
        </div>
    </div>

