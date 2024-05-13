<?php

namespace App\Livewire\Home;

use App\Models\Category;
use Livewire\Component;
use App\Models\Promotion;
use App\Models\Ad;
use App\Settings\LocationSettings;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class Home extends Component
{
    use SEOToolsTrait;

    public $categories;
    public $spotlightAds;
    public $freshAds;

   /**
     * Mount the component and fetch initial data.
     */
    public function mount()
    {
        $this->categories = $this->fetchMainCategories();
        $this->loadSpotlightAds();
        $this->loadFreshAds();
        $this->setSeoData();
    }

    /**
     * Fetch the main categories.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchMainCategories()
    {
        return Category::with('subcategories')->whereNull('parent_id')->get();
    }

     /**
     * Load spotlight ads based on the user's location and session data.
     */
    public function loadSpotlightAds()
    {
        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius;
        $radius =  $search_radius;
        $selectedCountry = session('country', null);
        $selectedState = session('state', null);
        $selectedCity = session('city', null);
        $locationType = session('locationType', null);

        $spotlightPromotion = Promotion::find(2);

        // Null check here
        if (!$spotlightPromotion) {
            $this->spotlightAds = collect();  // initialize as an empty collection
            return;
        }

        $spotlightPromotionId = $spotlightPromotion->id;

        // Fetch the current index from the cache. Default to 0 if not found.
        $currentIndex = Cache::get('spotlight_ad_index', 0);

        $spotlightAdsQuery = Ad::where('status', 'active')->whereHas('promotions', function($query) use ($spotlightPromotionId) {
            $query->where('promotion_id', $spotlightPromotionId)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        });


        if ($locationType === 'country' && $selectedCountry) {
            $spotlightAdsQuery->where('country', $selectedCountry);
        } elseif ($locationType === 'state' && $selectedState) {
            $spotlightAdsQuery->where('state', $selectedState);
        } elseif ($locationType === 'city' && $selectedCity) {
            $spotlightAdsQuery->where('city', $selectedCity);
        } elseif ($locationType === 'area' && $latitude && $longitude) {
            $spotlightAdsQuery = $spotlightAdsQuery->selectRaw("*, (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude)
                - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance', 'ASC');
        }

        // Count the total spotlight ads
        $totalSpotlightAds = $spotlightAdsQuery->count();

        // Fetch the next 5 ads starting from the current index
        $this->spotlightAds = $spotlightAdsQuery->skip($currentIndex)->take(5)->get();

        // If we fetched fewer than 5, get the remaining from the beginning
        if ($this->spotlightAds->count() < 5 && $currentIndex != 0) {
            $remainingCount = 5 - $this->spotlightAds->count();
            $remainingAds = $spotlightAdsQuery->take($remainingCount)->get();
            $this->spotlightAds = $this->spotlightAds->concat($remainingAds);
        }

        // Update the cache with the new index for the next display. Loop back to the beginning if needed.
        $newIndex = ($totalSpotlightAds > 0) ? ($currentIndex + $this->spotlightAds->count()) % $totalSpotlightAds : 0;


        Cache::put('spotlight_ad_index', $newIndex, now()->addDays(1)); // Store for 1 day. Adjust as needed.
    }


     /**
     * Handle the 'location-updated' event and reload the spotlight ads.
     */
    #[On('location-updated')]
    public function onLocationUpdated()
    {
        Cache::forget('spotlight_ad_index');
        $this->loadSpotlightAds();
        sleep(1);
        $this->js('location.reload()');
    }

    /**
     * Load the freshest ads based on the user's location and session data.
     */
    public function loadFreshAds()
    {
        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius;
        $radius =  $search_radius;
        $selectedCountry = session('country', null);
        $selectedState = session('state', null);
        $selectedCity = session('city', null);
        $locationType = session('locationType', null);

        $freshAdsQuery = Ad::where('status', 'active')
                            ->orderBy('posted_date', 'desc');

        if ($locationType === 'country' && $selectedCountry) {
            $freshAdsQuery->where('country', $selectedCountry);
        } elseif ($locationType === 'state' && $selectedState) {
            $freshAdsQuery->where('state', $selectedState);
        } elseif ($locationType === 'city' && $selectedCity) {
            $freshAdsQuery->where('city', $selectedCity);
        } elseif ($locationType === 'area' && $latitude && $longitude) {
            $freshAdsQuery = $freshAdsQuery->selectRaw("*, (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude)
                - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance', 'ASC');
        }

        $this->freshAds = $freshAdsQuery->take(10)->get();
    }


    /**
     * Render the home view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.home.home');
    }

    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $siteName = $generalSettings->site_name ?? 'AdFox';
        $siteTagline = $generalSettings->site_tagline ?? '';

        $title = $seoSettings->meta_title ?? $siteName  . ": " . $siteTagline;
        $description = $seoSettings->meta_description ?? 'AdFox';
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg'));
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        $this->seo()->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage);
        $this->seo()->twitter()->setUrl(url()->current());
        $this->seo()->twitter()->setSite("@" . $seoSettings->twitter_username);
        $this->seo()->twitter()->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property');
        $this->seo()->metatags()->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property');
        $this->seo()->metatags()->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title);
        $this->seo()->jsonLd()->setDescription($description);
        $this->seo()->jsonLd()->setUrl(url()->current());
        $this->seo()->jsonLd()->setType('WebSite');
    }
}
