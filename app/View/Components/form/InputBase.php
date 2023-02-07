<?php

namespace App\View\Components\form;

use Illuminate\View\Component;

class InputBase extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $type; 

    public $id;

    public $label;

    public $name;

    public function __construct($type, $id, $label, $name)
    {
        $this->type = $type;
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.input-base');
    }
}