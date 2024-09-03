<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموقع تحت الصيانة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: 'Cairo', sans-serif;
            text-align: center;
            direction: rtl;
            overflow: hidden;
            position: relative;
        }
        .container {
            background-color: #fff;
            padding: 40px 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
            animation: fadeIn 2s ease-in-out;
        }
        .container h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 20px;
            animation: slideInFromLeft 1s ease-in-out;
        }
        .container p {
            font-size: 1.2em;
            color: #666;
            animation: slideInFromRight 1s ease-in-out;
        }
        .animation {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            background-image: url('https://websiterepair.sg/wp-content/uploads/2022/02/websiterepair-3-scaled.jpg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            animation: bounce 2s infinite;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInFromLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideInFromRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
        .bubble {
            position: absolute;
            bottom: -50px;
            width: 20px;
            height: 20px;
            background-color: rgba(255, 0, 150, 0.7);
            border-radius: 50%;
            animation: bubbleAnimation 5s infinite;
        }
        @keyframes bubbleAnimation {
            0% {
                bottom: -50px;
                opacity: 0;
                transform: translateX(0);
            }
            50% {
                opacity: 1;
            }
            100% {
                bottom: 100%;
                opacity: 0;
                transform: translateX(200px);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="animation"></div>
        <h1>الموقع تحت الصيانة</h1>
        <p>نحن نعمل حالياً على الموقع. شكراً لصبركم وتفهمكم.</p>
        <p>سنحرص على أن يعمل في الساعات القليلة القادمة.</p>
    </div>
    <script>
        function createBubble() {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble');
            bubble.style.left = `${Math.random() * 100}%`;
            bubble.style.animationDuration = `${Math.random() * 3 + 2}s`;
            document.body.appendChild(bubble);

            setTimeout(() => {
                bubble.remove();
            }, 5000);
        }

        setInterval(createBubble, 500);
    </script>
</body>
</html>
