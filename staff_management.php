<?php
// staff_management.php - إدارة الموظفين (للمدير فقط)
if(session_status() === PHP_SESSION_NONE) session_start();
require_once 'database.php';

// التحقق من تسجيل الدخول والصلاحيات
if(!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle Add Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    
    try {
        $check = $pdo->prepare("SELECT id FROM library_staff WHERE username = ?");
        $check->execute([$username]);
        if($check->fetch()) {
            $error = '<div class="alert error">❌ اسم المستخدم موجود بالفعل</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO library_staff (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $full_name, $email, $phone, $role]);
            $message = '<div class="alert success">✓ تم إضافة الموظف بنجاح</div>';
        }
    } catch(PDOException $e) {
        $error = '<div class="alert error">❌ حدث خطأ في إضافة الموظف</div>';
    }
}

// Handle Edit Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_staff'])) {
    $id = $_POST['staff_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    
    try {
        if(!empty($_POST['new_password'])) {
            $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE library_staff SET full_name=?, email=?, phone=?, role=?, password=? WHERE id=?");
            $stmt->execute([$full_name, $email, $phone, $role, $password, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE library_staff SET full_name=?, email=?, phone=?, role=? WHERE id=?");
            $stmt->execute([$full_name, $email, $phone, $role, $id]);
        }
        $message = '<div class="alert success">✓ تم تعديل بيانات الموظف بنجاح</div>';
    } catch(PDOException $e) {
        $error = '<div class="alert error">❌ حدث خطأ في تعديل الموظف</div>';
    }
}

// Handle Delete Staff
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if($id == $_SESSION['staff_id']) {
        $error = '<div class="alert error">❌ لا يمكنك حذف حسابك الخاص</div>';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM library_staff WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div class="alert success">✓ تم حذف الموظف بنجاح</div>';
        } catch(PDOException $e) {
            $error = '<div class="alert error">❌ لا يمكن حذف الموظف</div>';
        }
    }
}

// Get all staff
$staff_list = $pdo->query("SELECT * FROM library_staff ORDER BY id")->fetchAll();
?>

<div class="borrow-theme">
<div class="card">
    <div class="page-header">
        <h3><i class="fas fa-users"></i> إدارة الموظفين</h3>
    </div>
    
    <?php echo $message; ?>
    <?php echo $error; ?>
    
    <!-- Add Staff Form -->
    <div class="mb-30">
        <h4><i class="fas fa-user-plus"></i> إضافة موظف جديد</h4>
        <form method="POST" action="" class="subtle-form">
            <div class="form-row">
                <div class="form-group">
                    <label>اسم المستخدم *</label>
                    <input type="text" name="username" required placeholder="أدخل اسم المستخدم">
                </div>
                <div class="form-group">
                    <label>كلمة المرور *</label>
                    <input type="password" name="password" required placeholder="أدخل كلمة المرور">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم الكامل *</label>
                    <input type="text" name="full_name" required placeholder="أدخل الاسم الكامل">
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" placeholder="example@library.edu">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>رقم الجوال</label>
                    <input type="tel" name="phone" placeholder="05xxxxxxxx">
                </div>
                <div class="form-group">
                    <label>الصلاحية *</label>
                    <select name="role" required>
                        <option value="staff">موظف</option>
                        <option value="admin">مدير</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_staff" class="btn-primary"><i class="fas fa-save"></i> إضافة الموظف</button>
        </form>
    </div>
    
    <!-- Staff List -->
    <div>
        <h4><i class="fas fa-list"></i> قائمة الموظفين</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>الاسم الكامل</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>الصلاحية</th>
                        <th>تاريخ الإضافة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($staff_list as $index => $staff): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($staff['username']); ?></td>
                        <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                        <td>
                            <span class="badge <?php echo $staff['role']; ?>">
                                <?php echo $staff['role'] == 'admin' ? 'مدير' : 'موظف'; ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($staff['created_at'])); ?></td>
                        <td class="action-buttons">
                            <button class="btn-edit" onclick="openEditModal(<?php echo $staff['id']; ?>, '<?php echo addslashes($staff['username']); ?>', '<?php echo addslashes($staff['full_name']); ?>', '<?php echo addslashes($staff['email']); ?>', '<?php echo addslashes($staff['phone']); ?>', '<?php echo $staff['role']; ?>')">
                                <i class="fas fa-edit"></i> تعديل
                            </button>
                            <?php if($staff['id'] != $_SESSION['staff_id']): ?>
                            <a href="?delete_id=<?php echo $staff['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                                <i class="fas fa-trash"></i> حذف
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> تعديل بيانات الموظف</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="staff_id" id="edit_staff_id">
            <div class="form-group">
                <label>اسم المستخدم</label>
                <input type="text" id="edit_username" disabled class="modal-input-disabled">
            </div>
            <div class="form-group">
                <label>الاسم الكامل *</label>
                <input type="text" name="full_name" id="edit_full_name" required class="modal-input">
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" id="edit_email" class="modal-input">
            </div>
            <div class="form-group">
                <label>رقم الجوال</label>
                <input type="tel" name="phone" id="edit_phone" class="modal-input">
            </div>
            <div class="form-group">
                <label>الصلاحية</label>
                <select name="role" id="edit_role" class="modal-input">
                    <option value="staff">موظف</option>
                    <option value="admin">مدير</option>
                </select>
            </div>
            <div class="form-group">
                <label>كلمة مرور جديدة</label>
                <input type="password" name="new_password" placeholder="اتركها فارغة إذا لم تريد التغيير" class="modal-input">
                <small class="hint-text">أدخل كلمة المرور الجديدة فقط إذا أردت تغييرها</small>
            </div>
            <button type="submit" name="edit_staff" class="btn-primary w-100 mt-10">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
        </form>
    </div>
</div>
</div>

<script>
function openEditModal(id, username, full_name, email, phone, role) {
    document.getElementById('edit_staff_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_full_name').value = full_name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_role').value = role;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
}
</script>