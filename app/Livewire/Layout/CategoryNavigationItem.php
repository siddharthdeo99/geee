<?php

namespace App\Livewire\Layout;


use Livewire\Component;

/**
 * CategoryNavigationItem Component.
 * Handles the display of a single category item in a navigation bar.
 */
class CategoryNavigationItem extends Component
{
    // Represents the Category model instance.
    public $category;
    public $locationSlug;

    /**
     * Renders a placeholder for the category navigation item during lazy loading.
     *
     * @param array $params Additional parameters to pass to the view.
     * @return \Illuminate\Contracts\View\View The placeholder view.
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.category-nav-skeleton', $params);
    }

    /**
     * Renders the category navigation item view.
     * Checks if a category is set before rendering it.
     *
     * @return \Illuminate\Contracts\View\View The category navigation item view.
     */
    public function render()
    {
        if (!$this->category) {
            return $this->placeholder();
        }
        return view('livewire.layout.category-navigation-item');
    }
}
