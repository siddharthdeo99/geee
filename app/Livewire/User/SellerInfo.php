<?php

namespace App\Livewire\User;

use App\Models\Page;
use App\Models\User;
use App\Models\Ad;
use Filament\Notifications\Notification;
use Livewire\Component;
use Parsedown;

class SellerInfo extends Component
{
    public $user;
    public $ad;
    public $followersList = [];
    public $followingList = [];
    public $showFollowers = false;
    public $extraClass = '';
    public $isWebsite;

    /**
     * Mount lifecycle hook.
     */
    public function mount(Ad $ad, $userId = null)
    {
        $this->user = $userId ? User::find($userId) : $ad->user;
    }

    public function showFollowersModal()
    {
        $this->showFollowers = true;
        $this->followersList = $this->user->followers()->get();
        $this->dispatch('open-modal', id: 'follow-modal');
    }

    public function showFollowingModal()
    {
        $this->showFollowers = false;
        $this->followingList = $this->user->following()->get();
        $this->dispatch('open-modal', id: 'follow-modal');
    }

    public function toggleFollow()
    {
        if (auth()->guest()) {
            Notification::make()
                ->title(__('messages.t_login_or_signup_to_follow'))
                ->info()
                ->send();
            return false;
        }

        if ($this->user->id == auth()->id()) {
            Notification::make()
            ->title(__('messages.t_cannot_follow_own_profile'))
            ->info()
            ->send();
            return;
        }

        if ($this->user->followers()->where('follower_id', auth()->id())->exists()) {
            // User is already following, so unfollow
            $this->user->followers()->detach(auth()->id());
        } else {
            // User is not following, so follow
            $this->user->followers()->attach(auth()->id());
        }

    }

    public function getFollowersCountProperty()
    {
        return $this->user->followers()->count();
    }

    public function getFollowingCountProperty()
    {
        return $this->user->following()->count();
    }

    public function isFollowing()
    {
        return auth()->check() && $this->user->followers()->where('follower_id', auth()->id())->exists();
    }

    /**
     * Renders the PageDetail view.
     */
    public function render()
    {
        return view('livewire.user.seller-info');
    }

}
