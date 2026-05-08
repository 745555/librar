<?php
// account.php
$account_message = '';
$account_error = '';

$staff_id = (int)($_SESSION['staff_id'] ?? 0);
$staff_stmt = $pdo->prepare("SELECT id, username, full_name, email, phone, role, created_at, password FROM library_staff WHERE id = ?");
$staff_stmt->execute([$staff_id]);
$staff = $staff_stmt->fetch();

if (!$staff) {
    echo '<div class="alert error">تعذر تحميل بيانات الحساب.</div>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($full_name === '') {
        $account_error = '<div class="alert error">❌ الاسم الكامل مطلوب.</div>';
    } else {
        try {
            $update_stmt = $pdo->prepare("UPDATE library_staff SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $email, $phone, $staff_id]);
            $_SESSION['staff_name'] = $full_name;
            $account_message = '<div class="alert success">✓ تم تحديث بيانات الحساب بنجاح.</div>';
            $staff['full_name'] = $full_name;
            $staff['email'] = $email;
            $staff['phone'] = $phone;
        } catch (PDOException $e) {
            $account_error = '<div class="alert error">❌ حدث خطأ أثناء حفظ البيانات.</div>';
        }
    }
}

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            $account_error = '<div class="alert error">❌ يجب أن تكون الصورة بصيغة JPG, PNG, GIF, أو WebP.</div>';
        } elseif ($file['size'] > $max_size) {
            $account_error = '<div class="alert error">❌ حجم الصورة يجب أن لا يتجاوز 2 ميجابايت.</div>';
        } else {
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $staff_id . '_' . time() . '.' . $extension;
            $upload_path = 'uploads/avatars/' . $filename;
            
            // Delete old avatar if exists
            if (!empty($staff['avatar']) && file_exists('uploads/avatars/' . $staff['avatar'])) {
                unlink('uploads/avatars/' . $staff['avatar']);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                try {
                    $avatar_stmt = $pdo->prepare("UPDATE library_staff SET avatar = ? WHERE id = ?");
                    $avatar_stmt->execute([$filename, $staff_id]);
                    $_SESSION['staff_image'] = $filename;
                    $account_message = '<div class="alert success">✓ تم رفع الصورة الشخصية بنجاح.</div>';
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Unknown column') !== false) {
                        $account_error = '<div class="alert error">❌ عمود الصورة غير موجود. يرجى تشغيل صفحة الإعداد أولاً.</div>';
                    } else {
                        $account_error = '<div class="alert error">❌ حدث خطأ أثناء حفظ الصورة.</div>';
                    }
                }
            } else {
                $account_error = '<div class="alert error">❌ فشل رفع الصورة.</div>';
            }
        }
    } else {
        $account_error = '<div class="alert error">❌ يرجى اختيار صورة.</div>';
    }
}

// Get current avatar from database (with error handling)
try {
    $avatar_stmt = $pdo->prepare("SELECT avatar FROM library_staff WHERE id = ?");
    $avatar_stmt->execute([$staff_id]);
    $staff_data = $avatar_stmt->fetch();
    $staff['avatar'] = $staff_data['avatar'] ?? '';
} catch (PDOException $e) {
    // If avatar column doesn't exist, set empty
    $staff['avatar'] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $account_error = '<div class="alert error">❌ يرجى تعبئة جميع حقول كلمة المرور.</div>';
    } elseif (!password_verify($current_password, $staff['password'])) {
        $account_error = '<div class="alert error">❌ كلمة المرور الحالية غير صحيحة.</div>';
    } elseif (strlen($new_password) < 6) {
        $account_error = '<div class="alert error">❌ كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.</div>';
    } elseif ($new_password !== $confirm_password) {
        $account_error = '<div class="alert error">❌ تأكيد كلمة المرور غير مطابق.</div>';
    } else {
        try {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $pass_stmt = $pdo->prepare("UPDATE library_staff SET password = ? WHERE id = ?");
            $pass_stmt->execute([$new_hash, $staff_id]);
            $account_message = '<div class="alert success">✓ تم تغيير كلمة المرور بنجاح.</div>';
        } catch (PDOException $e) {
            $account_error = '<div class="alert error">❌ حدث خطأ أثناء تغيير كلمة المرور.</div>';
        }
    }
}
?>

<div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-user-circle"></i> حسابي</h3>
        </div>

        <?php echo $account_message; ?>
        <?php echo $account_error; ?>

        <div class="stats-two-col">
            <div class="card">
                <h3><i class="fas fa-id-card"></i> معلومات الحساب</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المستخدم</label>
                        <input type="text" value="<?php echo htmlspecialchars($staff['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>الدور</label>
                        <input type="text" value="<?php echo $staff['role'] === 'admin' ? 'مدير' : 'موظف'; ?>" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label>تاريخ إنشاء الحساب</label>
                    <input type="text" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($staff['created_at']))); ?>" disabled>
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-camera"></i> الصورة الشخصية</h3>
                <div class="avatar-section">
                    <?php
                    // Check if avatar column exists
                    try {
                        $check_column = $pdo->query("SHOW COLUMNS FROM library_staff LIKE 'avatar'");
                        if ($check_column->rowCount() == 0) {
                            echo '<div class="alert warning" style="margin-bottom: 20px;">';
                            echo '<i class="fas fa-exclamation-triangle"></i> عمود الصورة غير موجود. ';
                            echo '<a href="auto_setup.php" style="color: #007bff; text-decoration: underline;">اضغط هنا للإعداد التلقائي</a>';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert error" style="margin-bottom: 20px;">';
                            echo '<i class="fas fa-database"></i> لا يمكن الوصول لقاعدة البيانات.';
                            echo '</div>';
                    }
                    ?>
                    
                    <div class="current-avatar">
                        <?php 
                        if (!empty($staff['avatar']) && file_exists('uploads/avatars/' . $staff['avatar'])) {
                            echo '<img src="uploads/avatars/' . htmlspecialchars($staff['avatar']) . '" alt="Current Avatar" class="avatar-preview">';
                        } else {
                            echo '<div class="avatar-placeholder"><i class="fas fa-user-circle"></i></div>';
                        }
                        ?>
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data" class="avatar-form">
                        <div class="form-group">
                            <label>اختر صورة جديدة</label>
                            <input type="file" name="avatar" accept="image/*" class="file-input">
                            <small>الصيغ المسموحة: JPG, PNG, GIF, WebP (الحد الأقصى: 2 ميجابايت)</small>
                        </div>
                        <button type="submit" name="upload_avatar" class="btn-primary w-100">
                            <i class="fas fa-upload"></i> رفع الصورة
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-user-edit"></i> تعديل البيانات الشخصية</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="required">الاسم الكامل</label>
                        <input type="text" name="full_name" required value="<?php echo htmlspecialchars($staff['full_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>رقم الجوال</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="save_profile" class="btn-primary w-100">
                        <i class="fas fa-save"></i> حفظ البيانات
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <h3><i class="fas fa-lock"></i> تغيير كلمة المرور</h3>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label class="required">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label class="required">كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" required>
                </div>
            </div>
            <div class="form-group">
                <label class="required">تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn-primary w-100">
                <i class="fas fa-key"></i> تحديث كلمة المرور
            </button>
        </form>
    </div>
</div>
