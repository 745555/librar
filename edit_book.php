<?php
// edit_book.php - Edit book with description
require_once 'database.php';
$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();

$id = $_GET['id'] ?? 0;
$book = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$book->execute([$id]);
$book = $book->fetch();

if(!$book) {
    header("Location: index.php");
    exit();
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY name_ar")->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = trim($_POST['isbn']);
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $publisher = trim($_POST['publisher']);
    $publication_year = $_POST['year'];
    $department_id = $_POST['department_id'];
    $quantity = $_POST['quantity'];
    $description = trim($_POST['description']);
    
    $stmt = $pdo->prepare("UPDATE books SET isbn=?, book_title=?, author=?, publisher=?, publication_year=?, department_id=?, quantity=?, description=? WHERE id=?");
    $stmt->execute([$isbn, $book_title, $author, $publisher, $publication_year, $department_id, $quantity, $description, $id]);
    $message = '<div class="alert success">✓ تم تحديث الكتاب بنجاح</div>';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل كتاب</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $css_version; ?>">
</head>
<body class="standalone-page">
<div class="standalone-container">
    <div class="standalone-card">
        <h1 class="standalone-title"><i class="fas fa-edit"></i> تعديل كتاب</h1>
        <div class="standalone-subtitle">تعديل بيانات الكتاب</div>
        
        <?php echo $message; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>📚 الرقم الأرشيفي (ISBN)</label>
                    <input type="text" name="isbn" required value="<?php echo htmlspecialchars($book['isbn']); ?>">
                </div>
                <div class="form-group">
                    <label>📖 عنوان الكتاب</label>
                    <input type="text" name="book_title" required value="<?php echo htmlspecialchars($book['book_title']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>✍️ المؤلف</label>
                    <input type="text" name="author" required value="<?php echo htmlspecialchars($book['author']); ?>">
                </div>
                <div class="form-group">
                    <label>🏛️ الناشر</label>
                    <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>📅 سنة النشر</label>
                    <select name="year" required>
                        <?php for($y = 2000; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $book['publication_year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🏢 القسم</label>
                    <select name="department_id" required>
                        <?php foreach($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo $book['department_id'] == $dept['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept['name_ar']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>🔢 الكمية المتاحة</label>
                <input type="number" name="quantity" required min="1" value="<?php echo $book['quantity']; ?>">
            </div>
            
            <!-- حقل النبذة -->
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> 📝 نبذة مختصرة عن الكتاب</label>
                <textarea name="description" rows="4" placeholder="أدخل نبذة مختصرة عن الكتاب..."><?php echo htmlspecialchars($book['description']); ?></textarea>
                <small class="hint-small">✏️ يمكنك تعديل النبذة أو إضافة ملخص جديد</small>
            </div>
            
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
        </form>
        <a href="index.php#books-page" class="btn-secondary"><i class="fas fa-arrow-right"></i> العودة إلى الكتب</a>
    </div>
</div>
</body>
</html>