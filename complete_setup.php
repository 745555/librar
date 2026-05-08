<?php
// Complete database setup for library system
echo "<h2>إعداد قاعدة البيانات الكاملة</h2>";

try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE library");
    echo "<p style='color: green;'>✓ قاعدة البيانات جاهزة</p>";
    
    // Create departments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_ar VARCHAR(100) NOT NULL,
            name_en VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✓ تم إنشاء جدول الأقسام</p>";
    
    // Create books table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            book_title VARCHAR(255) NOT NULL,
            author VARCHAR(255),
            isbn VARCHAR(20) UNIQUE,
            publisher VARCHAR(255),
            publication_year INT,
            quantity INT DEFAULT 1,
            available_quantity INT DEFAULT 1,
            department_id INT,
            custom_department VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✓ تم إنشاء جدول الكتب</p>";
    
    // Create projects table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_name VARCHAR(255) NOT NULL,
            student_name VARCHAR(255),
            supervisor VARCHAR(255),
            department_id INT,
            year INT,
            semester VARCHAR(50),
            project_type ENUM('project', 'research', 'thesis') DEFAULT 'project',
            description TEXT,
            file_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✓ تم إنشاء جدول المشاريع</p>";
    
    // Create faculty_borrowings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS faculty_borrowings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            faculty_name VARCHAR(255) NOT NULL,
            faculty_department VARCHAR(100),
            faculty_contact VARCHAR(100),
            book_title VARCHAR(255) NOT NULL,
            book_isbn VARCHAR(20),
            borrow_date DATE NOT NULL,
            expected_return_date DATE NOT NULL,
            actual_return_date DATE,
            status ENUM('active', 'returned', 'overdue') DEFAULT 'active',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✓ تم إنشاء جدول إعارات أعضاء هيئة التدريس</p>";
    
    // Create library_staff table with avatar column
    $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✓ تم إنشاء جدول الموظفين مع عمود الصورة</p>";
    
    // Add avatar column if it doesn't exist
    $pdo->exec("ALTER TABLE library_staff ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL AFTER phone");
    echo "<p style='color: green;'>✓ تم التأكد من عمود الصورة الرمزية</p>";
    
    // Insert sample departments
    $dept_check = $pdo->query("SELECT COUNT(*) FROM departments");
    if ($dept_check->fetchColumn() == 0) {
        $departments = [
            ['هندسة الحاسوب', 'Computer Engineering'],
            ['هندسة الاتصالات', 'Communication Engineering'],
            ['هندسة المدني', 'Civil Engineering'],
            ['هندسة الميكانيكا', 'Mechanical Engineering'],
            ['هندسة الكهرباء', 'Electrical Engineering']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO departments (name_ar, name_en) VALUES (?, ?)");
        foreach ($departments as $dept) {
            $stmt->execute($dept);
        }
        echo "<p style='color: green;'>✓ تم إضافة أقسام نموذجية</p>";
    }
    
    // Check if admin exists
    $admin_check = $pdo->query("SELECT COUNT(*) FROM library_staff WHERE role = 'admin'");
    if ($admin_check->fetchColumn() == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO library_staff (username, password, full_name, email, role) 
            VALUES (?, ?, ?, ?, 'admin')
        ");
        $stmt->execute(['admin', $default_password, 'مدير النظام', 'admin@library.com']);
        echo "<p style='color: green;'>✓ تم إنشاء حساب مدير افتراضي</p>";
    }
    
    echo "<h3 style='color: green;'>✅ تم الإعداد بنجاح!</h3>";
    echo "<p><strong>معلومات الدخول:</strong><br>";
    echo "اسم المستخدم: admin<br>";
    echo "كلمة المرور: admin123</p>";
    echo "<p><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الذهاب إلى النظام</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ خطأ: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
