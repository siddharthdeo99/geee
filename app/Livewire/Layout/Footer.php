<?php

namespace App\Livewire\Layout;

use App\Models\Category;
use App\Models\Page;
use Livewire\Component;

/**
 * Footer Component.
 *
 * Represents the footer section of the application. It displays popular categories,
 * essential pages like 'About Us', 'Careers', etc., and other relevant links.
 */
class Footer extends Component
{
    // Popular categories to be displayed in the footer.
    public $popularCategories;

    // Other relevant pages to be displayed in the footer.
    public $pages;

    // Essential pages for the application.
    public $aboutUsPage;
    public $careersPage;
    public $termsPage;
    public $privacyPage;

    /**
     * Mount the component.
     *
     * Initializes the data required for the footer by fetching the popular categories
     * and essential pages like 'About Us', 'Careers', etc.
     */
    public function mount()
    {
        $this->popularCategories = Category::getPopularCategories();

        $this->aboutUsPage = Page::where('slug', 'about-us')->first();
        $this->careersPage = Page::where('slug', 'careers')->first();
        $this->termsPage = Page::where('slug', 'terms-conditions')->first();
        $this->privacyPage = Page::where('slug', 'privacy-policy')->first();

        $excludeSlugs = ['careers', 'about-us', 'terms-conditions', 'privacy-policy'];
        $this->pages = Page::where('status', 'visible')
                           ->whereNotIn('slug', $excludeSlugs)
                           ->get(['title', 'slug']);
    }

    /**
     * Render the footer view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.footer');
    }
}
