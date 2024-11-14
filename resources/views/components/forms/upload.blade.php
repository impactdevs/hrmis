<div class="form-group upload mt-2">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="upload-container text-center">
        @if (old($name, $value) && $filetype == 'image')
            <label for="{{ $id }}">
                <img src="{{ asset('storage/' . old($name, $value)) }}" alt="current image" class="img-fluid mt-3"
                    id="current-{{ $id }}">
            </label>
        @elseif (old($name, $value) && $filetype == 'pdf')
            <label for="{{ $id }}">
                <div class="pdf-preview mt-3">
                    <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon" class="pdf-icon">
                    <span class="pdf-filename">{{ basename(old($name, $value)) }}</span>
                </div>
            </label>
        @else
            <label for="{{ $id }}">
                <img src="{{ asset('assets/img/upload.png') }}" alt="upload placeholder" height="70px" width="70px"
                    class="img-fluid upload-icon mt-3">
            </label>
        @endif
        <input type="file" name="{{ $name }}" id="{{ $id }}" class="form-control-file d-none" accept=".png, .jpg">
        {{-- show validation errors --}}
        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div id="{{ $form_text_id }}" class="form-text text-muted">
        {{ $description }}
    </div>
</div>

