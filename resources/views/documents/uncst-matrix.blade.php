<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3 font-weight-bold">Requirements Matrix</h5>
        </div>
        <div id="pdf-container" class="mt-3"></div>
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Include PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

    <style>
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

            const renderPDF = async (url) => {
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

                        await page.render({ canvasContext: context, viewport }).promise;
                        pdfContainer.appendChild(canvas);
                    }
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    // Optional: Display error message to user
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