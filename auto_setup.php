<?php
// Auto-setup script for avatar functionality
session_start();

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit();
}

try {
    // Connect to database
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Use the database
    $pdo->exec("USE library");
    
    // Check if avatar column exists
    $columns = $pdo->query("SHOW COLUMNS FROM library_staff LIKE 'avatar'");
    
    if ($columns->rowCount() == 0) {
        // Add avatar column
        $pdo->exec("ALTER TABLE library_staff ADD COLUMN avatar VARCHAR(255) NULL AFTER phone");
        
        // Success message
        $_SESSION['setup_message'] = '✓ تم إضافة عمود الصورة الرمزية بنجاح! يمكنك الآن رفع الصور الشخصية.';
        
        // Redirect to account page
        header('Location: index.php?page=account');
        exit();
    } else {
        // Column already exists
        $_SESSION['setup_message'] = '✓ عمود الصورة الرمزية موجود بالفعل!';
        
        // Redirect to account page
        header('Location: index.php?page=account');
        exit();
    }
    
} catch (PDOException $e) {
    $_SESSION['setup_error'] = '❌ خطأ: ' . htmlspecialchars($e->getMessage());
    header('Location: index.php?page=account');
    exit();
}
?>
