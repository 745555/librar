<?php
// dashboard.php
require_once 'database.php';
require_once 'includes/functions.php';
$stats = getStatistics($pdo);
$books_per_dept = getBooksPerDepartment($pdo);
$projects_per_dept = getProjectsPerDepartment($pdo);
$dashboard_alerts = getDashboardAlerts($pdo);
$recent_activity = getRecentDashboardActivity($pdo, 5);
$overdue_loans_details = getOverdueLoansDetails($pdo);

$max_books_count = 0;
foreach ($books_per_dept as $dept_row) {
    $max_books_count = max($max_books_count, (int)$dept_row['book_count']);
}

$max_projects_count = 0;
foreach ($projects_per_dept as $dept_row) {
    $max_projects_count = max($max_projects_count, (int)$dept_row['project_count']);
}

$months_ar = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
$current_month_label = $months_ar[(int)date('n') - 1] . ' ' . date('Y');

$monthly_total = (int)($stats['monthly_borrow_total'] ?? 0);
$top_count = (int)($stats['top_department_borrow_count'] ?? 0);
$top_share_pct = ($monthly_total > 0 && $top_count > 0)
    ? (int)round(($top_count / $monthly_total) * 100)
    : 0;
?>
<!-- Notification Bell Icon - Always Available -->
<div class="notification-bell-container">
    <div class="notification-bell" onclick="toggleNotifications()">
        <i class="fas fa-bell"></i>
        <span class="notification-badge" style="<?php echo empty($dashboard_alerts) ? 'display: none;' : ''; ?>"><?php echo count($dashboard_alerts); ?></span>
    </div>
    
    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h4><i class="fas fa-bell"></i> الإشعارات</h4>
            <button class="clear-all-btn" onclick="clearAllNotifications()">
                <i class="fas fa-trash"></i> مسح الكل
            </button>
        </div>
        <div class="notification-list">
            <?php if (!empty($dashboard_alerts)): ?>
                <?php foreach ($dashboard_alerts as $index => $alert): ?>
                <div class="notification-item <?php echo htmlspecialchars($alert['type']); ?>" data-index="<?php echo $index; ?>">
                    <div class="notification-icon">
                        <i class="fas <?php echo htmlspecialchars($alert['icon']); ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message"><?php echo htmlspecialchars($alert['message']); ?></div>
                        <div class="notification-time">الآن</div>
                    </div>
                    <button class="notification-close" onclick="removeNotification(<?php echo $index; ?>)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notification-item empty">
                    <div class="notification-content">
                        <div class="notification-message">لا توجد إشعارات حالياً</div>
                        <div class="notification-time">اضغط على بطاقة الإعارات المتأخرة لعرض التفاصيل</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="notification-footer">
            <a href="#" class="view-all-link">عرض جميع الإشعارات</a>
        </div>
    </div>
</div>

<script>
let notifications = <?php echo json_encode($dashboard_alerts); ?>;
let notificationCount = <?php echo count($dashboard_alerts); ?>;
let isDragging = false;
let dragTimeout;
let dragStartX, dragStartY;
let currentX, currentY;

// Load saved position on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBellPosition();
    initializeDragFunctionality();
});

function initializeDragFunctionality() {
    const bell = document.querySelector('.notification-bell');
    const container = document.querySelector('.notification-bell-container');
    
    // Mouse events
    bell.addEventListener('mousedown', startDrag);
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', endDrag);
    
    // Touch events for mobile
    bell.addEventListener('touchstart', startDrag);
    document.addEventListener('touchmove', drag);
    document.addEventListener('touchend', endDrag);
    
    // Prevent context menu on long press
    bell.addEventListener('contextmenu', e => e.preventDefault());
}

function startDrag(e) {
    e.preventDefault();
    
    // Check if it's a long press (500ms)
    clearTimeout(dragTimeout);
    dragTimeout = setTimeout(() => {
        isDragging = true;
        const container = document.querySelector('.notification-bell-container');
        const bell = document.querySelector('.notification-bell');
        
        // Add dragging styles
        container.classList.add('dragging');
        bell.style.cursor = 'grabbing';
        bell.style.transform = 'scale(1.1)';
        
        // Get starting positions
        const touch = e.touches ? e.touches[0] : e;
        dragStartX = touch.clientX - currentX;
        dragStartY = touch.clientY - currentY;
        
        // Prevent dropdown from opening during drag
        bell.onclick = null;
    }, 500);
}

function drag(e) {
    if (!isDragging) return;
    
    e.preventDefault();
    const container = document.querySelector('.notification-bell-container');
    const touch = e.touches ? e.touches[0] : e;
    
    currentX = touch.clientX - dragStartX;
    currentY = touch.clientY - dragStartY;
    
    // Constrain to viewport
    const maxX = window.innerWidth - 60;
    const maxY = window.innerHeight - 60;
    
    currentX = Math.max(10, Math.min(currentX, maxX));
    currentY = Math.max(10, Math.min(currentY, maxY));
    
    container.style.left = currentX + 'px';
    container.style.top = currentY + 'px';
    
    // Update dropdown position
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.style.left = '0';
        dropdown.style.top = '60px';
    }
}

function endDrag() {
    clearTimeout(dragTimeout);
    
    if (isDragging) {
        isDragging = false;
        const container = document.querySelector('.notification-bell-container');
        const bell = document.querySelector('.notification-bell');
        
        // Remove dragging styles
        container.classList.remove('dragging');
        bell.style.cursor = 'pointer';
        bell.style.transform = '';
        
        // Save position
        saveBellPosition(currentX, currentY);
        
        // Restore click functionality
        setTimeout(() => {
            bell.onclick = toggleNotifications;
        }, 100);
    }
}

function saveBellPosition(x, y) {
    localStorage.setItem('bellPosition', JSON.stringify({ x, y }));
}

function loadBellPosition() {
    const saved = localStorage.getItem('bellPosition');
    if (saved) {
        const position = JSON.parse(saved);
        currentX = position.x || 20;
        currentY = position.y || 20;
        
        const container = document.querySelector('.notification-bell-container');
        if (container) {
            container.style.left = currentX + 'px';
            container.style.top = currentY + 'px';
        }
    } else {
        currentX = 20;
        currentY = 20;
    }
}

function toggleNotifications() {
    if (isDragging) return; // Prevent toggle during drag
    
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.querySelector('.notification-bell');
    
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
        bell.classList.remove('active');
    } else {
        dropdown.style.display = 'block';
        bell.classList.add('active');
    }
}

function removeNotification(index) {
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            item.remove();
            updateNotificationCount();
        }, 300);
    }
}

function clearAllNotifications() {
    const items = document.querySelectorAll('.notification-item');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => item.remove(), 300);
        }, index * 100);
    });
    
    setTimeout(() => {
        updateNotificationCount();
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = 'none';
    }, items.length * 100 + 300);
}

function updateNotificationCount() {
    const remainingItems = document.querySelectorAll('.notification-item').length;
    const badge = document.querySelector('.notification-badge');
    const bell = document.querySelector('.notification-bell');
    
    if (remainingItems === 0) {
        badge.style.display = 'none';
        bell.classList.remove('has-notifications');
    } else {
        badge.textContent = remainingItems;
        badge.style.display = 'flex';
        bell.classList.add('has-notifications');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const container = document.querySelector('.notification-bell-container');
    if (!container.contains(event.target)) {
        document.getElementById('notificationDropdown').style.display = 'none';
        document.querySelector('.notification-bell').classList.remove('active');
    }
});
</script>

<div class="stats-grid">
    <div class="stat-card stat-card-priority stat-card-info clickable-card" onclick="navigateToPage('books_list.php')">
        <div class="stat-info">
            <h3><i class="fas fa-book"></i> إجمالي الكـتب</h3>
            <div class="stat-number"><?php echo number_format($stats['total_books']); ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-book"></i></div>
    </div>
    <div class="stat-card stat-card-priority stat-card-warning clickable-card" onclick="navigateToPage('borrow_form.php')">
        <div class="stat-info">
            <h3><i class="fas fa-hand-holding"></i> الإعارات النشطة</h3>
            <div class="stat-number"><?php echo $stats['active_loans']; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-hand-holding"></i></div>
    </div>
    <div class="stat-card stat-card-priority stat-card-danger clickable-card" onclick="showOverdueNotifications()">
        <div class="stat-info">
            <h3><i class="fas fa-triangle-exclamation"></i> الإعارات المتأخرة</h3>
            <div class="stat-number"><?php echo $stats['overdue_loans']; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
    </div>
    <div class="stat-card stat-card-priority stat-card-info clickable-card" onclick="navigateToPage('projects_list.php')">
        <div class="stat-info">
            <h3><i class="fas fa-archive"></i> المشاريع</h3>
            <div class="stat-number"><?php echo $stats['total_projects']; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-archive"></i></div>
    </div>
</div>

<div class="dashboard-secondary-stats">
    <div class="secondary-chip">
        <i class="fas fa-chalkboard-user"></i>
        <span>أعضاء هيئة التدريس: <strong><?php echo (int)$stats['total_faculty']; ?></strong></span>
    </div>
    <div class="secondary-chip">
        <i class="fas fa-box-open"></i>
        <span>مواد غير متاحة: <strong><?php echo (int)$stats['unavailable_books']; ?></strong></span>
    </div>
</div>

<!-- SMART DASHBOARD WITH QUICK SEARCH -->
<div class="card smart-dashboard">
    <div class="smart-dashboard__header">
        <h3><i class="fas fa-search"></i> البحث الذكي السريع</h3>
        <div class="smart-dashboard__stats">
            <span class="stat-badge"><i class="fas fa-database"></i> <?php echo (int)$stats['total_books'] + (int)$stats['total_projects']; ?> عنصر</span>
        </div>
    </div>
    
    <div class="smart-dashboard__search">
        <div class="search-container">
            <input type="text" id="smartSearchInput" placeholder="🔍 ابحث عن كتاب، مشروع، مؤلف، طالب، أو أي شيء..." autocomplete="off">
            <div class="search-shortcuts">
                <button type="button" class="shortcut-btn" onclick="quickSearch('book')" title="بحث سريع في الكتب">
                    <i class="fas fa-book"></i>
                    <span>كتاب</span>
                </button>
                <button type="button" class="shortcut-btn" onclick="quickSearch('project')" title="بحث سريع في المشاريع">
                    <i class="fas fa-folder-open"></i>
                    <span>مشروع</span>
                </button>
                <button type="button" class="shortcut-btn" onclick="quickSearch('borrow')" title="بحث سريع في الإعارات">
                    <i class="fas fa-hand-holding"></i>
                    <span>إعارة</span>
                </button>
            </div>
        </div>
        <div id="smartSearchResults" class="smart-search-results"></div>
    </div>
</div>

<div class="card dashboard-toolbar">
    <h3><i class="fas fa-filter"></i> فلترة سريعة حسب القسم</h3>
    <div class="search-box">
        <input type="text" id="dashboardDeptFilter" placeholder="🔍 اكتب اسم القسم لتصفية القوائم...">
        <button type="button" class="btn-secondary" onclick="clearDashboardFilter()">
            <i class="fas fa-sync-alt"></i> إعادة تعيين
        </button>
    </div>
</div>

<div class="stats-two-col">
    <div class="card dashboard-dept-card">
        <h3><i class="fas fa-book"></i> المواد حسب القسم</h3>
        <ul class="dept-list">
            <?php foreach($books_per_dept as $dept): ?>
            <?php
                $dept_name = $dept['department'] ?? 'بدون قسم';
                $book_count = (int)$dept['book_count'];
                $books_width = $max_books_count > 0 ? max(5, (int)(($book_count / $max_books_count) * 100)) : 5;
            ?>
            <li class="dept-item dashboard-dept-item" data-dept="<?php echo htmlspecialchars(mb_strtolower($dept_name)); ?>">
                <span class="dept-name"><i class="fas fa-building"></i><?php echo htmlspecialchars($dept['department'] ?? 'بدون قسم'); ?></span>
                <span class="dept-count books"><?php echo $book_count; ?> مادة</span>
            </li>
            <div class="dept-progress">
                <div class="dept-progress-bar books" style="width: <?php echo $books_width; ?>%;"></div>
            </div>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card dashboard-dept-card">
        <h3><i class="fas fa-folder-open"></i> المشاريع حسب القسم</h3>
        <ul class="dept-list">
            <?php foreach($projects_per_dept as $dept): ?>
            <?php
                $dept_name = $dept['department'] ?? 'بدون قسم';
                $project_count = (int)$dept['project_count'];
                $projects_width = $max_projects_count > 0 ? max(5, (int)(($project_count / $max_projects_count) * 100)) : 5;
            ?>
            <li class="dept-item dashboard-dept-item" data-dept="<?php echo htmlspecialchars(mb_strtolower($dept_name)); ?>">
                <span class="dept-name"><i class="fas fa-building"></i><?php echo htmlspecialchars($dept_name); ?></span>
                <span class="dept-count projects"><?php echo $project_count; ?> مشروع</span>
            </li>
            <div class="dept-progress">
                <div class="dept-progress-bar projects" style="width: <?php echo $projects_width; ?>%;"></div>
            </div>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-clock-rotate-left"></i> آخر النشاط</h3>
    <ul class="recent-activity-list">
        <?php if (!empty($recent_activity)): ?>
            <?php foreach ($recent_activity as $activity): ?>
                <li class="recent-activity-item">
                    <i class="fas <?php echo htmlspecialchars($activity['icon']); ?>"></i>
                    <span><?php echo htmlspecialchars($activity['text']); ?></span>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="recent-activity-item empty">لا يوجد نشاط حديث لعرضه حالياً.</li>
        <?php endif; ?>
    </ul>
</div>

<script>
function showOverdueNotifications() {
    console.log('showOverdueNotifications called');
    const overdueLoans = <?php echo json_encode($overdue_loans_details); ?>;
    console.log('Overdue loans:', overdueLoans);
    
    if (!overdueLoans || overdueLoans.length === 0) {
        alert('لا توجد إعارات متأخرة حالياً');
        return;
    }
    
    // Create custom notifications for overdue loans
    const customAlerts = overdueLoans.map((loan, index) => ({
        type: 'danger',
        icon: 'fa-triangle-exclamation',
        message: `تأخير ${loan.days_overdue} أيام: "${loan.book_title}" - ${loan.faculty_name} (${loan.department})`,
        index: index
    }));
    
    console.log('Custom alerts created:', customAlerts);
    
    // Update notification dropdown with overdue loans
    updateNotificationDropdown(customAlerts);
    
    // Show the bell notification
    const bell = document.querySelector('.notification-bell');
    const dropdown = document.getElementById('notificationDropdown');
    
    console.log('Bell element:', bell);
    console.log('Dropdown element:', dropdown);
    
    if (bell && dropdown) {
        dropdown.style.display = 'block';
        bell.classList.add('active');
        
        // Scroll to the bell if it's not visible
        bell.scrollIntoView({ behavior: 'smooth', block: 'center' });
        console.log('Dropdown shown successfully');
    } else {
        console.error('Bell or dropdown element not found');
    }
}

function updateNotificationDropdown(alerts) {
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;
    
    const notificationList = dropdown.querySelector('.notification-list');
    if (!notificationList) return;
    
    // Clear existing notifications
    notificationList.innerHTML = '';
    
    // Add overdue loan notifications
    alerts.forEach(alert => {
        const notificationItem = document.createElement('div');
        notificationItem.className = `notification-item ${alert.type}`;
        notificationItem.setAttribute('data-index', alert.index);
        
        notificationItem.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${alert.icon}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-message">${alert.message}</div>
                <div class="notification-time">الآن</div>
            </div>
            <button class="notification-close" onclick="removeNotification(${alert.index})">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        notificationList.appendChild(notificationItem);
    });
    
    // Update notification count
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = alerts.length;
        badge.style.display = 'flex';
        document.querySelector('.notification-bell').classList.add('has-notifications');
    }
    
    // Update header
    const header = dropdown.querySelector('.notification-header h4');
    if (header) {
        header.innerHTML = '<i class="fas fa-triangle-exclamation"></i> الإعارات المتأخرة';
    }
}

function navigateToPage(page) {
    window.location.href = page;
}

function clearDashboardFilter() {
    const filterInput = document.getElementById('dashboardDeptFilter');
    if (filterInput) {
        filterInput.value = '';
        applyDashboardFilter('');
    }
}

function applyDashboardFilter(term) {
    let visibleCount = 0;
    document.querySelectorAll('.dashboard-dept-item').forEach(item => {
        const dept = item.getAttribute('data-dept') || '';
        const progress = item.nextElementSibling;
        const visible = dept.includes(term);
        item.style.display = visible ? 'flex' : 'none';
        if (visible) visibleCount++;
        if (progress && progress.classList.contains('dept-progress')) {
            progress.style.display = visible ? 'block' : 'none';
        }
    });

    const noResults = document.getElementById('dashboardFilterNoResults');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// Smart Dashboard Functions
function quickSearch(type) {
    const input = document.getElementById('smartSearchInput');
    const resultsContainer = document.getElementById('smartSearchResults');
    
    // Set placeholder based on type
    const placeholders = {
        'book': 'ابحث عن كتاب، مؤلف، ناشر، أو ISBN...',
        'project': 'ابحث عن مشروع، مشرف، طالب، أو رقم أرشيفي...',
        'borrow': 'ابحث عن إعارة، اسم عضو هيئة التدريس، أو كتاب...'
    };
    
    input.placeholder = placeholders[type] || 'ابحث عن أي شيء...';
    input.focus();
    
    // Change input border color based on search type
    const colors = {
        'book': '#4f46e5',
        'project': '#059669', 
        'borrow': '#ea580c'
    };
    
    input.style.borderColor = colors[type] || '#667eea';
    input.style.boxShadow = `0 0 0 3px ${colors[type] || '#667eea'}`;
    
    // Simulate search results
    resultsContainer.innerHTML = `
        <div style="padding: 15px; text-align: center; color: white;">
            <i class="fas fa-spinner fa-spin"></i> جاري البحث في ${type === 'book' ? 'الكتب' : type === 'project' ? 'المشاريع' : 'الإعارات'}...
        </div>
    `;
    
    // Simulate search delay
    setTimeout(() => {
        if (type === 'book') {
            window.location.href = 'books_list.php';
        } else if (type === 'project') {
            window.location.href = 'projects_list.php';
        } else if (type === 'borrow') {
            window.location.href = 'borrow_form.php';
        }
    }, 1000);
}

// Smart search input handler
document.addEventListener('DOMContentLoaded', function () {
    const filterInput = document.getElementById('dashboardDeptFilter');
    if (!filterInput) return;

    filterInput.addEventListener('input', function () {
        applyDashboardFilter(this.value.trim().toLowerCase());
    });
    
    // Smart search functionality
    const smartSearchInput = document.getElementById('smartSearchInput');
    if (smartSearchInput) {
        let searchTimeout;
        
        smartSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            const resultsContainer = document.getElementById('smartSearchResults');
            
            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                // Simulate smart search
                resultsContainer.innerHTML = `
                    <div style="padding: 15px; text-align: center; color: white;">
                        <i class="fas fa-search"></i> البحث عن "${query}"...
                    </div>
                `;
                
                // Simulate results after delay
                setTimeout(() => {
                    resultsContainer.innerHTML = `
                        <div style="padding: 10px;">
                            <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;" onclick="navigateToPage('books_list.php')">
                                <i class="fas fa-book"></i> نتائج كتب لـ "${query}"
                            </div>
                            <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;" onclick="navigateToPage('projects_list.php')">
                                <i class="fas fa-folder"></i> نتائج مشاريع لـ "${query}"
                            </div>
                        </div>
                    `;
                }, 800);
            }, 500);
        });
        
        smartSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query.length >= 2) {
                    // Navigate to books list with search parameter
                    window.location.href = `books_list.php?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for smart search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            smartSearchInput?.focus();
        }
        // Ctrl/Cmd + B for books
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            navigateToPage('books_list.php');
        }
        // Ctrl/Cmd + P for projects
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            navigateToPage('projects_list.php');
        }
        // Ctrl/Cmd + L for loans
        if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
            e.preventDefault();
            navigateToPage('borrow_form.php');
        }
    });
});
</script>

<div id="dashboardFilterNoResults" class="alert info" style="display:none;">
    لا توجد نتائج مطابقة لاسم القسم الذي أدخلته.
</div>
