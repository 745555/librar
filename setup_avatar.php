<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد عمود الصورة الرمزية</title>
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
            max-width: 500px;
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
    </style>
</head>
<body>
    <div class="container">
        <i class="fas fa-camera icon"></i>
        <h1>إعداد عمود الصورة الرمزية</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=library", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Check if column already exists
                $check_sql = "SHOW COLUMNS FROM library_staff LIKE 'avatar'";
                $stmt = $pdo->query($check_sql);
                
                if ($stmt->rowCount() > 0) {
                    echo '<div class="message success">✓ عمود الصورة الرمزية موجود بالفعل!</div>';
                } else {
                    // Add avatar column
                    $sql = "ALTER TABLE library_staff ADD COLUMN avatar VARCHAR(255) NULL AFTER phone";
                    $pdo->exec($sql);
                    echo '<div class="message success">✓ تم إضافة عمود الصورة الرمزية بنجاح!</div>';
                }
                
            } catch (PDOException $e) {
                echo '<div class="message error">❌ خطأ: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>
        
        <p>هذا الإجراء سيقوم بإضافة عمود الصورة الرمزية إلى جدول الموظفين في قاعدة البيانات.</p>
        
        <form method="POST">
            <button type="submit" class="btn">
                <i class="fas fa-database"></i> إضافة عمود الصورة
            </button>
        </form>
        
        <p><small>بعد الإكمال، يمكنك حذف هذا الملف من الخادم.</small></p>
    </div>
</body>
</html>
