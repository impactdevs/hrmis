<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>

    <textarea 
        {{ $attributes->merge(['class' => 'form-control auto-resize']) }}
        name="{{ $name }}" rows="6" id="{{ $id }}"
        class="form-control @error($name) is-invalid @enderror" @if ($isDraft) style="color: transparent;" @endif
        style="font-family: 'Courier New', Courier, monospace; font-size: 16px; color: #333;"
        @if ($isDisabled) readonly @endif>
        {{ old($name, $value) }}
    </textarea>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
