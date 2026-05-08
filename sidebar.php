<?php
// sidebar.php
$current_page = $_GET['page'] ?? 'dashboard';
?>
<aside class="sidebar">
    <div class="logo">
        <div class="logo-header">
            <i class="fas fa-graduation-cap"></i>
            <div>
                <h2>كلية التقنية الهندسية جنزور</h2>
                <div class="logo-badge">
                    <i class="fas fa-book"></i>
                    <span>المكتبة</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="user-profile-mini">
        <div class="user-avatar-mini">
            <?php 
            // Check if user has uploaded profile image
            $user_image = isset($_SESSION['staff_image']) && !empty($_SESSION['staff_image']) ? $_SESSION['staff_image'] : '';
            if ($user_image && file_exists('uploads/avatars/' . $user_image)) {
                echo '<img src="uploads/avatars/' . htmlspecialchars($user_image) . '" alt="Profile Image" class="user-avatar-img">';
            } else {
                echo '<i class="fas fa-user-circle"></i>';
            }
            ?>
        </div>
        <div class="user-info-mini">
            <div class="user-name-mini"><?php echo htmlspecialchars($_SESSION['staff_name'] ?? ''); ?></div>
            <div class="user-role-mini"><?php echo ($_SESSION['staff_role'] ?? '') == 'admin' ? 'مدير النظام' : 'موظف'; ?></div>
        </div>
    </div>
    
        
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="index.php?page=dashboard" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i><span>لوحة التحكم</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?page=books" class="nav-link <?php echo $current_page == 'books' ? 'active' : ''; ?>" data-page="books">
                <i class="fas fa-book"></i><span>الكتب </span>
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?page=projects" class="nav-link <?php echo $current_page == 'projects' ? 'active' : ''; ?>" data-page="projects">
                <i class="fas fa-chalkboard-user"></i><span>المشاريع</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?page=borrow" class="nav-link <?php echo $current_page == 'borrow' ? 'active' : ''; ?>" data-page="borrow">
                <i class="fas fa-hand-holding-heart"></i><span>طلبات الإعارة</span>
            </a>
        </li>
        <?php if(isset($_SESSION['staff_role']) && $_SESSION['staff_role'] == 'admin'): ?>
        <li class="nav-item">
            <a href="index.php?page=staff" class="nav-link <?php echo $current_page == 'staff' ? 'active' : ''; ?>" data-page="staff">
                <i class="fas fa-users"></i><span>إدارة الموظفين</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="account-section">
        <a href="index.php?page=account" class="nav-link account-link <?php echo $current_page == 'account' ? 'active' : ''; ?>" data-page="account"><i class="fas fa-user-circle"></i><span>حسابي</span></a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>تسجيل الخروج</span></a>
    </div>
</aside>