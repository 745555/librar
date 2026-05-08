<?php
// index.php - الملف الرئيسي
session_start();

if(!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';
require_once 'includes/functions.php';
$css_version = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();

// Handle delete book
if (isset($_GET['delete_book'])) {
    $id = (int)$_GET['delete_book'];
    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        echo '<script>alert("تم حذف الكتاب بنجاح"); window.location.href="index.php?page=books";</script>';
        exit();
    } catch(PDOException $e) {
        echo '<script>alert("لا يمكن حذف الكتاب لأنه مرتبط بإعارات");</script>';
    }
}

// Handle delete project
if (isset($_GET['delete_project'])) {
    $id = (int)$_GET['delete_project'];
    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        echo '<script>alert("تم حذف المشروع بنجاح"); window.location.href="index.php?page=projects";</script>';
        exit();
    } catch(PDOException $e) {
        echo '<script>alert("لا يمكن حذف المشروع");</script>';
    }
}

$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'books', 'projects', 'borrow', 'staff', 'account'];
$page = in_array($page, $allowed_pages) ? $page : 'dashboard';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الأرشفة - <?php echo ucfirst($page); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $css_version; ?>">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <?php
        switch($page) {
            case 'dashboard':
                include 'dashboard.php';
                break;
            case 'books':
                include 'books_list_simple.php';
                break;
            case 'projects':
                include 'projects_list_simple.php';
                break;
            case 'borrow':
                include 'borrow_form.php';
                break;
            case 'staff':
                if($_SESSION['staff_role'] == 'admin') include 'staff_management.php';
                else echo '<div class="alert error">غير مصرح لك بالدخول</div>';
                break;
            case 'account':
                include 'account.php';
                break;
        }
        ?>
    </main>
</div>

<script>
// عرض الوقت الحقيقي
function updateTime() {
    const now = new Date();
    const timeString = `${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
    const timeElement = document.getElementById('currentTimeSidebar');
    if(timeElement) timeElement.innerHTML = timeString;
}
updateTime();
setInterval(updateTime, 1000);

// دوال البحث والحذف
function searchBook() {
    const query = document.getElementById('searchBookInput')?.value.trim();
    if(!query) {
        const container = document.getElementById('bookSearchResult');
        if(container) container.innerHTML = '';
        return;
    }
    fetch(`search.php?type=book&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('bookSearchResult');
            if(!container) return;
            if(!data?.length) { container.innerHTML = '<div class="alert error">لم يتم العثور على كتاب</div>'; return; }
            container.innerHTML = data.map(book => `<div class="search-result-item" onclick='showBookPreview(${JSON.stringify(book)})'>📖 ${book.book_title}<br><small>${book.author} | ${book.isbn || '—'}</small></div>`).join('');
        });
}

function showBookPreview(book) {
    const modal = document.createElement('div');
    modal.className = 'preview-modal';
    modal.innerHTML = `
        <div class="preview-content">
            <div class="preview-header">
                <i class="fas fa-book"></i>
                <h3>معاينة الكتاب</h3>
            </div>
            <div class="preview-body">
                <div class="preview-row"><span>📖 العنوان:</span><span>${book.book_title}</span></div>
                <div class="preview-row"><span>✍️ المؤلف:</span><span>${book.author}</span></div>
                <div class="preview-row"><span>🏛️ الناشر:</span><span>${book.publisher || '—'}</span></div>
                <div class="preview-row"><span>📅 السنة:</span><span>${book.year || 'غير محدد'}</span></div>
                <div class="preview-row"><span>🔢 الرقم الأرشيفي:</span><span>${book.isbn || '—'}</span></div>
                <div class="preview-row"><span>📚 الكمية:</span><span>${book.quantity} نسخة</span></div>
                <div class="preview-row"><span>🏢 القسم:</span><span>${book.custom_department || book.dept_name || '—'}</span></div>
                <div class="preview-description"><strong>📝 نبذة:</strong><p>${book.description || 'لا توجد نبذة'}</p></div>
            </div>
            <button onclick="this.closest('.preview-modal').remove()" class="preview-close">إغلاق</button>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', e => { if(e.target === modal) modal.remove(); });
}

function deleteBook(id, title) {
    if(confirm(`هل أنت متأكد من حذف "${title}"؟`)) window.location.href = `?delete_book=${id}`;
}

function deleteProject(id, name) {
    if(confirm(`هل أنت متأكد من حذف المشروع "${name}"؟`)) window.location.href = `?delete_project=${id}`;
}

// التنقل بين الصفحات - إصلاح المشكلة
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = this.getAttribute('data-page');
        if(page) {
            window.location.href = `index.php?page=${page}`;
        }
    });
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>