<?php
namespace App\View\Components;

use Illuminate\View\Component;

class CheckboxList extends Component
{
    public $options;
    public $name;
    public $title;
    public $selectedValues;

    public function __construct($options, $name, $title, $selectedValues = [])
    {
        $this->options = $options;
        $this->name = $name;
        $this->title = $title;
        $this->selectedValues = $selectedValues;
    }

    public function render()
    {
        return view('components.checkbox-list');
    }
}
