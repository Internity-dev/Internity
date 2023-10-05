<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $modalId;
    public $modalTitle;
    public $modalLable;

    public function __construct($modalId, $modalTitle, $modalLable)
    {
        $this->modalId = $modalId;
        $this->modalTitle = $modalTitle;
        $this->modalLable = $modalLable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
