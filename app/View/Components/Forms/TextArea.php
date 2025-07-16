<?php
namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class TextArea extends Component
{
    public string $id;
    public function __construct(
        public string $name,
        public string $label,
        string $id = null,
        public ?string $value = null, // Optional value for pre-filling
        public bool $isDisabled = false,
        public bool $isDraft = false
    ) {
        $this->id = $id ?? $name;
       }

    public function render(): View|Closure|string
    {
        return view('components.forms.text-area');
    }
}

