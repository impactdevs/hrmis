<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between align-items-center ms-3 me-3">
            <h5 class="font-weight-bold mb-0">Requirements Matrix</h5>
            <div class="d-flex gap-2">
                <a href="{{ asset('assets/documents/uncst-matrix.pdf') }}" target="_blank" rel="noopener"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                </a>
                <a href="{{ asset('assets/documents/uncst-matrix.pdf') }}" download
                    class="btn btn-primary btn-sm">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
        </div>
        <div id="pdf-viewer" class="mt-3 ms-3 me-3">
            <div id="pdf-container"></div>
        </div>
        <div id="pdf-error" class="alert alert-warning ms-3 me-3" style="display: none;">
            The preview couldn't be loaded (this can happen if your network blocks the PDF viewer script).
            You can still <a id="pdf-error-link" href="{{ asset('assets/documents/uncst-matrix.pdf') }}" target="_blank" rel="noopener">open</a>
            or <a href="{{ asset('assets/documents/uncst-matrix.pdf') }}" download>download</a> the PDF directly.
        </div>
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Include PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

    <style>
        /*
         * The sitewide layout locks scrolling to <body> (html{overflow:hidden}).
         * That works for normal-length pages, but this preview can be several
         * PDF pages tall, so it gets its own bounded, independently
         * scrollable region instead of relying on that outer scroll chain.
         */
        #pdf-viewer {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #f8fafc;
        }

        #pdf-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #pdf-container canvas {
            max-width: 100%;
            height: auto;
        }

        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border-left-color: #09f;
            animation: spin 1s ease infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const pdfUrl = "{{ asset('assets/documents/uncst-matrix.pdf') }}";
            const pdfContainer = document.getElementById("pdf-container");
            const spinnerContainer = document.querySelector('.spinner-container');
            const errorContainer = document.getElementById('pdf-error');

            const showError = () => {
                errorContainer.style.display = 'block';
            };

            const renderPDF = async (url) => {
                if (typeof pdfjsLib === 'undefined') {
                    showError();
                    return;
                }

                try {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

                    const pdf = await pdfjsLib.getDocument(url).promise;
                    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                        const page = await pdf.getPage(pageNum);
                        const scale = 1.5;
                        const viewport = page.getViewport({ scale });

                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.className = 'shadow-sm mb-3';

                        await page.render({ canvasContext: context, viewport }).promise;
                        pdfContainer.appendChild(canvas);
                    }
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    showError();
                } finally {
                    spinnerContainer.style.display = 'none';
                }
            };

            // Initial spinner show
            spinnerContainer.style.display = 'flex';
            renderPDF(pdfUrl);
        });
    </script>
</x-app-layout>