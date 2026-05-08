<?php
// print_form.php - Print current order form
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    // If not JSON, try POST
    $data = $_POST;
}

if (empty($data['faculty_name'])) {
    // Default empty data
    $data = [
        'faculty_name' => '',
        'faculty_id' => '',
        'faculty_department' => '',
        'faculty_phone' => '',
        'faculty_email' => '',
        'book_title' => '',
        'book_isbn' => '',
        'borrow_date' => date('Y-m-d'),
        'expected_return_date' => date('Y-m-d', strtotime('+14 days')),
        'notes' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج إعارة كتاب - <?php echo htmlspecialchars($data['faculty_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            padding: 0;
            background: white;
            direction: rtl;
        }

        @page {
            size: A4;
            margin: 12mm 10mm;
        }
        
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none;
            }
        }
        
        .print-container {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 26px 22px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c7da0;
        }
        
        .header h1 {
            color: #0f2b3d;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #64748b;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        .section-title {
            background: #2c7da0;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        
        .info-label {
            width: 160px;
            font-weight: bold;
            color: #475569;
        }
        
        .info-value {
            flex: 1;
            color: #1e293b;
        }
        
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 10px;
        }

        .stamp-row {
            margin-top: 22px;
            display: flex;
            justify-content: center;
        }

        .stamp-box {
            width: 280px;
            height: 54px;
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 700;
            font-size: 0.95rem;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }

        .order-meta {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            color: #0f2b3d;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .print-btn {
            background: #2c7da0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="print-container">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
        <button class="print-btn" onclick="window.close()" style="background: #64748b;">إغلاق</button>
    </div>
    
    <div class="header">
        <h1>📚 نموذج إعارة كتاب</h1>
        <p>مكتبة الكلية - نظام إعارة أعضاء هيئة التدريس</p>
        <div class="order-meta"><i class="fas fa-hashtag"></i> رقم الطلب: غير محفوظ</div>
    </div>
    
    <div class="section">
        <div class="section-title">معلومات المستعير</div>
        <div class="info-row">
            <div class="info-label">اسم عضو هيئة التدريس:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['faculty_name']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">الرقم الجامعي:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['faculty_id']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">القسم:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['faculty_department']); ?></div>
        </div>
        <?php if(!empty($data['faculty_phone'])): ?>
        <div class="info-row">
            <div class="info-label">رقم الجوال:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['faculty_phone']); ?></div>
        </div>
        <?php endif; ?>
        <?php if(!empty($data['faculty_email'])): ?>
        <div class="info-row">
            <div class="info-label">البريد الإلكتروني:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['faculty_email']); ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <div class="section-title">معلومات الكتاب</div>
        <div class="info-row">
            <div class="info-label">عنوان الكتاب:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['book_title']); ?></div>
        </div>
        <?php if(!empty($data['book_isbn'])): ?>
        <div class="info-row">
            <div class="info-label">رقم ISBN:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['book_isbn']); ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <div class="section-title">تفاصيل الإعارة</div>
        <div class="info-row">
            <div class="info-label">تاريخ الإعارة:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['borrow_date']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">تاريخ الإرجاع المتوقع:</div>
            <div class="info-value"><?php echo htmlspecialchars($data['expected_return_date']); ?></div>
        </div>
        <?php if(!empty($data['notes'])): ?>
        <div class="info-row">
            <div class="info-label">ملاحظات:</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($data['notes'])); ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="signature">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>توقيع المستعير</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>توقيع أمين المكتبة</div>
        </div>
    </div>

    <div class="stamp-row">
        <div class="stamp-box"><i class="fas fa-print" style="margin-left:8px;"></i>ختم أمين المكتبة</div>
    </div>
    
    <div class="footer">
        <p>هذا النموذج يؤكد استعارة الكتاب من مكتبة الكلية. يرجى إعادة الكتاب في التاريخ المحدد.</p>
        <p>تاريخ الطباعة: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</div>

<?php if (isset($_GET['auto']) && (string)$_GET['auto'] === '1'): ?>
<script>
    window.onload = function () {
        window.print();
    };
</script>
<?php endif; ?>
</body>
</html>