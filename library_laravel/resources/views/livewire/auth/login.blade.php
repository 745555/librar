<div>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Readex Pro', sans-serif; }
        
        .login-wrapper {
            display: flex;
            min-height: 100vh;
            background: #fff;
            overflow: hidden;
        }

        /* Left Side - Image/Banner */
        .login-banner {
            flex: 1.2;
            position: relative;
            background: url('/library_split_login_bg_1778321349477.png') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            color: white;
        }

        .login-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(225deg, rgba(24, 95, 132, 0.85) 0%, rgba(16, 64, 89, 0.7) 100%);
            z-index: 1;
        }

        .banner-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
            text-align: right;
            animation: fadeInRight 1s ease-out;
        }

        .banner-content h2 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            line-height: 1.2;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .banner-content p {
            font-size: 1.25rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 40px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .stat-item i { font-size: 24px; margin-bottom: 10px; color: #28a270; }
        .stat-item .val { font-size: 20px; font-weight: 800; display: block; }
        .stat-item .lbl { font-size: 13px; opacity: 0.8; }

        /* Right Side - Form */
        .login-form-section {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8fafc;
            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeInLeft 1s ease-out;
        }

        .logo-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-header i {
            font-size: 48px;
            color: #185f84;
            margin-bottom: 15px;
        }

        .logo-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .logo-header p {
            color: #64748b;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #334155;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 45px 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }

        .input-wrapper input:focus {
            border-color: #28a270;
            box-shadow: 0 0 0 4px rgba(40, 162, 112, 0.1);
            outline: none;
        }

        .input-wrapper input:focus + i { color: #28a270; }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
        }

        .forgot-pw {
            font-size: 14px;
            font-weight: 700;
            color: #185f84;
            text-decoration: none;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: #185f84;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-btn:hover {
            background: #104059;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(24, 95, 132, 0.2);
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #fee2e2;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Mobile Responsive */
        @media (max-width: 968px) {
            .login-banner { display: none; }
            .login-form-section { flex: 1; }
        }
    </style>

    <div class="login-wrapper">
        <div class="login-banner">
            <div class="banner-content">
                <h2>مرحباً بك في<br>مكتبة كلية التقنية</h2>
                <p>نظام الأرشفة الإلكتروني الحديث لإدارة الكتب والمشاريع البحثية، صُمم خصيصاً لتسهيل العملية التعليمية في كلية التقنية الهندسية جنزور.</p>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <i class="fas fa-book"></i>
                        <span class="val">5,000+</span>
                        <span class="lbl">عنوان متاح</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span class="val">1,200+</span>
                        <span class="lbl">مشروع بحثي</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-form-section">
            <div class="form-container">
                <div class="logo-header">
                    <i class="fas fa-landmark"></i>
                    <h1>تسجيل الدخول</h1>
                    <p>أهلاً بك مجدداً، يرجى إدخال بياناتك</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form wire:submit.prevent="login">
                    <div class="input-group">
                        <label>اسم المستخدم</label>
                        <div class="input-wrapper">
                            <input type="text" wire:model="username" placeholder="أدخل اسم المستخدم" required>
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>كلمة المرور</label>
                        <div class="input-wrapper">
                            <input type="password" wire:model="password" placeholder="أدخل كلمة المرور" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <div class="options">
                        <label class="remember-me">
                            <input type="checkbox" wire:model="remember"> <span>تذكرني</span>
                        </label>
                    </div>

                    <button type="submit" class="login-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove>دخول للنظام <i class="fas fa-arrow-left"></i></span>
                        <span wire:loading><i class="fas fa-spinner fa-spin"></i> جاري التحقق...</span>
                    </button>
                </form>

                <div style="text-align: center; margin-top: 40px; color: #94a3b8; font-size: 13px;">
                    © 2026 كلية التقنية الهندسية جنزور - وحدة البرمجة والأنظمة
                </div>
            </div>
        </div>
    </div>
</div>
