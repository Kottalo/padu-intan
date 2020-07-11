<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ConfirmModal extends Component
{
    public $id;
    public $title;
    public $function;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $title, $function)
    {
        $this->id = $id;
        $this->title = $title;
        $this->function = $function;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.confirm-modal');
    }
}
