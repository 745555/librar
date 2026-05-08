<?php
// add_project.php - Add new project with manual archive number entry
require_once 'database.php';
$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archive_number = trim($_POST['archive_number']);
    $project_name = trim($_POST['project_name']);
    $supervisor_name = trim($_POST['supervisor_name']);
    $student_name = trim($_POST['student_name']);
    $semester = trim($_POST['semester']);
    $year = $_POST['year'];
    $department_id = $_POST['department_id'];
    $file_path = $_POST['file_path'] ?? null;
    
    // Validation
    $errors = [];
    if (empty($archive_number)) {
        $errors[] = "الرجاء إدخال الرقم الأرشيفي";
    }
    if (empty($project_name)) {
        $errors[] = "الرجاء إدخال اسم المشروع";
    }
    if (empty($supervisor_name)) {
        $errors[] = "الرجاء إدخال اسم المشرف";
    }
    if (empty($student_name)) {
        $errors[] = "الرجاء إدخال اسم الطالب";
    }
    if (empty($semester)) {
        $errors[] = "الرجاء اختيار الفصل الدراسي";
    }
    if (empty($year)) {
        $errors[] = "الرجاء اختيار السنة";
    }
    
    if (empty($errors)) {
        try {
            // Check if archive number already exists
            $check = $pdo->prepare("SELECT id FROM projects WHERE archive_number = ?");
            $check->execute([$archive_number]);
            if ($check->fetch()) {
                $error = '<div class="alert error">❌ هذا الرقم الأرشيفي موجود بالفعل. الرجاء استخدام رقم مختلف.</div>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO projects (archive_number, project_name, supervisor, student_name, semester, year, department_id, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$archive_number, $project_name, $supervisor_name, $student_name, $semester, $year, $department_id, $file_path]);
                
                $message = '<div class="alert success">✓ تم إضافة المشروع بنجاح! الرقم الأرشيفي: ' . htmlspecialchars($archive_number) . '</div>';
                
                // Clear form after successful submission (optional)
                // You can uncomment the line below to redirect after success
                // header("Location: add_project.php?success=1");
                // exit();
            }
        } catch(PDOException $e) {
            $error = '<div class="alert error">❌ حدث خطأ: ' . $e->getMessage() . '</div>';
        }
    } else {
        $error = '<div class="alert error">❌ ' . implode('<br>', $errors) . '</div>';
    }
}

// Get departments for dropdown
$departments = $pdo->query("SELECT * FROM departments ORDER BY name_ar")->fetchAll();

// Get existing archive numbers for reference
$existing_archives = $pdo->query("SELECT archive_number FROM projects ORDER BY archive_number")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مشروع جديد - نظام مكتبة الكلية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $css_version; ?>">
</head>
<body class="standalone-page">
<div class="standalone-container">
    <div class="standalone-card">
        <h1 class="standalone-title"><i class="fas fa-plus-circle"></i> إضافة مشروع جديد</h1>
        <div class="standalone-subtitle">قم بإدخال بيانات المشروع مع الرقم الأرشيفي الخاص به</div>
        
        <?php 
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="alert success">✓ تم إضافة المشروع بنجاح!</div>';
        }
        echo $message;
        echo $error;
        ?>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i> 
            <strong>تعليمات:</strong><br>
            يمكنك إدخال الرقم الأرشيفي يدوياً حسب النظام المتبع في مؤسستك. يرجى التأكد من أن الرقم فريد وغير مكرر.<br>
            <strong>أمثلة مقترحة:</strong> CS-2024-001, ENG-2025-015, PRJ-2023-042
        </div>
        
        <form method="POST" action="" id="projectForm">
            <div class="form-group">
                <label class="required"><i class="fas fa-hashtag"></i> الرقم الأرشيفي *</label>
                <input type="text" name="archive_number" required 
                       placeholder="مثال: CS-2024-001 أو ENG-2025-015"
                       value="<?php echo htmlspecialchars($_POST['archive_number'] ?? ''); ?>">
                <div class="example"><i class="fas fa-lightbulb"></i> نصيحة: استخدم تنسيقاً موحداً مثل [رمز القسم]-[السنة]-[رقم تسلسلي]</div>
            </div>
            
            <div class="form-group">
                <label class="required"><i class="fas fa-tag"></i> اسم المشروع *</label>
                <input type="text" name="project_name" required 
                       placeholder="أدخل اسم المشروع كاملاً"
                       value="<?php echo htmlspecialchars($_POST['project_name'] ?? ''); ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-chalkboard-user"></i> اسم المشرف *</label>
                    <input type="text" name="supervisor_name" required 
                           placeholder="مثال: د. أحمد محمد"
                           value="<?php echo htmlspecialchars($_POST['supervisor_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="required"><i class="fas fa-user-graduate"></i> اسم الطالب *</label>
                    <input type="text" name="student_name" required 
                           placeholder="مثال: علي حسن"
                           value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar-alt"></i> الفصل الدراسي *</label>
                    <select name="semester" required>
                        <option value="">اختر الفصل</option>
                        <option value="الربيعي" <?php echo (($_POST['semester'] ?? '') == 'الربيعي') ? 'selected' : ''; ?>>الفصل الربيعي (Spring)</option>
                        <option value="الخريفي" <?php echo (($_POST['semester'] ?? '') == 'الخريفي') ? 'selected' : ''; ?>>الفصل الخريفي (Fall)</option>
                        <option value="الصيفي" <?php echo (($_POST['semester'] ?? '') == 'الصيفي') ? 'selected' : ''; ?>>الفصل الصيفي (Summer)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar"></i> السنة *</label>
                    <select name="year" required>
                        <option value="">اختر السنة</option>
                        <?php for($y = 2020; $y <= 2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo (($_POST['year'] ?? '') == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="required"><i class="fas fa-building"></i> القسم الأكاديمي *</label>
                <select name="department_id" required>
                    <option value="">اختر القسم</option>
                    <?php foreach($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>" <?php echo (($_POST['department_id'] ?? '') == $dept['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dept['name_ar']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-file"></i> مسار الملف (اختياري)</label>
                <input type="text" name="file_path" placeholder="مثال: uploads/project_file.pdf"
                       value="<?php echo htmlspecialchars($_POST['file_path'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> إضافة المشروع</button>
        </form>
        
        <?php if(count($existing_archives) > 0): ?>
        <div class="existing-list">
            <h4><i class="fas fa-list"></i> الأرقام الأرشيفية الموجودة:</h4>
            <?php foreach($existing_archives as $archive): ?>
                <span class="archive-item"><?php echo htmlspecialchars($archive['archive_number']); ?></span>
            <?php endforeach; ?>
            <div class="example mt-10">⚠️ تأكد من أن الرقم الذي ستضيفه غير مكرر مع الأرقام أعلاه</div>
        </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn-secondary"><i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم</a>
    </div>
</div>

<script>
// Real-time duplicate check
const archiveInput = document.querySelector('input[name="archive_number"]');
const existingArchives = <?php echo json_encode(array_column($existing_archives, 'archive_number')); ?>;

if(archiveInput) {
    archiveInput.addEventListener('input', function() {
        const value = this.value.trim();
        if(value && existingArchives.includes(value)) {
            this.style.borderColor = '#ef4444';
            this.style.backgroundColor = '#fef2f2';
            // Add warning message if not exists
            let warning = document.getElementById('duplicate-warning');
            if(!warning) {
                warning = document.createElement('div');
                warning.id = 'duplicate-warning';
                warning.className = 'example';
                warning.style.color = '#ef4444';
                warning.style.marginTop = '5px';
                this.parentNode.appendChild(warning);
            }
            warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ⚠️ هذا الرقم الأرشيفي موجود بالفعل! الرجاء استخدام رقم مختلف.';
        } else {
            this.style.borderColor = '#cbd5e1';
            this.style.backgroundColor = 'white';
            const warning = document.getElementById('duplicate-warning');
            if(warning) warning.remove();
        }
    });
}
</script>
</body>
</html>