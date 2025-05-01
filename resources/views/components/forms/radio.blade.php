<div class="form-group">
    <label>{{ $label }}</label>

    @foreach ($options as $value => $text)
        <div class="form-check">
            <input 
                type="radio" 
                name="{{ $name }}" 
                id="{{ $id . '_' . $value }}" 
                value="{{ $value }}" 
                class="form-check-input @error($name) is-invalid @enderror"
                {{ old($name) == $value ? 'checked' : '' }} required>
            <label class="form-check-label" for="{{ $id . '_' . $value }}">
                {{ $text }}
            </label>
        </div>
    @endforeach

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
