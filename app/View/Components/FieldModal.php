<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Section;

class FieldModal extends Component
{
    // Form instance
    public String $section;

    //form mode(edit/add)
    public string $mode;

    //optional $fieldType, $filedLabel
    public string $fieldType;

    public string $fieldLabel;

    /**
     * Create a new component instance.
     */
    public function __construct(
        String $section,
        string $mode,
        string $fieldType = '',
        string $fieldLabel = ''
    ) {
        $this->section = $section;
        $this->mode = $mode;
        $this->fieldType = $fieldType;
        $this->fieldLabel = $fieldLabel;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.field-modal');
    }
}
