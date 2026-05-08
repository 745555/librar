<?php
session_start();
require_once 'database.php';

// Test avatar upload functionality
echo "<h2>اختبار رفع الصور</h2>";

// Check database connection
try {
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p>✓ متصل بقاعدة البيانات: " . htmlspecialchars($result['db_name']) . "</p>";
} catch (Exception $e) {
    echo "<p>❌ خطأ في الاتصال: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check avatar column
try {
    $check = $pdo->query("SHOW COLUMNS FROM library_staff LIKE 'avatar'");
    if ($check->rowCount() > 0) {
        echo "<p>✓ عمود avatar موجود</p>";
    } else {
        echo "<p>❌ عمود avatar غير موجود</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ خطأ في التحقق من العمود: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check uploads directory
$avatars_dir = 'uploads/avatars/';
if (is_dir($avatars_dir)) {
    echo "<p>✓ مجلد avatars موجود</p>";
    if (is_writable($avatars_dir)) {
        echo "<p>✓ مجلد avatars قابل للكتابة</p>";
    } else {
        echo "<p>❌ مجلد avatars غير قابل للكتابة</p>";
    }
} else {
    echo "<p>❌ مجلد avatars غير موجود</p>";
}

// Show existing avatars
if (is_dir($avatars_dir)) {
    $files = glob($avatars_dir . '*');
    if (!empty($files)) {
        echo "<p>📷 الصور الموجودة (" . count($files) . "):</p>";
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li>" . basename($file) . " (" . filesize($file) . " bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>📷 لا توجد صور حالياً</p>";
    }
}

// Test form
echo "<h3>اختبار رفع صورة</h3>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_avatar" accept="image/*" required>
    <button type="submit" name="test_upload">رفع الصورة</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_upload'])) {
    if (isset($_FILES['test_avatar']) && $_FILES['test_avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['test_avatar'];
        echo "<p>📁 تم استلام الملف: " . htmlspecialchars($file['name']) . "</p>";
        echo "<p>📏 الحجم: " . $file['size'] . " bytes</p>";
        echo "<p>📄 النوع: " . htmlspecialchars($file['type']) . "</p>";
        echo "<p>📍 المسار المؤقت: " . htmlspecialchars($file['tmp_name']) . "</p>";
        
        // Test moving file
        $filename = 'test_' . time() . '_' . basename($file['name']);
        $target_path = $avatars_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            echo "<p style='color: green;'>✓ تم رفع الملف بنجاح إلى: " . htmlspecialchars($target_path) . "</p>";
            echo "<img src='" . htmlspecialchars($target_path) . "' width='100' height='100' style='border: 1px solid #ccc;'>";
        } else {
            echo "<p style='color: red;'>❌ فشل في نقل الملف</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ لم يتم اختيار ملف أو حدث خطأ</p>";
    }
}
?>
