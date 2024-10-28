<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>

    <select name="{{ $name }}" id="{{ $id }}" class="form-control @error($name) is-invalid @enderror">
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" {{ old($name) == $value ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
