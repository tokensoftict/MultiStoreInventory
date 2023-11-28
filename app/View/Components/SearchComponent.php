<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchComponent extends Component
{
    public $placeholder;
    public $s;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
        $this->s = request()->get('s', '');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.search-component');
    }
}
