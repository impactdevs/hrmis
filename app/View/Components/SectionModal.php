<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Form;

class SectionModal extends Component
{

    public Form $form;

    //form mode(edit/add)
    public string $mode;

    /**
     * Create a new component instance.
     */
    public function __construct(
        Form $form,
        string $mode,
    ) {
        $this->form = $form;
        $this->mode = $mode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.section-modal');
    }
}
