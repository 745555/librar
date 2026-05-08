<?php
// order_form.php - نموذج طلب إعارة كتاب (قابل للطباعة)
session_start();
require_once 'database.php';

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS faculty_borrowings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_name VARCHAR(100) NOT NULL,
        faculty_id VARCHAR(50) NOT NULL,
        faculty_department VARCHAR(100) NOT NULL,
        faculty_phone VARCHAR(20),
        faculty_email VARCHAR(100),
        book_title VARCHAR(255) NOT NULL,
        book_isbn VARCHAR(50),
        borrow_date DATE NOT NULL,
        expected_return_date DATE NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) {}

$message = '';
$last_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_borrowing'])) {
    $stmt = $pdo->prepare("INSERT INTO faculty_borrowings (faculty_name, faculty_id, faculty_department, faculty_phone, faculty_email, book_title, book_isbn, borrow_date, expected_return_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['faculty_name'],
        $_POST['faculty_id'],
        $_POST['faculty_department'],
        $_POST['faculty_phone'],
        $_POST['faculty_email'],
        $_POST['book_title'],
        $_POST['book_isbn'],
        $_POST['borrow_date'],
        $_POST['expected_return_date'],
        $_POST['notes']
    ]);
    
    $last_id = $pdo->lastInsertId();
    $message = '<div class="alert success">✓ تم تسجيل طلب الإعارة بنجاح! رقم الطلب: ' . $last_id . '</div>';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج طلب إعارة كتاب - مكتبة الكلية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background: #f0f4f8;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #0f2b3d, #1a4a6e);
            color: white;
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card h2 {
            color: #0f2b3d;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2c7da0;
            display: inline-block;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
        }
        
        .required::after {
            content: " *";
            color: #ef4444;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-primary {
            background: #2c7da0;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
        }
        
        .btn-primary:hover {
            background: #1f5e7a;
        }
        
        .btn-pdf {
            background: #dc2626;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
        }
        
        .btn-pdf:hover {
            background: #b91c1c;
        }
        
        .btn-back {
            background: #64748b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert.success {
            background: #d1fae5;
            color: #065f46;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            .header {
                background: #0f2b3d;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header no-print">
        <h1><i class="fas fa-book"></i> نموذج طلب إعارة كتاب</h1>
        <p>يرجى تعبئة النموذج التالي لطلب استعارة كتاب</p>
    </div>
    
    <?php if($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>
    
    <div class="card">
        <h2><i class="fas fa-file-alt"></i> بيانات طلب الإعارة</h2>
        <form method="POST" action="" id="borrowForm">
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-user"></i> اسم عضو هيئة التدريس</label>
                    <input type="text" name="faculty_name" required placeholder="مثال: د. أحمد محمد">
                </div>
                <div class="form-group">
                    <label class="required"><i class="fas fa-id-card"></i> الرقم الجامعي</label>
                    <input type="text" name="faculty_id" required placeholder="مثال: 2024001">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-building"></i> القسم</label>
                    <input type="text" name="faculty_department" required placeholder="مثال: قسم الحاسب الآلي">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> رقم الجوال</label>
                    <input type="tel" name="faculty_phone" placeholder="مثال: 05xxxxxxxx">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                    <input type="email" name="faculty_email" placeholder="example@university.edu">
                </div>
                <div class="form-group">
                    <label class="required"><i class="fas fa-book"></i> عنوان الكتاب</label>
                    <input type="text" name="book_title" required placeholder="عنوان الكتاب المراد استعارته">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-barcode"></i> رقم ISBN</label>
                    <input type="text" name="book_isbn" placeholder="رقم ISBN إن وجد">
                </div>
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar"></i> تاريخ الإعارة</label>
                    <input type="date" name="borrow_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar-check"></i> تاريخ الإرجاع المتوقع</label>
                    <input type="date" name="expected_return_date" required value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-sticky-note"></i> ملاحظات</label>
                    <textarea name="notes" rows="2" placeholder="أي ملاحظات إضافية"></textarea>
                </div>
            </div>
            
            <div class="btn-group no-print">
                <button type="submit" name="save_borrowing" class="btn-primary">
                    <i class="fas fa-save"></i> حفظ وتسجيل
                </button>
                <button type="button" class="btn-pdf" onclick="openOrderPrintForm()">
                    <i class="fas fa-print"></i> طباعة النموذج
                </button>
            </div>
        </form>
        
        <div style="margin-top: 20px; text-align: center;" class="no-print">
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-right"></i> العودة إلى الرئيسية
            </a>
        </div>
    </div>
</div>

<!-- Hidden print form (loads print_form.php in new tab) -->
<form id="printOrderForm" action="print_form.php?auto=1" method="POST" target="_blank" style="display:none;">
    <input type="hidden" name="faculty_name">
    <input type="hidden" name="faculty_id">
    <input type="hidden" name="faculty_department">
    <input type="hidden" name="faculty_phone">
    <input type="hidden" name="faculty_email">
    <input type="hidden" name="book_title">
    <input type="hidden" name="book_isbn">
    <input type="hidden" name="borrow_date">
    <input type="hidden" name="expected_return_date">
    <input type="hidden" name="notes">
</form>

<script>
function openOrderPrintForm() {
    const getVal = (selector) => document.querySelector(selector)?.value ?? '';
    const form = document.getElementById('printOrderForm');
    if (!form) return;

    form.querySelector('input[name="faculty_name"]').value = getVal('input[name="faculty_name"]');
    form.querySelector('input[name="faculty_id"]').value = getVal('input[name="faculty_id"]');
    form.querySelector('input[name="faculty_department"]').value = getVal('input[name="faculty_department"]');
    form.querySelector('input[name="faculty_phone"]').value = getVal('input[name="faculty_phone"]');
    form.querySelector('input[name="faculty_email"]').value = getVal('input[name="faculty_email"]');
    form.querySelector('input[name="book_title"]').value = getVal('input[name="book_title"]');
    form.querySelector('input[name="book_isbn"]').value = getVal('input[name="book_isbn"]');
    form.querySelector('input[name="borrow_date"]').value = getVal('input[name="borrow_date"]');
    form.querySelector('input[name="expected_return_date"]').value = getVal('input[name="expected_return_date"]');
    form.querySelector('input[name="notes"]').value = document.querySelector('textarea[name="notes"]')?.value ?? '';

    form.submit();
}

// Auto-fill today's date if empty
document.addEventListener('DOMContentLoaded', function() {
    const borrowDate = document.querySelector('input[name="borrow_date"]');
    const returnDate = document.querySelector('input[name="expected_return_date"]');
    
    if(borrowDate && !borrowDate.value) {
        borrowDate.value = '<?php echo date('Y-m-d'); ?>';
    }
    if(returnDate && !returnDate.value) {
        const today = new Date();
        const returnDateValue = new Date(today.setDate(today.getDate() + 14));
        returnDate.value = returnDateValue.toISOString().split('T')[0];
    }
});
</script>
</body>
</html>