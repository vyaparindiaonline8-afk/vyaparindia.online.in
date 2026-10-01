<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to VyaparIndia</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; margin-top: 30px; margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 36px 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0 0 8px 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 32px 30px; }
        .greeting { font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #0f172a; }
        .info-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 24px 0; }
        .info-card h3 { margin: 0 0 10px 0; color: #166534; font-size: 15px; font-weight: 700; }
        .info-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
        .info-label { color: #475569; font-weight: 600; }
        .info-value { color: #0f172a; font-weight: 700; }
        .btn-container { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff !important; padding: 14px 32px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 15px; }
        .btn:hover { background: #1d4ed8; }
        .features { margin: 24px 0; border-top: 1px dashed #cbd5e1; padding-top: 20px; }
        .feature-item { display: flex; align-items: flex-start; margin-bottom: 14px; font-size: 13px; color: #334155; }
        .feature-icon { font-size: 18px; margin-right: 12px; }
        .footer { background: #f8fafc; padding: 24px 30px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .footer a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="header">
            <h1>🇮🇳 VyaparIndia</h1>
            <p>भारत का अपना डिजिटल B2B व्यापार और रीसेलिंग प्लेटफ़ॉर्म</p>
        </div>

        <!-- Body Content -->
        <div class="content">
            <div class="greeting">नमस्ते {{ $user->name }} जी,</div>
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #334155;">
                VyaparIndia प्लेटफ़ॉर्म से जुड़ने के लिए आपका हार्दिक स्वागत है! आपका <strong>{{ $user->is_seller() ? 'Seller (विक्रेता / व्यापारी)' : 'Buyer (खरीदार)' }}</strong> अकाउंट सफलतापूर्वक एक्टिवेट हो गया है।
            </p>

            @if($user->is_seller() && !empty($storeUrl))
            <!-- Seller Store Details Card -->
            <div class="info-card">
                <h3>🎉 आपकी डिजिटल मिनी-वेबसाइट तैयार है:</h3>
                <div style="margin-bottom: 10px; font-size: 13px;">
                    <span class="info-label">फर्म / दुकान का नाम:</span>
                    <strong style="color: #0f172a; margin-left: 6px;">{{ $companyName ?? $user->name }}</strong>
                </div>
                <div style="margin-bottom: 10px; font-size: 13px;">
                    <span class="info-label">मिनी-वेबसाइट लिंक:</span><br>
                    <a href="{{ $storeUrl }}" target="_blank" style="color: #2563eb; font-weight: 700; word-break: break-all; text-decoration: underline;">
                        {{ $storeUrl }}
                    </a>
                </div>
                <div style="font-size: 12px; color: #15803d;">
                    💡 आप इस लिंक को सीधे अपने WhatsApp Status, Business Bio या ग्राहकों के साथ शेयर कर सकते हैं।
                </div>
            </div>

            <!-- Dashboard Button -->
            <div class="btn-container">
                <a href="{{ route('seller.dashboard') }}" class="btn">
                    सेलर डैशबोर्ड खोलें ➔
                </a>
            </div>

            <!-- Features Highlights -->
            <div class="features">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 12px; color: #0f172a;">
                    🚀 अब आप अपने डैशबोर्ड से क्या-क्या कर सकते हैं:
                </div>
                <div class="feature-item">
                    <span class="feature-icon">📦</span>
                    <div><strong>प्रोडक्ट्स लिस्ट करें:</strong> अपनी दुकान के सभी उत्पादों की फ़ोटो, थोक और फुटकर रेट जोड़ें।</div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">💬</span>
                    <div><strong>डायरेक्ट WhatsApp ऑर्डर्स:</strong> ग्राहक आपकी वेबसाइट से सीधा WhatsApp पर लिस्ट और बिल बनाकर ऑर्डर भेज सकेंगे।</div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">⭐</span>
                    <div><strong>Google Reviews और Map जोड़ें:</strong> अपनी दुकान का गूगल मैप और रिव्यूज जोड़कर ग्राहकों का भरोसा बढ़ाएं।</div>
                </div>
            </div>

            @else
            <!-- Buyer Button -->
            <div class="btn-container">
                <a href="{{ url('/') }}" class="btn">
                    थोक और स्थानीय उत्पाद देखें ➔
                </a>
            </div>
            @endif

            <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
                यदि आपको किसी भी सहायता की आवश्यकता है, तो आप हमें सीधे इस ईमेल पर उत्तर दे सकते हैं।
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;"><strong>VyaparIndia Online</strong> — Empowering MSMEs Across India</p>
            <p style="margin: 0;">
                वेबसाइट: <a href="https://www.vyaparindia.online">vyaparindia.online</a> | ईमेल: <a href="mailto:vyaparindiaonline8@gmail.com">vyaparindiaonline8@gmail.com</a>
            </p>
        </div>
    </div>
</body>
</html>
