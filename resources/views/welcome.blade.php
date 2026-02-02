<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - GR Silver International Pvt Ltd</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }

        .container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logo-container {
            animation: fadeInDown 1s ease-out;
            margin-bottom: 40px;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #c0c0c0, #f0f0f0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            }
        }

        .welcome-text {
            text-align: center;
            color: white;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .welcome-text h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 300;
            letter-spacing: 2px;
            text-shadow: 2px 2px 20px rgba(0, 0, 0, 0.3);
        }

        .company-name {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
            background: linear-gradient(45deg, #fff, #c0c0c0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                filter: brightness(1);
            }

            50% {
                filter: brightness(1.3);
            }
        }

        .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
            margin-bottom: 50px;
            animation: fadeIn 1s ease-out 0.6s both;
        }

        .cta-button {
            background: white;
            color: #667eea;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.9s both;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #c0c0c0, #f0f0f0);
        }

        .features {
            display: flex;
            gap: 40px;
            margin-top: 60px;
            animation: fadeInUp 1s ease-out 1.2s both;
            flex-wrap: wrap;
            justify-content: center;
        }

        .feature {
            text-align: center;
            color: white;
            max-width: 200px;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 30px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .feature:hover .feature-icon {
            transform: scale(1.1) rotate(10deg);
            background: rgba(255, 255, 255, 0.3);
        }

        .feature h3 {
            font-size: 1.1rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .feature p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .welcome-text h1 {
                font-size: 2.5rem;
            }

            .company-name {
                font-size: 1.5rem;
            }

            .tagline {
                font-size: 1rem;
            }

            .features {
                gap: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="particles" id="particles"></div>

    <div class="container">
        <div class="logo-container">
            <div class="logo">GR</div>
        </div>

        <div class="welcome-text">
            <h1>WELCOME</h1>
            <div class="company-name">GR SILVER INTERNATIONAL PVT LTD</div>
            <p class="tagline">Excellence in Silver Trading & International Commerce</p>
            <button class="cta-button" onclick="enterSite()">Enter</button>
        </div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">💎</div>
                <h3>Premium Quality</h3>
                <p>Certified pure silver products</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🌍</div>
                <h3>Global Reach</h3>
                <p>International trading network</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🤝</div>
                <h3>Trust & Reliability</h3>
                <p>Your trusted partner</p>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
            particlesContainer.appendChild(particle);
        }

        function enterSite() {
            document.body.style.transition = 'opacity 0.5s';
            document.body.style.opacity = '0';
            setTimeout(() => {
                alert(
                    'Welcome to GR Silver International Pvt Ltd!\n\nThis is where your main website content would load.'
                );
                document.body.style.opacity = '1';
            }, 500);
        }
    </script>
</body>

</html>
