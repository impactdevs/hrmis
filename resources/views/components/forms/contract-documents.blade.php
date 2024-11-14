<div id="repeater-container">
    <h5>{{ $label }}</h5>

    @php
        // Ensure $qualifications is an array to avoid count() errors
        $qualifications = $values ?? [];

        // If qualifications are empty, add a default empty qualification
        if (empty($qualifications)) {
            $qualifications[] = ['title' => '', 'proof' => ''];
        }
    @endphp

    @foreach ($qualifications as $index_contract => $qualification)
        <div class="item-group">
            <input type="text" name="{{ $name }}[{{ $index_contract }}][title]" placeholder="Title"
                class="form-control mb-2"
                value="{{ old($name . '.' . $index_contract . '.title', $qualification['title'] ?? '') }}">

            <label for="contract-proof-{{ $index_contract }}">
                @if (!empty($qualification['proof']))
                    @if (strpos($qualification['proof'], '.pdf') !== false)
                        <div class="pdf-preview mt-3" id="current-proof-contract-{{ $index_contract }}">
                            <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon" class="pdf-icon">
                            <span class="pdf-filename">{{ basename($qualification['proof']) }}</span>
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $qualification['proof']) }}" alt="current image"
                            class="img-fluid mt-3" id="current-proof-contract-{{ $index_contract }}">
                    @endif
                @else
                    <img src="{{ asset('assets/img/upload.png') }}" alt="upload placeholder" height="70px"
                        width="70px" class="img-fluid upload-icon mt-3"
                        id="current-proof-contract-{{ $index_contract }}">
                @endif
            </label>

            <input type="file" name="{{ $name }}[{{ $index_contract }}][proof]"
                id="contract-proof-{{ $index_contract }}" class="form-control-file d-none">

            <button type="button" class="btn btn-danger remove-item mt-2">Remove</button>
        </div>
    @endforeach
</div>
<button type="button" id="add-item" class="btn btn-secondary">Add {{ $label }}</button>

@push('scripts')
    <script>
        $(document).ready(function() {
            let itemIndexContract = {{ count($qualifications) }}; // Start from the next index

            // Function to create a new item group
            function createNewItemGroupContract(index) {
                return `
                <div class="item-group">
                    <input type="text" name="{{ $name }}[${index}][title]" placeholder="Title" class="form-control mb-2">
                    <label for="contract-proof-${index}">
                        <img src="{{ asset('assets/img/upload.png') }}" alt="upload placeholder" height="70px" width="70px" class="img-fluid upload-icon mt-3" id="current-proof-contract-${index}">
                    </label>
                    <input type="file" name="{{ $name }}[${index}][proof]" id="contract-proof-${index}" class="form-control-file d-none">
                    <button type="button" class="btn btn-danger remove-item mt-2">Remove</button>
                </div>
            `;
            }

            // Function to handle file input changes
            function handleFileChangeContract(input) {
                const id = input.attr('id');
                const file = input[0].files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const index = id.split('-')[1]; // Extract the index from the ID
                        if (file.type.includes('image')) {
                            $(`#current-proof-contract-${index}`).attr('src', e.target
                                .result); // Update image preview
                        } else if (file.type === 'application/pdf') {
                            // Replace the image with a PDF icon and filename
                            $(`#current-proof-contract-${index}`).replaceWith(`
                            <div class="pdf-preview mt-3" id="current-proof-contract-${index}">
                                <label for="contract-proof-${index}">
                                    <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon" class="pdf-icon">
                                    <span class="pdf-filename">${file.name}</span>
                                </label>
                            </div>
                        `);
                        }
                    };

                    reader.readAsDataURL(file);
                }
            }

            // Remove item group on button click
            $('#repeater-container').on('click', '.remove-item', function() {
                $(this).closest('.item-group').remove();
            });

            // Add new item group on button click
            $('#add-item').on('click', function() {
                const newItemGroup = createNewItemGroupContract(itemIndexContract);
                $('#repeater-container').append(newItemGroup);
                itemIndexContract++;
            });

            // Delegate the change event to the container, handling future inputs
            $('#repeater-container').on('change', 'input[type="file"]', function() {
                handleFileChangeContract($(this));
            });
        });
    </script>
@endpush
