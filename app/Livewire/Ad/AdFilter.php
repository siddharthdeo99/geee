<?php

namespace App\Livewire\Ad;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Route;


class AdFilter extends Component
{
    // Properties
    public $minPrice;
    public $maxPrice;
    public $sortBy = 'date';
    public $categorySlug;
    public $subcategorySlug = null;
    public $mainCategories = [];
    public $subCategories = [];
    public $selectedCategory;
    public $isMainCategory = true;
    public $locationSlug;

    /**
     * Mount the component with the given filters.
     *
     * @param array $filters Filters to apply.
     */
    public function mount($filters)
    {
        $this->locationSlug = request()->route('location');
        $this->minPrice = $filters['minPrice'] ?? null;
        $this->maxPrice = $filters['maxPrice'] ?? null;
        $this->sortBy = $filters['sortBy'] ?? 'date';

        // Load main categories
        $this->loadMainCategories();

        // Check and load sub-categories if required
        $this->loadSubCategories();
    }

    /**
     * Load the main categories.
     */
    protected function loadMainCategories()
    {
        $this->mainCategories = Category::whereNull('parent_id')->get();
    }

    /**
     * Check and load sub-categories based on selected category.
     */
    protected function loadSubCategories()
    {
        $this->selectedCategory = Category::where('slug', $this->categorySlug)->first();

        if ($this->selectedCategory) {
            if ($this->subcategorySlug) {
                $selectedSubCategory = Category::where('slug', $this->subcategorySlug)->first();
                $this->isMainCategory = false;
                if ($selectedSubCategory && $selectedSubCategory->parent_id === $this->selectedCategory->id) {
                    $this->subCategories = Category::where('parent_id', $this->selectedCategory->id)->get();
                }
            } else {
                $this->subCategories = Category::where('parent_id', $this->selectedCategory->id)->get();
                $this->isMainCategory = false;
            }
        }
    }

    /**
     * Listen for property updates and dispatch filter events accordingly.
     *
     * @param string $name Name of the property that was updated.
     */
    public function updated($name)
    {
        $allowedUpdates = ['minPrice', 'maxPrice', 'sortBy'];
        if (in_array($name, $allowedUpdates)) {
            $data = array_filter([
                'minPrice' => $this->minPrice,
                'maxPrice' => $this->maxPrice,
                'sortBy' => $this->sortBy,
            ]);
            $this->dispatch('filter-updated', $data);
        }
    }
    /**
     * Select the main category and redirect while keeping the same query parameters.
     *
     * @param string $categorySlug The slug of the main category to select.
     */
    public function selectMainCategory($categorySlug)
    {
        return redirect()->route('ad-category', [
            'category' => $categorySlug
        ] + request()->query());
    }

    /**
     * Select the sub-category and redirect while keeping the same query parameters.
     *
     * @param string $subcategorySlug The slug of the sub-category to select.
     */
    public function selectSubCategory($categorySlug, $subcategorySlug)
    {
        return redirect()->to(route('ad-category', ['category' => $categorySlug, 'subcategory' => $subcategorySlug]) . '?' . http_build_query(request()->query()));

    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.ad.ad-filter');
    }
}
