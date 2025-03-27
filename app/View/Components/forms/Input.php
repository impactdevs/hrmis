<?php
namespace app\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $label,
        public string $id,
        public string $placeholder = '', // Default value provided here
        public string $value = '', // Default value provided here

    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input');
    }
}
