<?php

namespace App\Livewire\Layout;

use App\Models\Category;
use Livewire\Component;

/**
 * Category Navigation Component.
 *
 * Represents the category navigation functionality. Displays main categories along with their subcategories.
 */
class CategoryNavigation extends Component
{
    // Main categories to display in the navigation.
    public $categories;

    public $locationSlug;

    // Contextual information, if any.
    public $context = '';

    /**
     * Mount the component.
     *
     * Fetches the main categories along with their subcategories for the navigation.
     */
    public function mount()
    {
        $this->locationSlug = request()->route('location');
        $this->categories = Category::with('subcategories')->whereNull('parent_id')->get();
    }

    /**
     * Render the category navigation view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.category-navigation');
    }
}
