<?php
namespace app\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContractDocuments extends Component
{
    public string $name;
    public string $label;

    public array $values = [];

    public string $filetype;

    public function __construct(string $name = 'items', string $label = 'Items', array $values = [], $filetype = 'image')
    {
        $this->name = $name;
        $this->label = $label;
        $this->values = $values;
        $this->filetype = $filetype;
    }


    public function render(): View|Closure|string
    {
        return view('components.forms.repeater', [
            'name' => $this->name,
            'label' => $this->label,
        ]);
    }
}
