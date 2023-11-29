<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Ramsey\Collection\Collection;

class PurchaseOrderListComponent extends Component
{
    public $purchaseorders;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($purchaseorders)
    {
        $this->purchaseorders = $purchaseorders;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.purchase-order-list-component');
    }
}
