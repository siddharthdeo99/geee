<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\Category;
use App\Models\Promotion;
use App\Settings\LocationSettings;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class AdList extends Component
{
    use WithPagination;
    use SEOToolsTrait;

    // Properties
    public $metaTitle;
    public $metaDescription;

    public $categorySlug;
    public $subcategorySlug;

    public $locationSlug;

    public $breadcrumbs = [];


    #[Url(keep: true, as: "query")]
    public $filters = [
        'sortBy' => 'date',
    ];

    /**
     * Mount the component with given category and optionally subcategory.
     *
     * @param  string  $category      The category slug for filtering.
     * @param  string|null  $subcategory  The subcategory slug for filtering (optional).
     */
    public function mount($category, $subcategory = null, $location = null)
    {
        $this->categorySlug = $category;
        $this->subcategorySlug = $subcategory;
        $this->locationSlug = $location;
        $this->buildBreadcrumbs();
        $this->setSeoData();
    }

    /**
     * Update the component based on the filters provided.
     *
     * @param array $filters The set of filters to apply.
     */
    #[On('filter-updated')]
    public function applyFilters($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
        $this->resetPage(); // Reset pagination after filtering.
    }

    /**
     * Retrieve a list of advertisements based on applied filters.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator The paginated list of ads.
     */
    public function getAdsProperty()
    {
        // Initiate the query with the active ads.
        $query = Ad::query()->where('status', 'active');

        $latitude = session('latitude', null);
        $longitude = session('longitude', null);
        $search_radius = app(LocationSettings::class)->search_radius;
        $radius =  $search_radius;
        $selectedCountry = session('country', null);
        $selectedState = session('state', null);
        $selectedCity = session('city', null);
        $locationType = session('locationType', null);


        // Define IDs for Featured and Urgent Promotions
        $featuredPromotionId = 1;
        $urgentPromotionId = 3;

        // Define current date
        $currentDate = now();

        // Create a subquery for featured promotions
        $featuredSubQuery = AdPromotion::selectRaw('COUNT(*)')
                                    ->whereColumn('ad_id', 'ads.id')
                                    ->where('promotion_id', $featuredPromotionId)
                                    ->where('start_date', '<=', $currentDate)
                                    ->where('end_date', '>=', $currentDate);

        // Create a subquery for urgent promotions
        $urgentSubQuery = AdPromotion::selectRaw('COUNT(*)')
                                    ->whereColumn('ad_id', 'ads.id')
                                    ->where('promotion_id', $urgentPromotionId)
                                    ->where('start_date', '<=', $currentDate)
                                    ->where('end_date', '>=', $currentDate);

        // Add select statements for the subqueries and order by them
        $query->addSelect([
            'featured' => $featuredSubQuery,
            'urgent' => $urgentSubQuery,
        ])->orderByDesc('featured')->orderByDesc('urgent');

        if ($this->categorySlug) {
            $category = Category::where('slug', $this->categorySlug)->first();

            if ($category) {
                $this->metaTitle = "Ads in {$category->name} - AdFox";
                $this->metaDescription = "Browse the best ads in {$category->name}. Discover amazing deals and offers at AdFox.";

                if (!$this->subcategorySlug) {
                    // Check if the selected category is a parent
                    $childCategories = Category::where('parent_id', $category->id)->pluck('id')->toArray();

                    if (count($childCategories) > 0) {
                        // If it's a parent category, fetch ads from all its child categories
                        $query->whereIn('category_id', $childCategories);
                    } else {
                        // Otherwise, just fetch ads from the selected category
                        $query->where('category_id', $category->id);
                    }
                } else {
                    // If there's a subcategorySlug provided, use it for filtering
                    $subcategory = Category::where('slug', $this->subcategorySlug)->first();
                    if ($subcategory) {
                        $query->where('category_id', $subcategory->id);
                    }
                }
            }
        }


        if ($locationType === 'country' && $selectedCountry) {
            $query->where('country', $selectedCountry);
        } elseif ($locationType === 'state' && $selectedState) {
            $query->where('state', $selectedState);
        } elseif ($locationType === 'city' && $selectedCity) {
            $query->where('city', $selectedCity);
        } elseif ($locationType === 'area' && $latitude && $longitude) {
            $query->selectRaw("ads.*, (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude)
                - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance', 'ASC');
        }

        if (isset($this->filters['minPrice']) && $this->filters['minPrice']) {
            $query->where('price', '>=', $this->filters['minPrice']);
        }

        if (isset($this->filters['maxPrice']) && $this->filters['maxPrice']) {
            $query->where('price', '<=', $this->filters['maxPrice']);
        }

        // Add string-based search logic
        if (isset($this->filters['search']) && $this->filters['search']) {
            $searchQuery = $this->filters['search'];

            // Define which columns to search in
            $query->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%')  // Search in 'title' column
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('tags', 'like', '%' . $searchQuery . '%');
            });
        }

        // Sorting logic based on 'sortBy' filter
        if (isset($this->filters['sortBy'])) {
            switch ($this->filters['sortBy']) {
                case 'date':
                    $query->orderBy('created_at', 'desc'); // For the newest ads first
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc'); // For price from Low to High
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc'); // For price from High to Low
                    break;
            }
        }

        return $query->simplePaginate(25);
    }

    /**
     * Refresh the list of ads when the location is updated.
     */
    #[On('location-updated')]
    public function onLocationUpdated()
    {
        $this->getAdsProperty();
    }

    /**
     * Redirect to location category route when the location is updated.
     */
    #[On('location-redirect')]
    public function onLocationRedirect($locationSlug)
    {
        $slug = slugify($locationSlug);

        $url = "/location/{$slug}";

        $url .= '/' . $this->categorySlug;

        // Append subcategory if available
        if ($this->subcategorySlug) {
            $url .= '/' . $this->subcategorySlug;
        }
        // Perform the redirection
        return redirect($url);
    }


    /**
     * Builds the breadcrumb trail based on the category and subcategory.
     */
    private function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = 'Home';

        $mainCategory = null;
        $subCategory = null;

        // Add the main category breadcrumb
        if ($this->categorySlug) {
            $mainCategory = Category::where('slug', $this->categorySlug)->first();
            if ($mainCategory) {
                $this->breadcrumbs['/categories/' . $mainCategory->slug] = $mainCategory->name;
            }
        }

        // If a subcategory slug is provided, add the subcategory breadcrumb
        if ($this->subcategorySlug) {
            $subCategory = Category::where('slug', $this->subcategorySlug)->first();
            if ($subCategory) {
                $this->breadcrumbs['/categories/' . $mainCategory->slug . '/' . $subCategory->slug] = $subCategory->name;
            }
        }

        // First, if a search filter is set, add a breadcrumb for search results
        if (isset($this->filters['search']) && $this->filters['search']) {
            $this->breadcrumbs[] = 'Search results for "' . $this->filters['search'] . '"';
        }
        // Then, check if category or subcategory is defined, and add breadcrumb for ad listings
        elseif ($mainCategory || $subCategory) {
            $this->breadcrumbs[] = 'Ad Listings in ' . ($subCategory ?? $mainCategory)->name;
        }
    }


    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad.ad-list', ['ads' => $this->ads]);
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

        // Default values
        $title = $siteName;
        $description = $seoSettings->meta_description ?? 'AdFox';;
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg')); // Default OG image

        // Fetching main category
        $mainCategory = null;
        if ($this->categorySlug) {
            $mainCategory = Category::where('slug', $this->categorySlug)->first();
            if ($mainCategory) {
                $ogImage = $mainCategory->icon;
            }
        }

        // Fetching subcategory and setting SEO data
        if ($this->subcategorySlug) {
            $subCategory = Category::where('slug', $this->subcategorySlug)->first();
            if ($subCategory) {
                $title = $subCategory->name . " $separator " . $siteName;
                if($subCategory->description) {
                    $description = $subCategory->description;
                }
            }
        } elseif ($mainCategory) {
            // Set SEO data for main category if no subcategory is defined
            $title = $mainCategory->name . " $separator " . $siteName;
            if($mainCategory->description) {
                $description = $mainCategory->description;
            }
        }

        // If there's a search
        if (isset($this->filters['search']) && $this->filters['search']) {
            $title = "Search results for " . $this->filters['search'] . " $separator " . $siteName;
        }

        // Setting the SEO data
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
