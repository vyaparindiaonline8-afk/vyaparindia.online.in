<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - VyaparIndia</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6; }
        .wrapper { width: 100%; max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; margin-top: 30px; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 32px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0 0 6px 0; font-size: 24px; font-weight: 800; }
        .header p { margin: 0; font-size: 13px; opacity: 0.9; }
        .content { padding: 32px 30px; }
        .btn-container { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff !important; padding: 13px 30px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 15px; }
        .btn:hover { background: #1d4ed8; }
        .notice { background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 14px; margin-top: 20px; font-size: 12px; color: #92400e; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>🇮🇳 VyaparIndia</h1>
            <p>पासवर्ड रीसेट अनुरोध (Password Reset Request)</p>
        </div>

        <div class="content">
            <h3 style="margin-top: 0; color: #0f172a; font-size: 18px;">नमस्ते,</h3>
            <p style="font-size: 14px; color: #334155;">
                हमें आपके VyaparIndia अकाउंट (<strong>{{ $email }}</strong>) के लिए पासवर्ड रीसेट करने का अनुरोध प्राप्त हुआ है।
            </p>
            <p style="font-size: 14px; color: #334155;">
                नया पासवर्ड बनाने के लिए कृपया नीचे दिए गए नीले बटन पर क्लिक करें:
            </p>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">
                    नया पासवर्ड बनाएं (Reset Password)
                </a>
            </div>

            <p style="font-size: 12px; color: #64748b; word-break: break-all;">
                यदि ऊपर दिया गया बटन काम नहीं कर रहा है, तो इस लिंक को कॉपी करके अपने ब्राउज़र में खोलें:<br>
                <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
            </p>

            <div class="notice">
                ⚠️ <strong>सुरक्षा सूचना:</strong> यह पासवर्ड रीसेट लिंक अगले <strong>60 मिनट</strong> के लिए ही वैध है। यदि आपने यह अनुरोध नहीं किया था, तो आप इस ईमेल को अनदेखा कर सकते हैं; आपका पासवर्ड सुरक्षित रहेगा।
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0;"><strong>VyaparIndia</strong> | सहायता: <a href="mailto:vyaparindiaonline8@gmail.com" style="color: #2563eb; text-decoration: none;">vyaparindiaonline8@gmail.com</a></p>
        </div>
    </div>
</body>
</html>
