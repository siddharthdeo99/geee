<?php

namespace App\Livewire\Home;

use Livewire\Component;

class Banner extends Component
{
    public $search;

    /**
     * Perform a search based on the user's query.
     *
     * This function will trim the search query, check if it's not empty, and then
     * redirect the user to the search results page.
     */
    public function performSearch()
    {
        // Trim white spaces from the search query.
        $this->search = trim($this->search);

        // Check if the search query is not empty.
        if(empty($this->search)) {
            // Optional: Notify the user that the search query cannot be empty.
            // Notification::make()->title('Search query cannot be empty.')->warning()->send();
            return;
        }

        // Construct the search URL with query parameters.
        $searchUrl = url('search') . '?query[sortBy]=date&query[search]=' . urlencode($this->search);

        // Redirect to the constructed URL.
        return redirect()->to($searchUrl);
    }

    /**
     * Render the banner view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.home.banner');
    }
}
