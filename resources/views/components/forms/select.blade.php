@props(['name', 'label' => '', 'options' => [], 'selected' => '', 'id' => null])

@php
    $id = $id ?? $name;
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'form-select']) }}>
        <option value="">-- Select --</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" {{ (old($name, $selected) == $value) ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
