<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Settings\AdSettings;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;

class SuccessAd extends Component
{
    use SEOToolsTrait;

    public $id;
    public $ad;

    /**
     * Mount the component and process the Ad and its promotions.
     *
     * @param int $id The Ad ID.
     */
    public function mount($id)
    {
        $this->id = $id;

        $this->initializeAd();
        $this->setSeoData();
    }

    /**
     * Initialize the Ad and ensure it belongs to the authenticated user.
     */
    protected function initializeAd()
    {
        $this->ad = Ad::find($this->id);
        if (!$this->ad || $this->ad->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        } else {
            $this->updateAdDetails();
        }
    }

    /**
    * Set SEO data
    */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);


        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? 'AdFox';

        $title = __('messages.t_seo_success_ad') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Update the Ad details based on settings.
     */
    protected function updateAdDetails()
    {
        $adSettings = app(AdSettings::class);
        $adModeration = $adSettings->ad_moderation;
        $adDuration = $adSettings->ad_duration;

        $this->ad->update([
            'status' =>  $adModeration ? 'pending' : 'active',
            'posted_date' => Carbon::now(),
            'expires_at' => now()->addDays($adDuration)
        ]);
    }


    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.success-ad');
    }
}
