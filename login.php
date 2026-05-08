<?php
// login.php - صفحة تسجيل الدخول الحديثة لمكتبة الكلية الجامعية
session_start();
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM library_staff WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['staff_id'] = $user['id'];
        $_SESSION['staff_username'] = $user['username'];
        $_SESSION['staff_name'] = $user['full_name'];
        $_SESSION['staff_role'] = $user['role'];
        $_SESSION['staff_image'] = $user['avatar'] ?? '';
        header("Location: index.php");
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام إدارة مكتبة كلية التقنية الهندسية - جنزور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, rgba(232, 224, 213, 0.9) 0%, rgba(214, 204, 191, 0.9) 100%), url('assets/images/2.jpg') center/cover fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        /* زخرفة كتب متحركة */
        body::before {
            content: "📚";
            position: fixed;
            bottom: 20px;
            left: 20px;
            font-size: 120px;
            opacity: 0.05;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        body::after {
            content: "📖";
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 100px;
            opacity: 0.05;
            pointer-events: none;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .login-container {
            width: 100%;
            max-width: 520px;
            z-index: 2;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            border-radius: 48px;
            padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(156, 128, 88, 0.1);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .library-icon {
            text-align: center;
            margin-bottom: 24px;
        }

        .library-icon i {
            font-size: 64px;
            color: #8B7355;
            background: #f5efe8;
            padding: 20px;
            border-radius: 60px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        h1 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #3d2b1a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            text-align: center;
            color: #8B7355;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 32px;
            border-bottom: 2px solid #e8ddd0;
            display: inline-block;
            width: auto;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 10px;
            line-height: 1.6;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #b8a58c;
            font-size: 18px;
            transition: color 0.3s;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            font-size: 16px;
            font-family: 'Cairo', sans-serif;
            border: 2px solid #e8ddd0;
            border-radius: 28px;
            background: #ffffff;
            transition: all 0.3s;
            outline: none;
            color: #2c241a;
            font-weight: 500;
        }

        .input-group input:focus {
            border-color: #9b7b5c;
            box-shadow: 0 0 0 4px rgba(139, 115, 85, 0.1);
        }

        .input-group input::placeholder {
            color: #cbbca8;
            font-weight: 400;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 28px;
            font-size: 14px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b5a48;
            cursor: pointer;
        }

        .checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #8B7355;
        }

        .forgot-link {
            color: #8B7355;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #5c4a34;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: #8B7355;
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .login-btn:hover {
            background: #6b543a;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(107, 84, 58, 0.2);
        }

        .alert {
            padding: 14px 20px;
            border-radius: 28px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .alert-error {
            background: #fef2f0;
            color: #bc5a3c;
            border-right: 4px solid #bc5a3c;
        }

        .alert-success {
            background: #eef6ec;
            color: #5a7c48;
            border-right: 4px solid #5a7c48;
        }

        .university-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            padding: 6px 14px;
            background: rgba(139, 115, 85, 0.1);
            border-radius: 20px;
            font-size: 12px;
            color: #8B7355;
            border: 1px solid rgba(139, 115, 85, 0.2);
        }

        .university-badge i {
            font-size: 14px;
        }

        .demo-info {
            background: #f9f5ef;
            border-radius: 20px;
            padding: 12px;
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #8B7355;
        }

        .extra-links {
            text-align: center;
            font-size: 14px;
            color: #8B7355;
        }

        hr {
            margin: 24px 0;
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, #e8ddd0, transparent);
        }

        @media (max-width: 550px) {
            .login-card {
                padding: 32px 24px;
            }
            h1 {
                font-size: 26px;
            }
            .subtitle {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="library-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <h1><i class="fas fa-graduation-cap"></i> كلية التقنية الهندسية</h1>
            <div style="text-align: center;">
                <div class="subtitle">
                    نظام الأرشفة الإلكتروني
                </div>
                <div class="university-badge">
                    <i class="fas fa-university"></i>
                    <span>لمكتــبة الكــلية </span>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <i class="fas fa-user-graduate"></i>
                    <input type="text" name="username" placeholder="اسم المستخدم" required autocomplete="username">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="كلمة المرور" required autocomplete="current-password">
                </div>

                <div class="options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember"> <span>تذكرني</span>
                    </label>
                    <a href="#" class="forgot-link">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    دخول إلى المكتبة
                </button>
            </form>

            <div class="demo-info">
                <i class="fas fa-info-circle"></i> نظام إدارة مكتبة كلية التقنية الهندسية - جنزور<br>
                <strong>ملاحظة:</strong> يرجى استخدام بيانات الدخول المعتمدة من إدارة المكتبة
            </div>

            <hr>

            <div class="extra-links">
                ليس لديك حساب؟ <a href="#">تواصل مع إدارة المكتبة</a><br>
                <small style="display: block; margin-top: 12px; color: #aa9c8a;">
                    <i class="fas fa-book-open"></i> المعرفة نور .. و مكتبتكم تنتظركم
                </small>
            </div>
        </div>
    </div>
</body>
</html>
