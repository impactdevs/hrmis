{{-- create an ipuut repeater where i click and get and get another input --}}
<div>
    <label for="{{ $name }}">{{ $label }}</label>

    <div id="{{ $name }}-container">
        @foreach ($values as $value)
            <div class="input-group mb-2">
                <input type="text" name="{{ $name }}[]" value="{{ $value }}" class="form-control"
                    placeholder="Item">
                <button type="button" class="btn btn-danger remove-item">Remove</button>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-primary add-item">Add Item</button>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.add-item').click(function() {
                $('#{{ $name }}-container').append(`
                <div class="input-group mb-2">
                    <input type="text" name="{{ $name }}[]" class="form-control" placeholder="Item">
                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                </div>
            `);
            });

            $('#{{ $name }}-container').on('click', '.remove-item', function() {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
@endpush
