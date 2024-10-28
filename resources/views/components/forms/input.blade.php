<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>

    <input type="{{ $type }}" class="form-control shadow-none @error($name) is-invalid @enderror"
        id="{{ $id }}" name="{{ $name }}"
        placeholder="{{ $placeholder }}" value="{{ old($name, $value) }}">

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
