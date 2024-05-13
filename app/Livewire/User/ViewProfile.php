<?php

namespace App\Livewire\User;

use App\Models\Ad;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

/**
 * ViewProfile Component.
 * Displays the profile of a specific user along with their associated ads.
 */
class ViewProfile extends Component
{
    use SEOToolsTrait;

    // Represents the ads associated with the user.
    public $ads;

    public $breadcrumbs = [];

    // Represents the User model instance.
    public $user;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Mount lifecycle hook.
     * Fetches the user details and their ads based on the provided id.
     *
     * @param int $id The ID of the user whose profile is to be displayed.
     */
    public function mount($id)
    {
        $this->fetchUser($id);
        $this->fetchAdsForUser($id);
        $this->setSeoData();
        $this->buildBreadcrumbs();
    }

    /**
     * Fetches the user details based on the provided id.
     *
     * @param int $id The ID of the user.
     */
    protected function fetchUser($id)
    {
        $this->user = User::findOrFail($id);
    }

    /**
     * Fetches the ads associated with the provided id.
     *
     * @param int $id The ID of the user.
     */
    protected function fetchAdsForUser($id)
    {
        if ($this->user) {
            $this->ads = Ad::whereIn('status', ['active', 'sold'])
               ->where('user_id', $id)
               ->get();
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

        $title = $this->user->name . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Builds the breadcrumb trail for the profile page.
     */
    private function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = 'Home';

        // Add the profile breadcrumb
        if ($this->user) {
            $this->breadcrumbs['/profile/' . $this->user->slug . '/' . $this->user->id ] = $this->user->name . "'s Profile";
        }
    }


    /**
     * Renders the ViewProfile view.
     */
    public function render()
    {
        return view('livewire.user.view-profile');
    }
}
