<?php
// add_book.php - Add new book with description
require_once 'database.php';
$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();

$message = '';
$error = '';

$departments = $pdo->query("SELECT * FROM departments ORDER BY name_ar")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = trim($_POST['isbn']);
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $publisher = trim($_POST['publisher']);
    $year = $_POST['year'];
    $department_type = $_POST['department_type'];
    
    // تحديد القسم بناءً على الاختيار
    if ($department_type == 'existing') {
        $department_id = $_POST['department_id'];
        $custom_department = null;
    } else {
        $department_id = null;
        $custom_department = trim($_POST['custom_department']);
        if (empty($custom_department)) {
            $error = '<div class="alert error">❌ الرجاء إدخال اسم القسم</div>';
        }
    }
    
    $quantity = $_POST['quantity'];
    $description = trim($_POST['description']);
    
    if(empty($isbn) || empty($book_title) || empty($author) || empty($year)) {
        $error = '<div class="alert error">❌ الرجاء تعبئة جميع الحقول المطلوبة</div>';
    } else if (empty($department_id) && empty($custom_department)) {
        $error = '<div class="alert error">❌ الرجاء اختيار أو إدخال القسم</div>';
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
            $check->execute([$isbn]);
            if($check->fetch()) {
                $error = '<div class="alert error">❌ هذا الرقم موجود بالفعل</div>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO books (isbn, book_title, author, publisher, publication_year, department_id, custom_department, quantity, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$isbn, $book_title, $author, $publisher, $year, $department_id, $custom_department, $quantity, $description]);
                $message = '<div class="alert success">✓ تم إضافة الكتاب بنجاح</div>';
            }
        } catch(PDOException $e) {
            $error = '<div class="alert error">❌ حدث خطأ: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة كتاب جديد</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $css_version; ?>">
    <style>
        .radio-group { display: flex; gap: 20px; margin-top: 8px; }
        .radio-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; cursor: pointer; }
        .dept-existing, .dept-custom { transition: all 0.3s; }
        .hidden { display: none; }
        .hint-small { color: #64748b; display: block; margin-top: 5px; }
    </style>
</head>
<body class="standalone-page">
<div class="standalone-container">
    <div class="standalone-card">
        <h1 class="standalone-title"><i class="fas fa-book"></i> إضافة كتاب جديد</h1>
        <div class="standalone-subtitle">الرقم الأرشيفي للكتاب هو رقم ISBN</div>
        
        <?php echo $message; ?>
        <?php echo $error; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>📚 الرقم الأرشيفي (ISBN) *</label>
                    <input type="text" name="isbn" required placeholder="978-999-123-456-7">
                </div>
                <div class="form-group">
                    <label>📖 عنوان الكتاب *</label>
                    <input type="text" name="book_title" required placeholder="عنوان الكتاب">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>✍️ المؤلف *</label>
                    <input type="text" name="author" required placeholder="اسم المؤلف">
                </div>
                <div class="form-group">
                    <label>🏛️ الناشر</label>
                    <input type="text" name="publisher" placeholder="اسم الناشر">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>📅 سنة النشر *</label>
                    <select name="year" required>
                        <option value="">اختر السنة</option>
                        <?php for($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🔢 الكمية المتاحة *</label>
                    <input type="number" name="quantity" required min="1" value="1">
                </div>
            </div>
            
            <!-- قسم اختيار القسم -->
            <div class="form-group">
                <label>🏢 القسم *</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="department_type" value="existing" checked onclick="toggleDepartment()"> اختيار من القائمة
                    </label>
                    <label>
                        <input type="radio" name="department_type" value="custom" onclick="toggleDepartment()"> إدخال قسم جديد
                    </label>
                </div>
                
                <!-- قائمة الأقسام الموجودة -->
                <div id="existing_dept" class="dept-existing">
                    <select name="department_id" class="mt-10">
                        <option value="">اختر القسم</option>
                        <?php foreach($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name_ar']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- حقل إدخال قسم جديد -->
                <div id="custom_dept" class="dept-custom hidden">
                    <input type="text" name="custom_department" placeholder="أدخل اسم القسم (مثال: قسم إدارة الأعمال)" class="mt-10">
                    <small class="hint-small">📝 يمكنك إضافة قسم غير موجود في القائمة (مثل قسم خارج الكلية)</small>
                </div>
            </div>
            
            <!-- حقل النبذة -->
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> 📝 نبذة مختصرة عن الكتاب</label>
                <textarea name="description" rows="4" placeholder="أدخل نبذة مختصرة عن الكتاب... (محتوى، أهداف، الفئة المستهدفة...)"></textarea>
                <small class="hint-small">✏️ يمكنك كتابة ملخص قصير عن الكتاب (اختياري)</small>
            </div>
            
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> إضافة الكتاب</button>
        </form>
        <a href="index.php" class="btn-secondary"><i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم</a>
    </div>
</div>

<script>
function toggleDepartment() {
    const existingDiv = document.getElementById('existing_dept');
    const customDiv = document.getElementById('custom_dept');
    const selected = document.querySelector('input[name="department_type"]:checked').value;
    
    if (selected === 'existing') {
        existingDiv.classList.remove('hidden');
        customDiv.classList.add('hidden');
    } else {
        existingDiv.classList.add('hidden');
        customDiv.classList.remove('hidden');
    }
}
</script>
</body>
</html>