<?php
// Debug script to check file upload configuration

echo "<h2>PHP File Upload Configuration Check</h2>";

echo "<h3>File Upload Settings:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "max_input_time: " . ini_get('max_input_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

echo "<h3>Upload Directory Check:</h3>";
$uploadDir = __DIR__ . '/storage/app/public';
echo "Upload directory: $uploadDir<br>";
echo "Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";

if ($_POST && isset($_FILES['test_file'])) {
    echo "<h3>File Upload Test Result:</h3>";
    $file = $_FILES['test_file'];
    
    echo "Original filename: " . $file['name'] . "<br>";
    echo "File size: " . formatBytes($file['size']) . " (" . $file['size'] . " bytes)<br>";
    echo "MIME type: " . $file['type'] . "<br>";
    echo "Upload error code: " . $file['error'] . "<br>";
    echo "Error description: " . getUploadError($file['error']) . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "<span style='color: green;'>✓ File uploaded successfully!</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Upload failed!</span><br>";
    }
}

function getUploadError($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_OK:
            return 'No error occurred';
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload';
        default:
            return 'Unknown upload error';
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2, h3 { color: #333; }
        .test-form { 
            border: 1px solid #ddd; 
            padding: 20px; 
            margin-top: 20px; 
            background: #f9f9f9; 
        }
    </style>
</head>
<body>

<div class="test-form">
    <h3>Test File Upload</h3>
    <p>Use this form to test file uploads and see detailed error information:</p>
    
    <form action="" method="post" enctype="multipart/form-data">
        <label for="test_file">Choose a file to test (max 10MB):</label><br>
        <input type="file" name="test_file" id="test_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"><br><br>
        <input type="submit" value="Upload Test File" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer;">
    </form>
</div>

</body>
</html>
