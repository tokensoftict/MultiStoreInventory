<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DepositListComponent extends Component
{
    public $lists;
    public $showTotal;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($lists, $showTotal = true)
    {
        $this->lists = $lists;
        $this->showTotal = $showTotal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.deposit-list-component');
    }
}
