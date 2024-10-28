<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>

    <textarea name="{{ $name }}" id="{{ $id }}" class="form-control @error($name) is-invalid @enderror">
        {{ old($name, $value) }}
    </textarea>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
