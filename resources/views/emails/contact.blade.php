<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission - 3Sixty Shows</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #28a745;
            min-width: 100px;
            margin-right: 10px;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .message-section {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .message-title {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .message-text {
            line-height: 1.6;
            color: #555;
            white-space: pre-wrap;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 New Contact Form Submission</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">3Sixty Shows - Contact Form</p>
        </div>

        <div class="content">
            <p>Hello Admin,</p>
            <p>You have received a new message through the contact form on your website. Here are the details:</p>

            <div class="info-section">
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $phone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Subject:</div>
                    <div class="info-value">{{ $subject }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted:</div>
                    <div class="info-value">{{ $sent_at }}</div>
                </div>
            </div>

            <div class="message-section">
                <div class="message-title">📝 Message:</div>
                <div class="message-text">{{ $message }}</div>
            </div>

            <div class="alert">
                <strong>⚡ Quick Action:</strong> You can reply directly to this email to respond to {{ $name }}.
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="mailto:{{ $email }}?subject=Re: {{ $subject }}" class="button">
                    📧 Reply to {{ $name }}
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>3Sixty Shows</strong> - Your Entertainment Hub</p>
            <p>This email was automatically generated from your website contact form.</p>
            <p>Please do not reply to this email address. Use the reply button above to contact the sender.</p>
        </div>
    </div>
</body>
</html>
