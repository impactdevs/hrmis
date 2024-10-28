<?php

namespace App\View\Components\forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    public string $label;

    public array $headers;

    public array $values;
    /**
     * Create a new component instance.
     */
    public function __construct(
        array $values = [],
        array $headers = [],
        string $label
    ) {
        $this->label = $label;

        $this->headers = $headers;

        $this->values = $values;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.table');
    }
}
