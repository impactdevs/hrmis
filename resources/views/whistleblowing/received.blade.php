<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .thank-you-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .checkmark {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        p {
            color: #34495e;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .confirmation-id {
            background-color: #e8f4ff;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-weight: bold;
            font-size: 16px;
            margin: 25px 0;
            display: inline-block;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            text-align: left;
            margin: 25px 0;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .contact-info {
            margin-top: 30px;
            font-size: 16px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="checkmark">✓</div>
        <h1>Thank You for Your Report</h1>
        
        <p>Your whistleblowing submission has been received successfully. We appreciate your courage in coming forward.</p>
        
        <div class="confirmation-id">
            Report ID: WB-{{ date('Ymd') }}-{{ strtoupper(Str::random(6)) }}
        </div>
        
        <div class="info-box">
            <strong>What happens next:</strong>
            <ul style="margin-top: 10px; margin-left: 20px;">
                <li>Your report is now being processed by our ethics team</li>
                <li>All submissions are treated with strict confidentiality</li>
                {{-- <li>You may be contacted for additional information if needed</li> --}}
            </ul>
        </div>
        
        <p>We take all reports seriously and will investigate this matter promptly.</p>
        
        <a href="/" class="btn">Return to Homepage</a>
        
        <div class="contact-info">
            <p>If you need to contact us regarding this report:<br>
            Email: info@uncst.go.ug<br>
            Phone: +256 414 705 500</p>
        </div>
    </div>
</body>
</html>