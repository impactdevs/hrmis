<?php
namespace app\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputRepeater extends Component
{
    public string $name;
    public string $label;

    public array $values = [];

    public function __construct(string $name = 'items', string $label = 'Items', array $values = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->values = $values;
    }


    public function render(): View|Closure|string
    {
        return view('components.forms.input-repeater', [
            'name' => $this->name,
            'label' => $this->label,
        ]);
    }
}
