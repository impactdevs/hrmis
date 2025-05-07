<?php
namespace App\View\Components\Forms;

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
        public bool $isDisabled = false,
        public ?int $min = null,  // Optional min
        public ?int $max = null   // Optional max

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
