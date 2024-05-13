<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\Promotion;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class PromoteAd extends Component implements HasForms
{
    use InteractsWithForms;

    // Properties
    public $id;
    public $promotions;
    public $website_url;
    public $selectedPromotions = [];

    /**
     * Initialize the component with provided ID and fetch promotions.
     *
     * @param int $id The advertisement ID.
     */
    public function mount($id)
    {
        $this->id = $id;
        $this->promotions = Promotion::all();
        if ($this->id) {
            $this->loadAdDetails($this->id);
        }
    }

      /**
     * Load and set ad details if an ID is provided.
     *
     * @param int $id The ID of the ad to load
     */
    private function loadAdDetails($id)
    {
        $ad = Ad::find($id);
        if ($ad) {
          $this->website_url = $ad->website_url;
        }
    }

    public function updatedWebsiteUrl($value) {
        $ad = Ad::find($this->id);
        if ($ad) {
            $ad->update(['website_url' => $value]);
        }
    }

    public function isActivePromotion($promotionId)
    {
        $adId = $this->id;
        $activePromotion = AdPromotion::where('ad_id', $adId)
                                      ->where('promotion_id', $promotionId)
                                      ->whereDate('end_date', '>=', now())
                                      ->first();

        if ($activePromotion) {
            return [
                'isActive' => true,
                'start_date' => $activePromotion->start_date,
                'end_date' => $activePromotion->end_date,
            ];
        } else {
            return ['isActive' => false];
        }
    }

    /**
     * Toggle a promotion's selection status.
     *
     * @param int $promotionId The ID of the promotion to toggle.
     */
    public function togglePromotion($promotionId)
    {
        if (isset($this->selectedPromotions[$promotionId])) {
            unset($this->selectedPromotions[$promotionId]);
        } else {
            // Find the existing promotion for the ad
            $this->selectedPromotions[$promotionId] = true;
        }
        $this->dispatch('promotion-selected', $this->selectedPromotions);
    }

    /**
     * Define the form for the website URL input.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('website_url')
                    ->label(__('messages.t_website_url'))
                    ->live(onBlur: true)
                    ->placeholder(__('messages.t_enter_your_business_website'))
                    ->minLength(10)
                    ->required(),
            ]);
    }


    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad.post-ad.promote-ad');
    }
}
