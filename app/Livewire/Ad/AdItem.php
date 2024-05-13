<?php

namespace App\Livewire\Ad;

use App\Models\AdPromotion;
use App\Models\FavouriteAd;
use App\Models\Promotion;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class AdItem extends Component
{
    // Properties
    public $ad;
    #[Reactive]
    public $ref = "/";
    public $isFavourited = false;
    public $isFeatured = false;
    public $isUrgent = false;

    /**
     * Mount the component with the given ad.
     *
     * @param mixed $ad The ad to display.
     */
    public function mount($ad)
    {
        $this->ad = $ad;
        $this->isFavourited = FavouriteAd::where('user_id', Auth::id())
                                        ->where('ad_id', $ad->id)
                                        ->exists();
        $this->checkPromotions();
    }

    /**
     * Check if the ad has any promotions applied.
     */
    protected function checkPromotions()
    {
        $currentDate = now();

        // Check if the ad is featured
        $this->isFeatured = $this->isPromotionActive(1);

        // Check if the ad is urgent
        $this->isUrgent = $this->isPromotionActive(3);
    }

    /**
     * Check if a given promotion is active for the ad.
     *
     * @param int $promotionId The ID of the promotion to check.
     * @return bool Whether the promotion is active or not.
     */
    protected function isPromotionActive(int $promotionId): bool
    {
        $currentDate = now();
        return AdPromotion::where('ad_id', $this->ad->id)
                          ->where('promotion_id', $promotionId)
                          ->where('start_date', '<=', $currentDate)
                          ->where('end_date', '>=', $currentDate)
                          ->exists();
    }

    /**
     * Add the ad to the user's favourites or remove it if it's already a favourite.
     */
    public function addToFavourites()
    {
        // Ensure the user is authenticated before adding to favourites.
        if (!Auth::check()) {
            // If not logged in, redirect to login page or show a message.
            Notification::make()
            ->title(__('messages.t_login_to_add_favorites'))
            ->success()
            ->send();
            return redirect(route('login'));
        }

        // Toggle the favourite status for the ad.
        if ($this->isFavourited) {
            FavouriteAd::where('user_id', Auth::id())
                       ->where('ad_id', $this->ad->id)
                       ->delete();
            $this->isFavourited = false;
        } else {
            FavouriteAd::create([
                'user_id' => Auth::id(),
                'ad_id' => $this->ad->id,
            ]);
            $this->isFavourited = true;
        }
    }

    /**
     * Render a placeholder for the component.
     *
     * @param array $params Parameters for the placeholder.
     * @return \Illuminate\Contracts\View\View
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.ad-skeleton', $params);
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad.ad-item');
    }
}
