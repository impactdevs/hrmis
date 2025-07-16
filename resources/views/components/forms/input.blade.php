@props(['name', 'label' => '', 'type' => 'text', 'value' => '', 'id' => null])
<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>

    <input type="{{ $type }}"
        class="form-control shadow-none @isset($max) score-input-component @endisset @error($name) is-invalid @enderror"
        id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}" @if ($isDisabled) disabled @endif
        @isset($min) min="{{ $min }}" @endisset
        @isset($max) max="{{ $max }}" @endisset>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
