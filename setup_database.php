<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد قاعدة البيانات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s;
            margin: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .message {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .step {
            text-align: right;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-right: 4px solid #667eea;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <i class="fas fa-database icon"></i>
        <h1>إعداد قاعدة بيانات نظام الأرشفة</h1>
        
        <?php
        $setup_complete = false;
        $messages = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = new PDO("mysql:host=localhost", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Step 1: Create database
                $pdo->exec("CREATE DATABASE IF NOT EXISTS library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $messages[] = "✓ تم إنشاء قاعدة البيانات أو كانت موجودة";
                
                // Step 2: Use the database
                $pdo->exec("USE library");
                
                // Step 3: Create library_staff table
                $create_table_sql = "
                CREATE TABLE IF NOT EXISTS library_staff (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    full_name VARCHAR(100) NOT NULL,
                    email VARCHAR(100),
                    phone VARCHAR(20),
                    role ENUM('admin', 'staff') DEFAULT 'staff',
                    avatar VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                $pdo->exec($create_table_sql);
                $messages[] = "✓ تم إنشاء جدول الموظفين مع عمود الصورة";
                
                // Step 4: Check if admin user exists
                $admin_check = $pdo->query("SELECT COUNT(*) FROM library_staff WHERE role = 'admin'");
                $admin_count = $admin_check->fetchColumn();
                
                if ($admin_count == 0) {
                    // Create default admin user
                    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
                    $insert_admin = $pdo->prepare("
                        INSERT INTO library_staff (username, password, full_name, email, role) 
                        VALUES (?, ?, ?, ?, 'admin')
                    ");
                    $insert_admin->execute(['admin', $default_password, 'مدير النظام', 'admin@library.com']);
                    $messages[] = "✓ تم إنشاء حساب مدير افتراضي (admin/admin123)";
                } else {
                    $messages[] = "✓ يوجد حساب مدير بالفعل";
                }
                
                $setup_complete = true;
                
            } catch (PDOException $e) {
                $messages[] = "❌ خطأ: " . htmlspecialchars($e->getMessage());
            }
        }
        ?>
        
        <?php if (!$setup_complete): ?>
        <div class="step">
            <h3><i class="fas fa-info-circle"></i> ما سيتم إنشاؤه:</h3>
            <ul style="text-align: right; list-style: none; padding: 0;">
                <li>📊 قاعدة بيانات باسم <strong>library</strong></li>
                <li>👥 جدول الموظفين مع عمود الصورة الرمزية</li>
                <li>🔐 حساب مدير افتراضي (admin/admin123)</li>
                <li>🎨 دعم رفع الصور الشخصية</li>
            </ul>
        </div>
        
        <form method="POST">
            <button type="submit" class="btn">
                <i class="fas fa-play"></i> بدء الإعداد
            </button>
        </form>
        <?php else: ?>
        <div class="message success">
            <h3><i class="fas fa-check-circle"></i> تم الإعداد بنجاح!</h3>
            <?php foreach ($messages as $message): ?>
                <p><?php echo $message; ?></p>
            <?php endforeach; ?>
        </div>
        
        <div class="step">
            <h3><i class="fas fa-user-shield"></i> معلومات الدخول:</h3>
            <p><strong>اسم المستخدم:</strong> admin</p>
            <p><strong>كلمة المرور:</strong> admin123</p>
            <p style="color: #666; margin-top: 10px;">🔒 يرجى تغيير كلمة المرور بعد الدخول الأول</p>
        </div>
        
        <a href="index.php" class="btn">
            <i class="fas fa-sign-in-alt"></i> الذهاب إلى نظام الأرشفة
        </a>
        <?php endif; ?>
        
        <p><small>بعد الإكمال، يمكنك حذف هذا الملف من الخادم للأمان.</small></p>
    </div>
</body>
</html>
