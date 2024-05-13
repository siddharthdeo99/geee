<div class="border border-gray-200 dark:border-white/20 rounded classic:border-black {{ $extraClass }}">
    <!-- Seller Profile -->
    <div class="px-6 pt-6 pb-10 text-sm md:text-base">
        <div class="flex items-center justify-between gap-x-2">
            <div class="text-lg font-semibold flex items-center gap-x-1">
                <x-user.list-item :user="$user" />
                @if($user->verified)
                    <x-filament::icon-button
                        icon="heroicon-s-check-badge"
                        tooltip="{{ __('messages.t_user_verified_tooltip') }}"
                        size="lg"
                        color="success"
                    />
                @endif
            </div>
            <div wire:click="toggleFollow" class="{{ $this->isFollowing() ? 'bg-gray-900' : '' }} px-3 py-1 rounded-full border classic:border-black cursor-pointer flex items-center">
                @if($this->isFollowing())
                    <x-heroicon-s-user class="w-6 h-6 text-white" />
                    <x-heroicon-s-check class="w-5 h-5 -ml-1 text-white" />
                @else
                    <x-heroicon-o-user class="w-6 h-6" />
                    <x-heroicon-o-plus-small class="w-5 h-5 -ml-1" />
                @endif
            </div>
        </div>
        @if($ad)
            <div class="flex items-center mt-4 gap-x-2">
                @if($ad->for_sale_by == 'owner')
                <x-heroicon-o-user class="w-6 h-6" />
                @else
                <x-heroicon-o-briefcase class="w-6 h-6" />
                @endif
                <div>
                <span class="text-sm md:text-base capitalize">{{ $ad->for_sale_by }}</span>
                </div>
            </div>
        @endif
        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-users class="w-6 h-6" />
            <div class="flex gap-x-2">
                <div class="cursor-pointer" wire:click="showFollowersModal">
                    <span class="font-semibold">{{ $this->followersCount }}</span> {{ __('messages.t_followers') }}
                </div>
                <div class="text-muted"> | </div>
                <div class="cursor-pointer" wire:click="showFollowingModal">
                    <span class="font-semibold">{{ $this->followingCount }}</span> {{ __('messages.t_following') }}
                </div>
            </div>
        </div>


        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
            <span class="text-sm md:text-base">{{ $user->ads->count() }} {{ __('messages.t_listings') }}</span>
        </div>
        <!-- Member Since -->
        <div class="flex items-center mt-4 gap-x-2">
            <x-heroicon-o-calendar-days class="w-6 h-6" />
            <span class="text-sm md:text-base">{{ __('messages.t_member_since') }} {{ $user->created_at->format('F Y') }}</span>
        </div>

        <!-- Email Verified -->
        @if($user->email_verified_at)
            <div class="flex items-center mt-4 gap-x-2">
                <x-heroicon-o-envelope class="w-6 h-6" />
                <span class="text-sm md:text-base">{{ __('messages.t_email_verified') }}</span>
            </div>
        @endif

        @if($ad && $ad->website_url && $isWebsite)
            <x-ad.website websiteURL="{{ $ad->website_url }}" />
        @endif

        @if($ad && $ad->display_phone && auth()->check())
            <x-ad.phone phoneNumber="{{ $ad->phone_number }}" />
        @endif

    </div>
    {{-- Modals (Followers || Following) --}}
    <x-filament::modal id="follow-modal" width="xl">
        <x-slot name="heading">{{ $showFollowers ?  __('messages.t_followers') : __('messages.t_following') }}</x-slot>
        <div class=" space-y-4">
            @if($showFollowers)
                @foreach($followersList as $follower)
                   <div wire:key="follower-{{ $follower->id }}">
                      <x-user.list-item :user="$follower" />
                   </div>
                @endforeach
            @else
                @foreach($followingList as $following)
                <div wire:key="following-{{ $following->id }}" >
                    <x-user.list-item :user="$following" />
                 </div>
                @endforeach
            @endif
        </div>
    </x-filament::modal>

</div>
