<?php
// borrow_form.php
require_once 'database.php';
require_once 'includes/functions.php';
// Handle new borrowing submission
$borrow_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_borrowing'])) {
    $faculty_name = trim($_POST['faculty_name']);
    $faculty_department = trim($_POST['faculty_department']);
    $faculty_contact = trim($_POST['faculty_contact']);
    $book_title = trim($_POST['book_title']);
    $book_isbn = trim($_POST['book_isbn']);
    $borrow_date = $_POST['borrow_date'];
    $expected_return_date = $_POST['expected_return_date'];
    $notes = trim($_POST['notes']);
    
    if (!empty($faculty_name) && !empty($faculty_department) && !empty($book_title)) {
        try {
            $insert = $pdo->prepare("INSERT INTO faculty_borrowings (faculty_name, faculty_department, faculty_contact, book_title, book_isbn, borrow_date, expected_return_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$faculty_name, $faculty_department, $faculty_contact, $book_title, $book_isbn, $borrow_date, $expected_return_date, $notes]);
            $borrow_message = '<div class="alert success">✓ تم تسجيل طلب الإعارة بنجاح!</div>';
        } catch(PDOException $e) {
            $borrow_message = '<div class="alert error">❌ حدث خطأ في تسجيل الطلب</div>';
        }
    } else {
        $borrow_message = '<div class="alert error">❌ الرجاء تعبئة جميع الحقول المطلوبة</div>';
    }
}

// Handle delete borrowing request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_borrowing'])) {
    $borrow_id = $_POST['borrow_id'];
    try {
        $delete = $pdo->prepare("DELETE FROM faculty_borrowings WHERE id = ?");
        $delete->execute([$borrow_id]);
        $borrow_message = '<div class="alert success">✓ تم حذف طلب الإعارة بنجاح!</div>';
    } catch(PDOException $e) {
        $borrow_message = '<div class="alert error">❌ حدث خطأ في حذف الطلب</div>';
    }
}

// Handle edit borrowing request
$editing_borrow = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_borrowing'])) {
    $borrow_id = $_POST['borrow_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM faculty_borrowings WHERE id = ?");
        $stmt->execute([$borrow_id]);
        $editing_borrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $borrow_message = '<div class="alert error">❌ حدث خطأ في جلب بيانات الطلب</div>';
    }
}

// Handle update borrowing request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_borrowing'])) {
    $borrow_id = $_POST['borrow_id'];
    $faculty_name = trim($_POST['faculty_name']);
    $faculty_department = trim($_POST['faculty_department']);
    $faculty_contact = trim($_POST['faculty_contact']);
    $book_title = trim($_POST['book_title']);
    $book_isbn = trim($_POST['book_isbn']);
    $borrow_date = $_POST['borrow_date'];
    $expected_return_date = $_POST['expected_return_date'];
    $notes = trim($_POST['notes']);
    
    if (!empty($faculty_name) && !empty($faculty_department) && !empty($book_title)) {
        try {
            $update = $pdo->prepare("UPDATE faculty_borrowings SET faculty_name = ?, faculty_department = ?, faculty_contact = ?, book_title = ?, book_isbn = ?, borrow_date = ?, expected_return_date = ?, notes = ? WHERE id = ?");
            $update->execute([$faculty_name, $faculty_department, $faculty_contact, $book_title, $book_isbn, $borrow_date, $expected_return_date, $notes, $borrow_id]);
            $borrow_message = '<div class="alert success">✓ تم تحديث طلب الإعارة بنجاح!</div>';
        } catch(PDOException $e) {
            $borrow_message = '<div class="alert error">❌ حدث خطأ في تحديث الطلب</div>';
        }
    } else {
        $borrow_message = '<div class="alert error">❌ الرجاء تعبئة جميع الحقول المطلوبة</div>';
    }
}

// Get recent borrowings
$faculty_borrowings = $pdo->query("SELECT id, faculty_name, book_title, borrow_date, expected_return_date FROM faculty_borrowings ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>

<style>
/* تحسينات نموذج الإعارة */
.borrow-container {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* تحسين بطاقة النموذج */
.card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.card h3 {
    color: #2c3e50;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
    display: inline-block;
    font-size: 24px;
}

.card h3 i {
    color: #667eea;
    margin-right: 10px;
}

/* تحسين التنبيهات */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    animation: slideIn 0.4s ease-out;
    font-weight: 500;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert.success {
    background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
    color: #1e3c2c;
    border-right: 4px solid #2d6a4f;
}

.alert.error {
    background: linear-gradient(135deg, #fda4a4 0%, #f87171 100%);
    color: #7f1d1d;
    border-right: 4px solid #dc2626;
}

/* تنسيق الحقول */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.form-group label.required::after {
    content: '*';
    color: #ef4444;
    margin-right: 4px;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.3s;
    font-family: inherit;
    background: white;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.form-group input:hover,
.form-group textarea:hover,
.form-group select:hover {
    border-color: #9ca3af;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

/* مجموعة الأزرار المحسنة */
.btn-group {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    flex: 1;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-primary:hover::before {
    width: 300px;
    height: 300px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-pdf {
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    flex: 1;
}

.btn-pdf::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-pdf:hover::before {
    width: 300px;
    height: 300px;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4);
}

/* تحسين الجدول */
.table-responsive {
    overflow-x: auto;
    border-radius: 16px;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 16px;
    overflow: hidden;
}

thead tr {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

th {
    padding: 15px;
    text-align: right;
    font-weight: 600;
    font-size: 14px;
}

td {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    color: #374151;
}

tbody tr {
    transition: all 0.3s;
}

tbody tr:hover {
    background: linear-gradient(90deg, #f9fafb 0%, #ffffff 100%);
    transform: scale(1.01);
}

.text-center {
    text-align: center;
}

/* حقول التاريخ المحسنة */
input[type="date"] {
    position: relative;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    padding: 5px;
    border-radius: 8px;
    transition: background 0.3s;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
    background: #e5e7eb;
}

/* شريط التمرير المخصص */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* استجابة للشاشات الصغيرة */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .card {
        padding: 20px;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-primary,
    .btn-pdf {
        width: 100%;
    }
    
    .card h3 {
        font-size: 20px;
    }
    
    th, td {
        padding: 10px;
        font-size: 13px;
    }
}

/* تأثير تحميل للحقول */
.form-group input,
.form-group textarea {
    position: relative;
    background: white;
}

.form-group input:focus,
.form-group textarea:focus {
    animation: pulse 0.3s ease-out;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
    100% {
        transform: scale(1);
    }
}

/* إضافة أيقونات داخل الحقول */
.form-group input[type="text"],
.form-group input[type="tel"],
.form-group input[type="email"] {
    background-repeat: no-repeat;
    background-position: left 12px center;
    background-size: 18px;
    padding-left: 40px;
}

.form-group input[name="faculty_name"] {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>');
}

.form-group input[name="faculty_id"] {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>');
}

.form-group input[name="faculty_phone"] {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>');
}

.form-group input[name="faculty_email"] {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>');
}

/* علامات تبويب (اختيارية محسنة) */
.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* أزرار التعديل والحذف */
.btn-edit {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-left: 5px;
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-left: 5px;
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
}

/* تحسينات الطباعة */
@media print {
    .btn-group {
        display: none;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .form-group input,
    .form-group textarea {
        border: 1px solid #ddd;
        background: white;
    }
    
    .btn-edit,
    .btn-delete {
        display: none;
    }
}

</style>

<div class="borrow-container">
<div class="card">
    <h3><i class="fas fa-pen-alt"></i> <?php echo $editing_borrow ? 'تعديل طلب الإعارة' : 'نموذج طلب إعارة'; ?></h3>
    <?php echo $borrow_message; ?>
    
    <form method="POST" action="" id="borrowForm">
        <?php if ($editing_borrow): ?>
            <input type="hidden" name="borrow_id" value="<?php echo $editing_borrow['id']; ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label class="required">اسم عضو هيئة التدريس</label>
                <input type="text" name="faculty_name" required placeholder="د. أحمد محمد" autocomplete="off" value="<?php echo htmlspecialchars($editing_borrow['faculty_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="required">القسم</label>
                <input type="text" name="faculty_department" required placeholder="قسم الحاسب الآلي" autocomplete="off" value="<?php echo htmlspecialchars($editing_borrow['faculty_department'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>معلومات التواصل</label>
                <input type="text" name="faculty_contact" placeholder="رقم الهاتف أو البريد الإلكتروني" autocomplete="off" value="<?php echo htmlspecialchars($editing_borrow['faculty_contact'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="required">عنوان الكتاب</label>
                <input type="text" name="book_title" required placeholder="عنوان الكتاب" autocomplete="off" value="<?php echo htmlspecialchars($editing_borrow['book_title'] ?? ''); ?>">
            </div>
        <div class="form-row">
            <div class="form-group">
                <label>رقم ISBN</label>
                <input type="text" name="book_isbn" placeholder="رقم ISBN" autocomplete="off" value="<?php echo htmlspecialchars($editing_borrow['book_isbn'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="required">تاريخ الإعارة</label>
                <input type="date" name="borrow_date" required value="<?php echo htmlspecialchars($editing_borrow['borrow_date'] ?? date('Y-m-d')); ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="required">تاريخ الإرجاع المتوقع</label>
                <input type="date" name="expected_return_date" required value="<?php echo htmlspecialchars($editing_borrow['expected_return_date'] ?? date('Y-m-d', strtotime('+14 days'))); ?>">
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" rows="2" placeholder="ملاحظات إضافية"><?php echo htmlspecialchars($editing_borrow['notes'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <div class="btn-group">
            <?php if ($editing_borrow): ?>
                <button type="submit" name="update_borrowing" class="btn-primary">
                    <i class="fas fa-save"></i> تحديث الطلب
                </button>
                <button type="button" class="btn-pdf" onclick="cancelEdit()">
                    <i class="fas fa-times"></i> إلغاء التعديل
                </button>
            <?php else: ?>
                <button type="submit" name="save_borrowing" class="btn-primary">
                    <i class="fas fa-save"></i> حفظ وتسجيل
                </button>
                <button type="button" class="btn-pdf" onclick="openBorrowPrintForm()">
                    <i class="fas fa-print"></i> طباعة النموذج
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-history"></i> طلبات الإعارة المسجلة</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>عضو هيئة التدريس</th>
                    <th>الكتاب</th>
                    <th>تاريخ الإعارة</th>
                    <th>تاريخ الإرجاع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($faculty_borrowings) > 0): ?>
                    <?php foreach($faculty_borrowings as $fb): ?>
                    <tr>
                        <td><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($fb['faculty_name']); ?></td>
                        <td><i class="fas fa-book"></i> <?php echo htmlspecialchars($fb['book_title']); ?></td>
                        <td><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($fb['borrow_date']); ?></td>
                        <td><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($fb['expected_return_date']); ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline; margin: 0;">
                                <input type="hidden" name="borrow_id" value="<?php echo $fb['id']; ?>">
                                <button type="submit" name="edit_borrowing" class="btn-edit" onclick="return confirm('هل تريد تعديل هذا الطلب؟');">
                                    <i class="fas fa-edit"></i> تعديل
                                </button>
                                <button type="submit" name="delete_borrowing" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.');">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            <i class="fas fa-inbox"></i> لا توجد طلبات حالياً
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Hidden print form (loads print_form.php in new tab) -->
<form id="printBorrowForm" action="print_form.php?auto=1" method="POST" target="_blank" style="display:none;">
    <input type="hidden" name="faculty_name">
    <input type="hidden" name="faculty_department">
    <input type="hidden" name="faculty_contact">
    <input type="hidden" name="book_title">
    <input type="hidden" name="book_isbn">
    <input type="hidden" name="borrow_date">
    <input type="hidden" name="expected_return_date">
    <input type="hidden" name="notes">
</form>

<script>
function openBorrowPrintForm() {
    const getVal = (selector) => document.querySelector(selector)?.value ?? '';
    const printForm = document.getElementById('printBorrowForm');
    if (!printForm) return;

    printForm.querySelector('input[name="faculty_name"]').value = getVal('input[name="faculty_name"]');
    printForm.querySelector('input[name="faculty_department"]').value = getVal('input[name="faculty_department"]');
    printForm.querySelector('input[name="faculty_contact"]').value = getVal('input[name="faculty_contact"]');
    printForm.querySelector('input[name="book_title"]').value = getVal('input[name="book_title"]');
    printForm.querySelector('input[name="book_isbn"]').value = getVal('input[name="book_isbn"]');
    printForm.querySelector('input[name="borrow_date"]').value = getVal('input[name="borrow_date"]');
    printForm.querySelector('input[name="expected_return_date"]').value = getVal('input[name="expected_return_date"]');
    printForm.querySelector('input[name="notes"]').value = document.querySelector('textarea[name="notes"]')?.value ?? '';

    printForm.submit();
}

function cancelEdit() {
    window.location.href = window.location.pathname;
}

// إضافة تأثيرات إضافية للنموذج
document.addEventListener('DOMContentLoaded', function() {
    // تأثير تحريك للحقول عند التركيز
    const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateX(5px)';
        });
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateX(0)';
        });
    });
    
    // تحقق بسيط من تطابق البريد الإلكتروني (اختياري)
    const emailInput = document.querySelector('input[name="faculty_email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            if (email && !email.includes('@')) {
                this.style.borderColor = '#f87171';
                setTimeout(() => {
                    this.style.borderColor = '#e5e7eb';
                }, 2000);
            }
        });
    }
    
    // إضافة تأكيد قبل الطباعة
    const printBtn = document.querySelector('.btn-pdf');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            console.log('جاري تحضير النموذج للطباعة...');
        });
    }
});
</script>