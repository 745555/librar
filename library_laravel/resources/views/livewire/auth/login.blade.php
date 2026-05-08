<div>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, rgba(24, 95, 132, 0.9) 0%, rgba(16, 64, 89, 0.9) 100%), url('/assets/images/2.jpg') center/cover fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }
        body::before { content: "📚"; position: fixed; bottom: 20px; left: 20px; font-size: 120px; opacity: 0.05; pointer-events: none; animation: float 6s ease-in-out infinite; }
        body::after { content: "📖"; position: fixed; top: 20px; right: 20px; font-size: 100px; opacity: 0.05; pointer-events: none; animation: float 8s ease-in-out infinite reverse; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .login-container { width: 100%; max-width: 520px; z-index: 2; }
        .login-card { background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); border-radius: 40px; padding: 50px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transition: transform 0.3s ease; }
        .login-card:hover { transform: translateY(-5px); }
        .library-icon { text-align: center; margin-bottom: 24px; }
        .library-icon i { font-size: 64px; color: #185f84; background: #f7f7f7; padding: 20px; border-radius: 60px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        h1 { text-align: center; font-size: 32px; font-weight: 800; color: #185f84; margin-bottom: 12px; }
        .subtitle { text-align: center; color: #28a270; font-size: 14px; font-weight: 700; margin-bottom: 32px; border-bottom: 2px solid #e8e8e8; display: inline-block; width: auto; margin-left: auto; margin-right: auto; padding-bottom: 10px; }
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group i { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 18px; transition: color 0.3s; pointer-events: none; }
        .input-group input { width: 100%; padding: 16px 50px 16px 20px; font-size: 16px; border: 2px solid #e8e8e8; border-radius: 16px; background: #ffffff; transition: all 0.3s; outline: none; }
        .input-group input:focus { border-color: #28a270; box-shadow: 0 0 0 4px rgba(40, 162, 112, 0.1); }
        .options { display: flex; justify-content: space-between; align-items: center; margin: 20px 0 28px; font-size: 14px; }
        .checkbox { display: flex; align-items: center; gap: 8px; color: #185f84; cursor: pointer; font-weight: 600; }
        .forgot-link { color: #185f84; text-decoration: none; font-weight: 600; }
        .login-btn { width: 100%; padding: 16px; background: #28a270; color: white; border: none; border-radius: 50px; font-size: 18px; font-weight: 800; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 24px; }
        .login-btn:hover { background: #218c61; transform: scale(1.02); box-shadow: 0 10px 20px rgba(40, 162, 112, 0.2); }
        .alert { padding: 14px 20px; border-radius: 16px; margin-bottom: 24px; font-size: 14px; font-weight: 600; text-align: center; }
        .alert-error { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .university-badge { display: inline-flex; align-items: center; gap: 8px; margin-top: 8px; padding: 6px 14px; background: rgba(40, 162, 112, 0.1); border-radius: 20px; font-size: 12px; color: #28a270; font-weight: 700; }
        .demo-info { background: #f7f7f7; border-radius: 16px; padding: 15px; margin-top: 20px; text-align: center; font-size: 13px; color: #185f84; font-weight: 500; }
        .extra-links { text-align: center; font-size: 14px; color: #64748b; }
        hr { margin: 24px 0; border: none; height: 1px; background: linear-gradient(to right, transparent, #e8ddd0, transparent); }
    </style>

    <div class="login-container">
        <div class="login-card">
            <div class="library-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <h1><i class="fas fa-graduation-cap"></i> كلية التقنية الهندسية</h1>
            <div style="text-align: center;">
                <div class="subtitle">نظام الأرشفة الإلكتروني</div>
                <div class="university-badge">
                    <i class="fas fa-university"></i>
                    <span>لمكتــبة الكــلية </span>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form wire:submit.prevent="login">
                <div class="input-group">
                    <i class="fas fa-user-graduate"></i>
                    <input type="text" wire:model="username" placeholder="اسم المستخدم" required autocomplete="username">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" wire:model="password" placeholder="كلمة المرور" required autocomplete="current-password">
                </div>

                <div class="options">
                    <label class="checkbox">
                        <input type="checkbox" wire:model="remember"> <span>تذكرني</span>
                    </label>
                    <a href="#" class="forgot-link">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="login-btn" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-sign-in-alt"></i> دخول إلى المكتبة
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i> جاري الدخول...
                    </span>
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
</div>
