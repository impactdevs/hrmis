<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whistleblower Reporting Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(120deg, #1a3a5f, #2c5282);
            color: white;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        header::after {
            content: "";
            position: absolute;
            bottom: -70px;
            left: -30px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        header p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .form-container {
            background: white;
            border-radius: 0 0 10px 10px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            position: relative;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eaeef5;
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: #2c5282;
            font-size: 1.3rem;
        }

        .section-title i {
            background: #e3f2fd;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
        }

        .radio-option input[type="radio"] {
            margin-top: 5px;
            margin-right: 12px;
            accent-color: #2c5282;
        }

        .radio-option label {
            font-weight: 500;
            cursor: pointer;
        }

        .other-input {
            margin-top: 10px;
            margin-left: 28px;
            width: 100%;
            max-width: 400px;
            display: none;
        }

        .other-input input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .other-input input:focus {
            outline: none;
            border-color: #2c5282;
            box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
        }

        textarea,
        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafbfc;
        }

        textarea:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #2c5282;
            box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
            background: white;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .radio-row {
            display: flex;
            gap: 25px;
            margin-bottom: 15px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-item input {
            accent-color: #2c5282;
        }

        .confidentiality {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #2c5282;
        }

        .confidentiality p {
            display: flex;
            gap: 15px;
        }

        .confidentiality input {
            margin-top: 5px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .submit-btn {
            background: linear-gradient(120deg, #1a3a5f, #2c5282);
            color: white;
            border: none;
            padding: 15px 45px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(44, 82, 130, 0.25);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(44, 82, 130, 0.35);
        }

        .form-note {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        /* Evidence Upload Section */
        .upload-container {
            margin-top: 15px;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .upload-container:hover {
            border-color: #2c5282;
            background: #edf2f7;
        }

        .upload-icon {
            font-size: 3rem;
            color: #2c5282;
            margin-bottom: 15px;
        }

        .upload-text {
            margin-bottom: 15px;
            color: #4a5568;
        }

        .upload-btn {
            background: #e3f2fd;
            color: #2c5282;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .upload-btn:hover {
            background: #d0e1f5;
        }

        .file-info {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #718096;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .file-info span {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .file-list {
            margin-top: 20px;
            text-align: left;
        }

        .file-item {
            background: #edf2f7;
            padding: 10px 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .file-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-remove {
            color: #e53e3e;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
        }

        .requirements {
            margin-top: 15px;
            padding: 15px;
            background: #fffaf0;
            border-radius: 8px;
            border-left: 4px solid #ecc94b;
        }

        .requirements h3 {
            margin-bottom: 10px;
            color: #975a16;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .requirements ul {
            padding-left: 25px;
        }

        .requirements li {
            margin-bottom: 8px;
        }

        @media (max-width: 600px) {
            .radio-row {
                flex-direction: column;
                gap: 10px;
            }

            header h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 20px;
            }

            .upload-container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
                class="d-block mx-auto object-fit-contain border rounded img-fluid"
                style="max-width: 100%; height: auto;">
            <h1><i class="fas fa-shield-alt"></i> Whistleblower Reporting Form</h1>
            <p>Your report helps us maintain a safe, ethical, and compliant workplace environment. All submissions are
                treated with strict confidentiality.</p>
        </header>

        <div class="form-container">
            <form id="whistleblowerForm" action="{{ route('whistleblowing.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-tag"></i>
                        <h2>Type of Submission</h2>
                    </div>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="workplace" name="submission_type" value="Workplace Concern">
                            <label for="workplace">Workplace Concern</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="ethical" name="submission_type"
                                value="Ethical Misconduct or Violation">
                            <label for="ethical">Ethical Misconduct or Violation</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="harassment" name="submission_type"
                                value="Harassment or Discrimination">
                            <label for="harassment">Harassment or Discrimination</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="safety" name="submission_type" value="Health & Safety Issue">
                            <label for="safety">Health & Safety Issue</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="suggestion" name="submission_type"
                                value="Suggestion for Improvement">
                            <label for="suggestion">Suggestion for Improvement</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="other" name="submission_type" value="Other">
                            <label for="other">Other (Please specify):</label>
                        </div>
                        <div class="other-input" id="otherInput">
                            <input type="text" placeholder="Specify other concern type" name="submission_type_other">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-file-alt"></i>
                        <h2>Description of Concern</h2>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;">Please describe the situation in detail, including what
                        happened, when it occurred, and where it took place:</p>
                    <textarea placeholder="Provide detailed information about the concern..." name="description"></textarea>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-users"></i>
                        <h2>Individuals Involved</h2>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;">Names and roles of the individuals involved:</p>
                    <textarea placeholder="List all individuals involved and their roles..." name="individuals_involved"></textarea>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-clipboard-check"></i>
                        <h2>Evidence or Witnesses</h2>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;">Please list any documents, emails, or witnesses that
                        support your concern:</p>
                    <textarea placeholder="List evidence or witnesses..." name="evidence_details"></textarea>

                    <div class="requirements">
                        <h3><i class="fas fa-info-circle"></i> Evidence Upload Requirements</h3>
                        <ul>
                            <li>Please consolidate all evidence into a <strong>single PDF file</strong></li>
                            <li>Maximum file size: <strong>10 MB</strong></li>
                            <li>Accepted format: <strong>PDF only</strong> (.pdf)</li>
                            <li>Include any relevant screenshots, documents, or correspondence</li>
                            <li>For witness information, include names and contact details</li>
                        </ul>
                    </div>

                    <div class="upload-container">
                        <div class="upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <p class="upload-text">Upload your evidence as a single PDF file</p>
                        <button type="button" class="upload-btn" id="uploadTrigger">
                            <i class="fas fa-cloud-upload-alt"></i> Choose File
                        </button>
                        <input type="file" id="fileInput" accept=".pdf" style="display: none;" name="evidence_file">

                        <div class="file-info">
                            <span><i class="fas fa-info-circle"></i> Max file size: 10 MB</span>
                            <span><i class="fas fa-file-pdf"></i> PDF format only</span>
                        </div>

                        <div class="file-list" id="fileList">
                            <!-- File preview will appear here -->
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-history"></i>
                        <h2>Previous Reporting</h2>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;">Has this issue been reported before?</p>
                    <div class="radio-row">
                        <div class="radio-item">
                            <input type="radio" id="reportedYes" name="reported_before" value="Yes">
                            <label for="reportedYes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="reportedNo" name="reported_before" value="No">
                            <label for="reportedNo">No</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="reportedUnknown" name="reported_before" value="I don't know">
                            <label for="reportedUnknown">I don't know</label>
                        </div>
                    </div>
                    <div id="reportedDetails" style="display: none;">
                        <p style="margin: 15px 0 10px; color: #555;">If yes, please provide details (e.g., to whom,
                            when, and outcome):</p>
                        <textarea placeholder="Provide details of previous reporting..." name="reported_details"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-lightbulb"></i>
                        <h2>Suggested Resolution</h2>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;">Suggested Resolution or Action (if any):</p>
                    <textarea placeholder="Your suggestions for resolving this issue..." name="suggested_resolution"></textarea>
                </div>

                <div class="confidentiality">
                    <p>
                        <input type="checkbox" id="confirmation" name="confirmation" value="1" required>
                        <label for="confirmation">I understand that this report will be treated with confidentiality to
                            the extent possible, but the company may be required to disclose information during an
                            investigation or as required by law.</label>
                    </p>
                </div>

                <div class="btn-container">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </button>
                </div>

                <div class="form-note">
                    <p><i class="fas fa-lock"></i> This form is secured and encrypted. Your identity will be protected
                        to the fullest extent possible by law.</p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show/hide other input field
        document.getElementById('other').addEventListener('change', function() {
            document.getElementById('otherInput').style.display = this.checked ? 'block' : 'none';
        });

        // Show/hide previous reporting details
        document.querySelectorAll('input[name="reported_before"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const detailsDiv = document.getElementById('reportedDetails');
                detailsDiv.style.display = (this.value === 'Yes') ? 'block' : 'none';
            });
        });

        // File upload functionality
        const uploadTrigger = document.getElementById('uploadTrigger');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');

        uploadTrigger.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];

                // Validate file type
                if (file.type !== 'application/pdf') {
                    alert('Please upload a PDF file only.');
                    this.value = '';
                    return;
                }

                // Validate file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10MB limit. Please upload a smaller file.');
                    this.value = '';
                    return;
                }

                // Display file info
                fileList.innerHTML = `
                    <div class="file-item">
                        <div class="file-name">
                            <i class="fas fa-file-pdf" style="color: #e53e3e;"></i>
                            <div>
                                <div>${file.name}</div>
                                <div style="font-size: 0.8rem; color: #718096;">${formatFileSize(file.size)}</div>
                            </div>
                        </div>
                        <button class="file-remove" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        });

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeFile() {
            fileInput.value = '';
            fileList.innerHTML = '';
        }

        // Form submission
        // document.getElementById('whistleblowerForm').addEventListener('submit', function(e) {
        //     e.preventDefault();

        //     // Check if file is required but not uploaded
        //     if (fileInput.files.length === 0) {
        //         const uploadConfirmation = confirm(
        //             'You haven\'t uploaded any evidence file. Are you sure you want to submit without evidence?'
        //             );
        //         if (!uploadConfirmation) return;
        //     }

        //     //submit the form now


        //     alert(
        //         'Thank you for your submission. Your report has been received and will be reviewed by our compliance team.');
        //     this.reset();
        //     document.getElementById('otherInput').style.display = 'none';
        //     document.getElementById('reportedDetails').style.display = 'none';
        //     fileList.innerHTML = '';
        // });
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</body>

</html>
