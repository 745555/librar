<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - نظام المكتبة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-blue: #185f84;
            --primary-green: #28a270;
            --bg-off-white: #f7f7f7;
            --text-dark: #1e293b;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
            background: var(--bg-off-white);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            text-align: center;
        }
        .error-container {
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            color: var(--primary-blue);
            margin: 0;
            line-height: 1;
            position: relative;
            display: inline-block;
        }
        .error-code::after {
            content: '';
            position: absolute;
            bottom: 10px;
            right: 0;
            width: 100%;
            height: 15px;
            background: rgba(40, 162, 112, 0.2);
            z-index: -1;
        }
        h1 {
            font-size: 28px;
            margin: 20px 0 15px;
            color: var(--primary-blue);
        }
        p {
            color: #64748b;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            background: var(--primary-green);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 162, 112, 0.2);
            background: #218c61;
        }
        .icon-box {
            font-size: 60px;
            color: var(--primary-green);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon-box">
            @yield('icon')
        </div>
        <div class="error-code">@yield('code')</div>
        <h1>@yield('message_title')</h1>
        <p>@yield('message_body')</p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fas fa-home"></i> العودة للرئيسية
        </a>
    </div>
</body>
</html>
