<?php
// edit_project.php - Edit project
require_once 'database.php';
$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();

$id = $_GET['id'] ?? 0;
$project = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$project->execute([$id]);
$project = $project->fetch();

if(!$project) {
    header("Location: index.php");
    exit();
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY name_ar")->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archive_number = trim($_POST['archive_number']);
    $project_name = trim($_POST['project_name']);
    $supervisor = trim($_POST['supervisor_name']);
    $student_name = trim($_POST['student_name']);
    $semester = trim($_POST['semester']);
    $year = $_POST['year'];
    $department_id = $_POST['department_id'];
    
    $stmt = $pdo->prepare("UPDATE projects SET archive_number=?, project_name=?, supervisor=?, student_name=?, semester=?, year=?, department_id=? WHERE id=?");
    $stmt->execute([$archive_number, $project_name, $supervisor, $student_name, $semester, $year, $department_id, $id]);
    $message = '<div class="alert success">✓ تم تحديث المشروع بنجاح</div>';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل مشروع</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $css_version; ?>">
</head>
<body class="standalone-page">
<div class="standalone-container">
    <div class="standalone-card">
        <h1 class="standalone-title"><i class="fas fa-edit"></i> تعديل مشروع</h1>
        <div class="standalone-subtitle">تعديل بيانات المشروع</div>
        
        <?php echo $message; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>📋 الرقم الأرشيفي</label>
                <input type="text" name="archive_number" required value="<?php echo htmlspecialchars($project['archive_number']); ?>">
            </div>
            <div class="form-group">
                <label>📌 اسم المشروع</label>
                <input type="text" name="project_name" required value="<?php echo htmlspecialchars($project['project_name']); ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>👨‍🏫 المشرف</label>
                    <input type="text" name="supervisor_name" required value="<?php echo htmlspecialchars($project['supervisor']); ?>">
                </div>
                <div class="form-group">
                    <label>👨‍🎓 الطالب</label>
                    <input type="text" name="student_name" required value="<?php echo htmlspecialchars($project['student_name']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>📅 الفصل الدراسي</label>
                    <select name="semester" required>
                        <option value="الربيعي" <?php echo $project['semester'] == 'الربيعي' ? 'selected' : ''; ?>>الربيعي</option>
                        <option value="الخريفي" <?php echo $project['semester'] == 'الخريفي' ? 'selected' : ''; ?>>الخريفي</option>
                        <option value="الصيفي" <?php echo $project['semester'] == 'الصيفي' ? 'selected' : ''; ?>>الصيفي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>📅 السنة</label>
                    <select name="year" required>
                        <?php for($y = 2020; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $project['year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>🏢 القسم</label>
                <select name="department_id" required>
                    <?php foreach($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>" <?php echo $project['department_id'] == $dept['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept['name_ar']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
        </form>
        <a href="index.php#projects-page" class="btn-secondary"><i class="fas fa-arrow-right"></i> العودة إلى المشاريع</a>
    </div>
</div>
</body>
</html>