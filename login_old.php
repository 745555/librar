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
    <title>تسجيل الدخول | مكتبة الكلية الجامعية</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #2d3748 50%, #4a5568 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation .floating-book {
            position: absolute;
            width: 60px;
            height: 80px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            animation: float-book 15s infinite ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: rgba(102, 126, 234, 0.3);
        }

        @keyframes float-book {
            0%, 100% { 
                transform: translateY(0) rotate(0deg); 
                opacity: 0.3;
            }
            25% { 
                transform: translateY(-30px) rotate(5deg); 
                opacity: 0.6;
            }
            50% { 
                transform: translateY(-60px) rotate(-3deg); 
                opacity: 0.4;
            }
            75% { 
                transform: translateY(-30px) rotate(2deg); 
                opacity: 0.7;
            }
        }

        .bg-animation .gradient-orb {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.2) 0%, transparent 70%);
            animation: pulse-orb 30s infinite ease-in-out;
        }

        @keyframes pulse-orb {
            0%, 100% { 
                transform: scale(1); 
                opacity: 0.3;
            }
            50% { 
                transform: scale(1.2); 
                opacity: 0.6;
            }
        }

        /* Main Container */
        .login-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px -12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-card:hover {
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(102, 126, 234, 0.3);
        }

        /* Card Header */
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Card Body */
        .card-body {
            padding: 25px 30px;
            background: white;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shine 15s infinite;
        }

        @keyframes shine {
            0% { transform: translate(-30%, -30%) rotate(0deg); }
            100% { transform: translate(30%, 30%) rotate(360deg); }
        }

        .logo-section {
            position: relative;
            z-index: 2;
        }

        .logo-icon {
            font-size: 2.8rem;
            color: white;
            margin-bottom: 15px;
            display: inline-block;
            animation: bounce 3s infinite;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .card-header h1 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.01em;
            line-height: 1.2;
        }

        .card-header .subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.02em;
            line-height: 1.6;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-group input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Tajawal', sans-serif;
            transition: all 0.3s ease;
            background: #f9fafb;
            text-align: right;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-group input:focus + i {
            color: #667eea;
        }

        .password-toggle {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        /* Options Row */
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 0.9rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #6b7280;
        }

        .checkbox-wrapper input {
            margin-left: 8px;
            cursor: pointer;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .forgot-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'Tajawal', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn.loading {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            cursor: not-allowed;
        }

        /* Error Message */
        .error-message {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-right: 4px solid #ef4444;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message i {
            color: #ef4444;
            font-size: 1.3rem;
        }

        .error-message span {
            color: #991b1b;
            font-size: 0.95rem;
            flex: 1;
        }

        .error-close {
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .error-close:hover {
            color: #374151;
        }

        /* Demo Info */
        .demo-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .demo-title {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .demo-credentials {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .credential-item {
            background: #f3f4f6;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #374151;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .credential-item:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .credential-item strong {
            color: #667eea;
            font-weight: 600;
        }

        /* Footer */
        .login-footer {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-footer p {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .login-footer .version {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .card-header {
                padding: 40px 30px;
            }
            
            .card-body {
                padding: 40px 30px;
            }
            
            .options-row {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .demo-credentials {
                flex-direction: column;
                align-items: center;
            }

            .login-card {
                margin: 10px;
            }
        }

        /* Loading Animation */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <?php
        // Generate floating books
        for ($i = 0; $i < 8; $i++) {
            $size = rand(40, 80);
            $left = rand(5, 95);
            $duration = rand(15, 25);
            $delay = rand(0, 10);
            echo "<div class='floating-book' style='width: {$size}px; height: " . ($size * 1.3) . "px; left: {$left}%; top: " . rand(10, 80) . "%; animation-duration: {$duration}s; animation-delay: {$delay}s;'><i class='fas fa-book'></i></div>";
        }
        
        // Generate gradient orbs
        for ($i = 0; $i < 3; $i++) {
            $size = rand(100, 300);
            $left = rand(10, 90);
            $top = rand(10, 90);
            $duration = rand(20, 30);
            echo "<div class='gradient-orb' style='width: {$size}px; height: {$size}px; left: {$left}%; top: {$top}%; animation-duration: {$duration}s;'></div>";
        }
        ?>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Card Header -->
            <div class="card-header">
                <div class="logo-section">
                    <div class="logo-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <h1>مكتبة الكلية</h1>
                    <div class="subtitle">نظـام إدارة مكــتبة كلــية التنقــنية الهنــدسـية - جنزور</div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <?php if($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                        <i class="fas fa-times error-close" onclick="this.parentElement.style.display='none'"></i>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <div class="input-group">
                        <label>اسم المستخدم</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" placeholder="أدخل اسم المستخدم" required autocomplete="off">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>كلمة المرور</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="أدخل كلمة المرور" required>
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="options-row">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">تذكرني</label>
                        </div>
                        <a href="#" class="forgot-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i>
                        دخول إلى النظام
                    </button>
                </form>

                <div class="demo-section">
                    <div class="demo-title">
                        <i class="fas fa-info-circle"></i>
                        بيانات الدخول التجريبية
                    </div>
                    <div class="demo-credentials">
                        <div class="credential-item">
                            <strong>المستخدم:</strong> admin
                        </div>
                        <div class="credential-item">
                            <strong>كلمة المرور:</strong> admin123
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>مكتبة الكلية الجامعية - النظام المتكامل لإدارة الموارد</p>
                <p class="version">
                    <i class="fas fa-code"></i>  جميع الحقوق محفوظة
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        // Remember Me functionality
        const rememberCheckbox = document.getElementById('remember');
        const usernameInput = document.querySelector('input[name="username"]');
        
        if (localStorage.getItem('rememberedUsername')) {
            usernameInput.value = localStorage.getItem('rememberedUsername');
            if (rememberCheckbox) rememberCheckbox.checked = true;
        }

        document.getElementById('loginForm')?.addEventListener('submit', function() {
            if (rememberCheckbox && rememberCheckbox.checked) {
                localStorage.setItem('rememberedUsername', usernameInput.value);
            } else {
                localStorage.removeItem('rememberedUsername');
            }
        });

        // Add loading effect on submit
        const loginBtn = document.getElementById('loginBtn');
        const loginForm = document.getElementById('loginForm');

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (loginBtn) {
                    loginBtn.innerHTML = '<span class="spinner"></span> جاري الدخول...';
                    loginBtn.classList.add('loading');
                    loginBtn.disabled = true;
                }
            });
        }

        // Remove loading state on error
        document.addEventListener('DOMContentLoaded', function() {
            const errorMsg = document.querySelector('.error-message');
            if (errorMsg) {
                setTimeout(() => {
                    if (loginBtn) {
                        loginBtn.innerHTML = '<i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i> دخول إلى النظام';
                        loginBtn.classList.remove('loading');
                        loginBtn.disabled = false;
                    }
                }, 3000);
            }
        });

        // Enhanced floating animation
        const floatingBooks = document.querySelectorAll('.floating-book');
        floatingBooks.forEach((book, index) => {
            book.style.animation = `float-book ${15 + index * 2}s infinite ease-in-out`;
        });
    </script>
</body>
</html>