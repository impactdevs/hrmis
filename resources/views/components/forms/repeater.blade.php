<div id="repeater-container-{{ $name }}" class="repeater-container">
    <h5 class="mb-4 text-primary font-weight-bold">{{ $label }}</h5>

    @php
        $qualifications = $values ?? [];
        if (empty($qualifications)) {
            $qualifications[] = ['title' => '', 'proof' => ''];
        }
    @endphp

    @foreach ($qualifications as $index => $qualification)
        <div class="item-group card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted">#{{ $index + 1 }}</h6>
                    <button type="button" class="btn btn-link text-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <input type="text" name="{{ $name }}[{{ $index }}][title]" placeholder="Enter title"
                    class="form-control mb-3"
                    value="{{ old($name . '.' . $index . '.title', $qualification['title'] ?? '') }}">

                <div class="file-upload-wrapper">
                    <div class="drop-zone" data-target="proof-{{ $name }}-{{ $index }}"
                        ondragover="event.preventDefault()"
                        ondrop="handleDrop(event, 'proof-{{ $name }}-{{ $index }}')"
                        ondragenter="this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')">
                        <div class="preview-content">
                            @if (!empty($qualification['proof']))
                                @if (strpos($qualification['proof'], '.pdf') !== false)
                                    <div class="pdf-preview">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        <span class="d-block text-muted small mt-2">
                                            {{ basename($qualification['proof']) }}
                                        </span>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $qualification['proof']) }}" alt="Current document"
                                        class="img-thumbnail">
                                @endif
                            @else
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                <div class="mt-2">Drag & drop or click to upload</div>
                                <div class="text-muted small">Supports: PDF, JPG, PNG, GIF</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-text">
                        All files must be in either of the formats PDF, JPG,PNG(Max 5MB each)
                    </div>
                    <input type="file" name="{{ $name }}[{{ $index }}][proof]"
                        id="proof-{{ $name }}-{{ $index }}" class="d-none"
                        accept=".pdf,.jpg,.jpeg,.png,.gif">

                    <div class="file-error text-danger small mt-2"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-item-{{ $name }}" class="btn btn-primary mt-3">
    <i class="fas fa-plus-circle mr-2"></i>Add {{ $label }}
</button>




@push('scripts')
    <style>
        .repeater-container {
            border-radius: 8px;
            padding: 1.5rem;
            background: #f8f9fa;
        }

        .item-group {
            transition: all 0.3s ease;
            background: white;
        }

        .item-group:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drop-zone:hover {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }

        .drop-zone.dragover {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .preview-content img {
            max-width: 200px;
            max-height: 150px;
            object-fit: contain;
            border-radius: 4px;
        }

        .pdf-preview {
            line-height: 1;
            text-align: center;
        }

        .remove-item {
            transition: all 0.3s ease;
            padding: 4px 8px;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-error {
            height: 20px;
            font-size: 0.85rem;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('repeater-container-{{ $name }}');
            const addButton = document.getElementById('add-item-{{ $name }}');
            let itemIndex = {{ count($qualifications) }};

            // Add new item
            addButton.addEventListener('click', function() {
                const newItem = createItemGroup(itemIndex);
                container.insertAdjacentHTML('beforeend', newItem);
                itemIndex++;
            });

            document.addEventListener('click', function(e) {
                const zone = e.target.closest('.drop-zone');
                if (!zone) return;

                // figure out which input to open
                const inputId = zone.getAttribute('data-target');
                const input = document.getElementById(inputId);
                if (input) input.click();
            });


            // Remove item
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    const itemGroup = e.target.closest('.item-group');
                    if (itemGroup) {
                        itemGroup.style.opacity = '0';
                        itemGroup.style.transform = 'translateX(-100px)';
                        setTimeout(() => itemGroup.remove(), 300);
                    }
                }
            });

            // File input handler
            document.addEventListener('change', function(e) {
                if (e.target.matches('input[type="file"]')) {
                    handleFileSelect(e.target);
                }
            });
        });

        function handleDrop(e, targetId) {
            e.preventDefault();
            const input = document.getElementById(targetId);
            input.files = e.dataTransfer.files;
            handleFileSelect(input);
            e.currentTarget.classList.remove('dragover');
        }

        function handleFileSelect(input) {
            const file = input.files[0];
            const wrapper = input.closest('.file-upload-wrapper');
            const preview = wrapper.querySelector('.preview-content');
            const errorDiv = wrapper.querySelector('.file-error');

            if (!file) return;

            // Validate file type
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                errorDiv.textContent = 'Invalid file type. Please upload a PDF, JPG, PNG, or GIF.';
                input.value = '';
                return;
            }
            errorDiv.textContent = '';

            // Show preview
            if (file.type === 'application/pdf') {
                preview.innerHTML = `
                <div class="pdf-preview">
                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                    <div class="mt-2">${file.name}</div>
                </div>
            `;
            } else {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail">`;
                };
                reader.readAsDataURL(file);
            }
        }

        function createItemGroup(index) {
            return `
        <div class="item-group card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title text-muted">#${index + 1}</h6>
                    <button type="button" class="btn btn-link text-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <input type="text" 
                       name="{{ $name }}[${index}][title]" 
                       placeholder="Enter title" 
                       class="form-control mb-3">

                <div class="file-upload-wrapper">
                    <div class="drop-zone" 
                         data-target="proof-{{ $name }}-${index}"
                         ondragover="event.preventDefault()"
                         ondrop="handleDrop(event, 'proof-{{ $name }}-${index}')"
                         ondragenter="this.classList.add('dragover')"
                         ondragleave="this.classList.remove('dragover')">
                        <div class="preview-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                            <div class="mt-2">Drag & drop or click to upload</div>
                            <div class="text-muted small">Supports: PDF, JPG, PNG, GIF</div>
                        </div>
                    </div>
                    <input type="file" 
                           name="{{ $name }}[${index}][proof]" 
                           id="proof-{{ $name }}-${index}" 
                           class="d-none"
                           accept=".pdf,.jpg,.jpeg,.png,.gif">
                    <div class="file-error text-danger small mt-2"></div>
                </div>
            </div>
        </div>`;
        }
    </script>
@endpush
