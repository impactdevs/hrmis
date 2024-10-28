<div>
    <p>{{ $title }}</p>
    <div class="row">
        @foreach ($options as $value => $label)
            <div class="col-3">
                <input type="checkbox" id="{{ $name . '_' . $value }}" name="{{ $name }}[]"
                    value="{{ $value }}" class="form-check-input {{ $errors->has($name) ? 'is-invalid' : '' }}"
                    @if (in_array($value, $selectedValues)) checked @endif>
                <label for="{{ $name . '_' . $value }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>

    @if ($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif
</div>
