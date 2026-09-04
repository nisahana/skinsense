<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #fde8ec, #fdf6f0, #fde8c8);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating bubbles */
        .bubbles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 0;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(224, 123, 138, 0.1);
            animation: floatUp linear infinite;
        }

        .bubble:nth-child(1)  { width: 40px;  height: 40px;  left: 10%; animation-duration: 8s;  animation-delay: 0s; }
        .bubble:nth-child(2)  { width: 20px;  height: 20px;  left: 20%; animation-duration: 12s; animation-delay: 2s; }
        .bubble:nth-child(3)  { width: 60px;  height: 60px;  left: 35%; animation-duration: 10s; animation-delay: 4s; }
        .bubble:nth-child(4)  { width: 80px;  height: 80px;  left: 50%; animation-duration: 7s;  animation-delay: 1s; }
        .bubble:nth-child(5)  { width: 30px;  height: 30px;  left: 65%; animation-duration: 11s; animation-delay: 3s; }
        .bubble:nth-child(6)  { width: 50px;  height: 50px;  left: 75%; animation-duration: 9s;  animation-delay: 5s; }
        .bubble:nth-child(7)  { width: 25px;  height: 25px;  left: 85%; animation-duration: 13s; animation-delay: 2s; }
        .bubble:nth-child(8)  { width: 70px;  height: 70px;  left: 90%; animation-duration: 6s;  animation-delay: 0s; }

        @keyframes floatUp {
            0%   { bottom: -100px; opacity: 0; transform: scale(0.5); }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { bottom: 110%; opacity: 0; transform: scale(1.2); }
        }

        /* Main content */
        .welcome-box {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
            animation: fadeInUp 1s ease forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .welcome-logo {
            font-size: 80px;
            animation: bounce 2s ease infinite;
            display: block;
            margin-bottom: 10px;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-15px); }
        }

        /* Brand name */
        .welcome-brand {
            font-size: 52px;
            font-weight: 800;
            background: linear-gradient(135deg, #e07b8a, #f8a5b0, #c0546a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        /* Tagline */
        .welcome-tagline {
            font-size: 18px;
            color: #999;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        /* Description */
        .welcome-desc {
            font-size: 15px;
            color: #bbb;
            margin-bottom: 40px;
            max-width: 380px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* Buttons */
        .welcome-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .btn-login {
            background: linear-gradient(135deg, #e07b8a, #c0546a);
            color: white;
            padding: 15px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 8px 25px rgba(224, 123, 138, 0.4);
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease 0.3s both;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(224, 123, 138, 0.5);
        }

        .btn-register {
            background: white;
            color: #e07b8a;
            padding: 15px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #e07b8a;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease 0.5s both;
        }

        .btn-register:hover {
            background: #fde8ec;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }

        /* Features row */
        .welcome-features {
            display: flex;
            gap: 25px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.7s both;
        }

        .welcome-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #aaa;
            font-size: 13px;
        }

        .welcome-feature span {
            font-size: 18px;
        }

        /* Typing animation */
        .typing-text {
            font-size: 16px;
            color: #e07b8a;
            font-weight: 600;
            margin-bottom: 20px;
            min-height: 24px;
        }

        .cursor {
            display: inline-block;
            width: 2px;
            height: 18px;
            background: #e07b8a;
            margin-left: 2px;
            animation: blink 0.7s infinite;
            vertical-align: middle;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0; }
        }

        /* Skin type pills */
        .skin-pills {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .skin-pill {
            background: white;
            border: 2px solid #f0e0e5;
            color: #e07b8a;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        /* Footer text */
        .welcome-footer {
            color: #ccc;
            font-size: 12px;
            animation: fadeInUp 1s ease 0.9s both;
        }
    </style>
</head>
<body>

    <!-- Floating bubbles -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <!-- Main Content -->
    <div class="welcome-box">

        <!-- Logo -->
        <span class="welcome-logo">🧴</span>

        <!-- Brand -->
        <h1 class="welcome-brand">SkinSense</h1>

        <!-- Tagline with typing animation -->
        <p class="welcome-tagline">AI-Powered Skin Analysis</p>
        <div class="typing-text">
            <span id="typingText"></span>
            <span class="cursor"></span>
        </div>

        <!-- Skin Type Pills -->
        <div class="skin-pills">
            <div class="skin-pill">💦 Oily</div>
            <div class="skin-pill">🌵 Dry</div>
            <div class="skin-pill">✨ Normal</div>
            
        </div>

        <!-- Buttons -->
        <div class="welcome-buttons">
            <a href="login.php" class="btn-login">Login →</a>
            <a href="register.php" class="btn-register">Register</a>
        </div>

        <!-- Features -->
        <div class="welcome-features">
            <div class="welcome-feature">
                <span>🤖</span> AI Detection
            </div>
            <div class="welcome-feature">
                <span>💡</span> Smart Recommendations
            </div>
            <div class="welcome-feature">
                <span>📊</span> Progress Tracking
            </div>
        </div>

        <!-- Footer -->
        <p class="welcome-footer">
            Your skin, understood. ✨
        </p>

    </div>

    <script>
    // Typing animation
    const texts = [
        "Detect your skin type instantly!",
        "Get personalized skincare routines.",
        "Track your skin progress over time.",
        "Look and feel your best every day."
    ];

    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typingElement = document.getElementById('typingText');

    function type() {
        const currentText = texts[textIndex];

        if(isDeleting) {
            typingElement.textContent = currentText.substring(0, charIndex - 1);
            charIndex--;
        } else {
            typingElement.textContent = currentText.substring(0, charIndex + 1);
            charIndex++;
        }

        if(!isDeleting && charIndex === currentText.length) {
            isDeleting = true;
            setTimeout(type, 1500);
            return;
        }

        if(isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
        }

        setTimeout(type, isDeleting ? 50 : 80);
    }

    type();
    </script>

        <!-- Admin Access Link -->
        <div style="position:fixed; bottom:20px; right:25px;">
            <a href="admin/login.php" 
            style="color:rgba(200,150,160,0.5); 
                    font-size:12px; 
                    text-decoration:none;
                    transition: all 0.3s;">
                Admin Access
            </a>
        </div>

</body>
</html>