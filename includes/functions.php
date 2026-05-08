<?php
// includes/functions.php
function getStatistics($pdo) {
    $total_books = (int)($pdo->query("SELECT COALESCE(SUM(quantity), 0) as total FROM books")->fetch()['total'] ?? 0);
    $total_projects = (int)($pdo->query("SELECT COUNT(*) as total FROM projects")->fetch()['total'] ?? 0);
    $active_loans = (int)($pdo->query("SELECT COUNT(*) as total FROM faculty_borrowings")->fetch()['total'] ?? 0);

    $overdue_loans = (int)($pdo->query("SELECT COUNT(*) as total FROM faculty_borrowings WHERE expected_return_date < CURDATE()")->fetch()['total'] ?? 0);
    $unavailable_books = (int)($pdo->query("SELECT COUNT(*) as total FROM books WHERE quantity <= 0")->fetch()['total'] ?? 0);

    $top_department_stmt = $pdo->query("
        SELECT faculty_department, COUNT(*) as borrow_count
        FROM faculty_borrowings
        WHERE MONTH(borrow_date) = MONTH(CURDATE()) AND YEAR(borrow_date) = YEAR(CURDATE())
        GROUP BY faculty_department
        ORDER BY borrow_count DESC
        LIMIT 1
    ");
    $top_department_row = $top_department_stmt ? $top_department_stmt->fetch() : null;

    $monthly_borrow_total = (int)($pdo->query("
        SELECT COUNT(*) as total FROM faculty_borrowings
        WHERE MONTH(borrow_date) = MONTH(CURDATE()) AND YEAR(borrow_date) = YEAR(CURDATE())
    ")->fetch()['total'] ?? 0);

    try {
        $total_faculty = (int)($pdo->query("SELECT COUNT(*) as total FROM library_staff")->fetch()['total'] ?? 0);
    } catch (PDOException $e) {
        $total_faculty = 0;
    }

    return [
        'total_books' => $total_books,
        'total_projects' => $total_projects,
        'active_loans' => $active_loans,
        'total_faculty' => $total_faculty,
        'overdue_loans' => $overdue_loans,
        'unavailable_books' => $unavailable_books,
        'top_department' => $top_department_row['faculty_department'] ?? 'لا يوجد',
        'top_department_borrow_count' => (int)($top_department_row['borrow_count'] ?? 0),
        'monthly_borrow_total' => $monthly_borrow_total
    ];
}

function getBooksPerDepartment($pdo) {
    return $pdo->query("
        SELECT 
            CASE 
                WHEN b.custom_department IS NOT NULL THEN b.custom_department
                ELSE d.name_ar 
            END as department, 
            SUM(b.quantity) as book_count 
        FROM books b 
        LEFT JOIN departments d ON b.department_id = d.id 
        GROUP BY department 
        ORDER BY book_count DESC
    ")->fetchAll();
}

function getProjectsPerDepartment($pdo) {
    return $pdo->query("
        SELECT d.name_ar as department, COUNT(p.id) as project_count 
        FROM projects p 
        LEFT JOIN departments d ON p.department_id = d.id 
        GROUP BY d.id, d.name_ar 
        ORDER BY project_count DESC
    ")->fetchAll();
}

function getOverdueLoansDetails($pdo) {
    $stmt = $pdo->query("
        SELECT 
            fb.id,
            fb.faculty_name,
            fb.borrow_date,
            fb.expected_return_date,
            DATEDIFF(CURDATE(), fb.expected_return_date) as days_overdue,
            fb.book_title,
            fb.book_isbn,
            fb.faculty_department as department
        FROM faculty_borrowings fb
        WHERE fb.expected_return_date < CURDATE()
        ORDER BY fb.expected_return_date ASC
        LIMIT 10
    ");
    
    $overdue_loans = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $overdue_loans[] = [
            'id' => $row['id'],
            'faculty_name' => $row['faculty_name'],
            'book_title' => $row['book_title'],
            'book_isbn' => $row['book_isbn'],
            'department' => $row['department'] ?? 'غير محدد',
            'borrow_date' => $row['borrow_date'],
            'expected_return_date' => $row['expected_return_date'],
            'days_overdue' => (int)$row['days_overdue']
        ];
    }
    
    return $overdue_loans;
}

function getDashboardAlerts($pdo) {
    $alerts = [];

    $due_soon_count = (int)($pdo->query("
        SELECT COUNT(*) as total
        FROM faculty_borrowings
        WHERE expected_return_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    ")->fetch()['total'] ?? 0);

    if ($due_soon_count > 0) {
        $alerts[] = [
            'type' => 'warning',
            'icon' => 'fa-clock',
            'message' => "يوجد {$due_soon_count} إعارة ستنتهي خلال 3 أيام."
        ];
    }

    $overdue_count = (int)($pdo->query("
        SELECT COUNT(*) as total
        FROM faculty_borrowings
        WHERE expected_return_date < CURDATE()
    ")->fetch()['total'] ?? 0);

    if ($overdue_count > 0) {
        $alerts[] = [
            'type' => 'danger',
            'icon' => 'fa-triangle-exclamation',
            'message' => "يوجد {$overdue_count} إعارة متأخرة تحتاج متابعة."
        ];
    }

    $unclassified_books = (int)($pdo->query("
        SELECT COUNT(*) as total
        FROM books
        WHERE department_id IS NULL AND (custom_department IS NULL OR custom_department = '')
    ")->fetch()['total'] ?? 0);

    if ($unclassified_books > 0) {
        $alerts[] = [
            'type' => 'info',
            'icon' => 'fa-layer-group',
            'message' => "يوجد {$unclassified_books} مادة بدون تصنيف قسم."
        ];
    }

    return $alerts;
}

function getRecentDashboardActivity($pdo, $limit = 5) {
    $activities = [];

    try {
        $book_rows = $pdo->query("
            SELECT book_title AS item_name, created_at
            FROM books
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();
        foreach ($book_rows as $row) {
            $activities[] = [
                'icon' => 'fa-book',
                'text' => 'تمت إضافة مادة: ' . ($row['item_name'] ?? 'بدون عنوان'),
                'created_at' => $row['created_at'] ?? null
            ];
        }
    } catch (PDOException $e) {
        // Skip if created_at column doesn't exist.
    }

    try {
        $project_rows = $pdo->query("
            SELECT project_name AS item_name, created_at
            FROM projects
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();
        foreach ($project_rows as $row) {
            $activities[] = [
                'icon' => 'fa-folder-open',
                'text' => 'تمت إضافة مشروع: ' . ($row['item_name'] ?? 'بدون اسم'),
                'created_at' => $row['created_at'] ?? null
            ];
        }
    } catch (PDOException $e) {
        // Skip if created_at column doesn't exist.
    }

    try {
        $borrow_rows = $pdo->query("
            SELECT faculty_name, book_title, created_at
            FROM faculty_borrowings
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();
        foreach ($borrow_rows as $row) {
            $faculty = $row['faculty_name'] ?? 'عضو هيئة تدريس';
            $book = $row['book_title'] ?? 'مادة';
            $activities[] = [
                'icon' => 'fa-hand-holding',
                'text' => "تم تسجيل إعارة: {$book} ({$faculty})",
                'created_at' => $row['created_at'] ?? null
            ];
        }
    } catch (PDOException $e) {
        // Skip if created_at column doesn't exist.
    }

    usort($activities, function ($a, $b) {
        return strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? '');
    });

    return array_slice($activities, 0, max(1, (int)$limit));
}

function getAllBooks($pdo) {
    return $pdo->query("
        SELECT b.*, d.name_ar as dept_name 
        FROM books b 
        LEFT JOIN departments d ON b.department_id = d.id 
        ORDER BY b.id
    ")->fetchAll();
}

function getAllProjects($pdo) {
    return $pdo->query("
        SELECT p.*, d.name_ar as dept_name 
        FROM projects p 
        LEFT JOIN departments d ON p.department_id = d.id 
        ORDER BY p.year DESC, p.created_at DESC
    ")->fetchAll();
}
?>