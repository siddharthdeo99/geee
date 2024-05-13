<?php

namespace App\Livewire\Layout;

use App\Models\Category;
use App\Models\Page;
use Livewire\Component;

/**
 * Sidebar Component.
 *
 * Represents the sidebar functionality for mobile view. Displays popular categories and selected pages.
 */
class Sidebar extends Component
{
    // Popular categories to display in the sidebar.
    public $popularCategories;

    // List of pages excluding some predefined pages.
    public $pages;

    // Individual pages to be displayed.
    public $aboutUsPage;
    public $careersPage;
    public $termsPage;
    public $privacyPage;

    /**
     * Mount the component.
     *
     * Fetches the required popular categories and individual pages for the sidebar.
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
     * Render the sidebar view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
