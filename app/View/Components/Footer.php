<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Footer extends Component
{
    public $currentSong;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($currentSong)
    {
        $this->currentSong = $currentSong;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.footer');
    }
}
