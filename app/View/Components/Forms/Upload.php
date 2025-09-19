<?php
namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Upload extends Component
{
    public string $label;
    public string $name;
    public string $id;
    public string $form_text_id;
    public string $value;
    public string $description;
    public string $accept;

    public string $filetype;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $label,
        string $name,
        string $id,
        string $form_text_id = '',
        string $value = '',
        string $description = 'The file should be in PNG or JPG format.',
        string $filetype = 'image',
        string $accept = '.png,.jpg'
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->id = $id;
        $this->form_text_id = $form_text_id;
        $this->value = $value;
        $this->description = $description;
        $this->filetype = $filetype;
        $this->accept = $accept;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.upload');
    }
}
