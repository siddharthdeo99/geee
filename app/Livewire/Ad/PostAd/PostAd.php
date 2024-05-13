<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\OrderPackageItem;
use App\Models\UserAdPosting;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\PackageSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;

class PostAd extends Component
{
    use SEOToolsTrait;

    #[Url]
    public $current = 'ad.post-ad.ad-detail';
    public Ad $ad;

    #[Url(keep: true)]
    public $id = '';

    // Regular properties
    public $promotionIds = [];

    public $steps = [
        'ad.post-ad.ad-detail',
        'ad.post-ad.visualize-ad',
        'ad.post-ad.locate-ad',
    ];

   /**
     * Mount the component and set the properties if an ad ID is provided.
     * If an ad ID is provided, it is checked for validity and ownership by the current user.
     * If the ad is not found or does not belong to the current user, a 404 error is triggered.
    */
    public function mount()
    {
        if (!empty($this->id)) {
            $ad = Ad::find($this->id);
            if($ad->status->value == 'draft' && app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
                $this->checkAdLimit();
            }
            $userId = auth()->id();
            if (!$ad || $ad->user_id != $userId) {
                abort(404, 'Not found');
            }
        } else {
            if(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
                $this->checkAdLimit();
            }
        }

        // Check if current page is 'ad.post-ad.payment-ad' and 'promotionIds' is empty
        if ($this->current === 'ad.post-ad.payment-ad' && empty($this->promotionIds)) {
            $routeParameters = [
                'id' => $this->id,
                'current' => 'ad.post-ad.promote-ad',
            ];

            return redirect()->route('post-ad', $routeParameters);
        }

        $this->setSeoData();


    }

    protected function checkAdLimit()
    {
        $userId = auth()->id();

        // Retrieve or create the UserAdPosting record for the current user
        $userAdPosting = UserAdPosting::firstOrCreate(
            ['user_id' => $userId],
            ['period_start' => Carbon::now()]
        );

        $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;
        $now = Carbon::now();

        // Determine the next period start date based on the renewal period
        $nextPeriodStart = $renewalPeriod === 'year' ?
                        $userAdPosting->period_start->addYear() :
                        $userAdPosting->period_start->addMonth();

        // Reset the ad count if the current date is greater or equal to the next period start date
        if ($now->greaterThanOrEqualTo($nextPeriodStart)) {
            $userAdPosting->ad_count = 0;
            $userAdPosting->free_ad_count = 0;
            $userAdPosting->period_start = $now;
            $userAdPosting->save();
        }

        // Free ad limit from settings
        $freeAdLimit = app(PackageSettings::class)->free_ad_limit;

        // Calculate the total available limit from active package items
        $activeAdLimit = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
                                    $query->where('user_id', $userId);
                                })
                                ->where('type', 'ad_count')
                                ->whereDate('expiry_date', '>=', now())
                                ->sum('available');

        // Total ad limit is the sum of free limit and the current period's ad count from UserAdPosting
        $totalAdLimit = $freeAdLimit + $activeAdLimit;

        // Check if the user's ad count for the current period is within the total limit
        if ($userAdPosting->ad_count >= $totalAdLimit) {
            return redirect()->route('ad-limit-reached');
        }

        return null;
    }


    /**
     * Toggle the selected promotions.
     */
    #[On('promotion-selected')]
    public function togglePromotion($selectedPromotions)
    {
        $this->promotionIds = array_keys($selectedPromotions);
    }

    /**
     * Move to the next step in the process.
     */
    #[On('next-step')]
    public function next()
    {
        $currentIndex = array_search($this->current, $this->steps);

        if ($currentIndex !== false && isset($this->steps[$currentIndex + 1])) {
            $this->current = $this->steps[$currentIndex + 1];
        }
    }

    /**
     * Move to the previous step or redirect to home if at the first step.
     */
    public function back()
    {
        $currentIndex = array_search($this->current, $this->steps);

        if ($currentIndex === 0 || $this->current == 'ad.post-ad.promote-ad') {
            // If it's the first step, redirect to home
            return redirect(route($this->current == 'ad.post-ad.promote-ad' ? 'my-ads':'home'));
        }

        if ($currentIndex !== false && isset($this->steps[$currentIndex - 1])) {
            $this->current = $this->steps[$currentIndex - 1];
        } else {
            // If it's the payment confirm
            $this->current = 'ad.post-ad.promote-ad';
        }
    }

    /**
     * Get the title based on the current step.
     *
     * @return string
     */
    public function getTitle()
    {
        switch ($this->current) {
            case 'ad.post-ad.ad-detail':
                return __('messages.t_ad_details');
            case 'ad.post-ad.visualize-ad':
                return __('messages.t_visualize_ad');
            case 'ad.post-ad.locate-ad':
                return __('messages.t_locate_ad');
            case 'ad.post-ad.promote-ad':
                return __('messages.t_promote_ad');
            case 'ad.post-ad.payment-ad':
                return __('messages.t_payment_ad');
            default:
                return '';
        }
    }

    /**
     * Update the Ad ID.
     *
     * @param int $id
     */
    #[On('ad-created')]
    public function updateAdId($id)
    {
        $this->id = $id;
    }


     /**
     * Update the Current.
     *
     * @param string $current
     */
    #[On('current-step')]
    public function updateCurrentStep($current)
    {
        $this->current = $current;
    }

    /**
     * Check if the current step is the last step.
     *
     * @return bool
     */
    public function isLastStep()
    {
        return $this->current === end($this->steps) || $this->current == 'ad.post-ad.promote-ad' ;
    }

    /**
     * Get the index of the current step.
     *
     * @return int
     */
    public function getCurrentStepIndex()
    {
        return array_search($this->current, $this->steps);
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

        $title = __('messages.t_seo_post_ad_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.ad.post-ad.post-ad');
    }
}
