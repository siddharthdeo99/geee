<?php

namespace App\Livewire\Layout;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class Header extends Component
{
    public $isSearch = false;
    public $context = '';
    public $isMobileHidden = false;
    public $sidbarOpen = false;

    #[Reactive]
    public $locationSlug;

    /**
     * Render the header view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.header');
    }
}
