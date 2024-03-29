<?php

namespace App\View\Components\form;

use Illuminate\View\Component;

class InputRich extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $id;

    public $label;

    public $name;

    public $value;

    public $disabled;

    public function __construct($id, $name, $value = null, $label = null, $disabled = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.input-rich');
    }
}
