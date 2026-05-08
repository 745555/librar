<?php
// Simple script to add avatar column
echo "<h2>إصلاح عمود الصورة الرمزية</h2>";

try {
    // Connect to MySQL server first
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS library");
    echo "<p style='color: green;'>✓ قاعدة البيانات جاهزة</p>";
    
    // Use the database
    $pdo->exec("USE library");
    
    // Check if table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'library_staff'");
    if ($tables->rowCount() == 0) {
        // Create the table
        $create_sql = "
        CREATE TABLE library_staff (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            role ENUM('admin', 'staff') DEFAULT 'staff',
            avatar VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        $pdo->exec($create_sql);
        echo "<p style='color: green;'>✓ تم إنشاء جدول الموظفين</p>";
    } else {
        echo "<p style='color: blue;'>📋 جدول الموظفين موجود بالفعل</p>";
    }
    
    // Check if avatar column exists
    $columns = $pdo->query("SHOW COLUMNS FROM library_staff LIKE 'avatar'");
    if ($columns->rowCount() == 0) {
        // Add avatar column
        $pdo->exec("ALTER TABLE library_staff ADD COLUMN avatar VARCHAR(255) NULL AFTER phone");
        echo "<p style='color: green;'>✓ تم إضافة عمود الصورة الرمزية</p>";
    } else {
        echo "<p style='color: blue;'>📷 عمود الصورة الرمزية موجود بالفعل</p>";
    }
    
    // Check if admin exists
    $admin_check = $pdo->query("SELECT COUNT(*) as count FROM library_staff WHERE role = 'admin'");
    $result = $admin_check->fetch();
    if ($result['count'] == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = $pdo->prepare("
            INSERT INTO library_staff (username, password, full_name, email, role) 
            VALUES (?, ?, ?, ?, 'admin')
        ");
        $insert_admin->execute(['admin', $default_password, 'مدير النظام', 'admin@library.com']);
        echo "<p style='color: green;'>✓ تم إنشاء حساب مدير افتراضي</p>";
        echo "<p><strong>معلومات الدخول:</strong><br>";
        echo "اسم المستخدم: admin<br>";
        echo "كلمة المرور: admin123</p>";
    } else {
        echo "<p style='color: blue;'>👤 يوجد حساب مدير بالفعل</p>";
    }
    
    echo "<h3 style='color: green;'>✅ الإعداد اكتمل بنجاح!</h3>";
    echo "<p>يمكنك الآن استخدام نظام الصور الشخصية.</p>";
    echo "<p><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الذهاب إلى النظام</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ خطأ: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
