<?php
// projects_list_simple.php - For inclusion in index.php
require_once 'database.php';
require_once 'includes/functions.php';
$projects = getAllProjects($pdo);
?>
<div class="borrow-theme">
<div class="card">
    <div class="page-header">
        <h3><i class="fas fa-folder-open"></i> أرشيف المشاريع</h3>
        <a href="projects_list.php" class="btn-add">
            <i class="fas fa-plus"></i> إضافة مشروع جديد
        </a>
    </div>
    
    <div class="search-box">
        <input type="text" id="searchProjectInput" placeholder="البحث بالرقم الأرشيفي أو اسم المشروع...">
        <button class="btn-secondary" onclick="searchProjectInList()"><i class="fas fa-search"></i> بحث</button>
    </div>
    
    <div class="table-responsive">
        <table id="projectsTable">
            <thead>
                <tr>
                    <th>الرقم الأرشيفي</th>
                    <th>اسم المشروع</th>
                    <th>المشرف</th>
                    <th>الطالب</th>
                    <th>الفصل</th>
                    <th>السنة</th>
                    <th>القسم</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($projects) > 0): ?>
                    <?php foreach($projects as $project): ?>
                    <tr>
                        <td class="mono-id"><?php echo htmlspecialchars($project['archive_number']); ?></td>
                        <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['supervisor']); ?></td>
                        <td><?php echo htmlspecialchars($project['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['semester']); ?></td>
                        <td><?php echo $project['year']; ?></td>
                        <td><?php echo htmlspecialchars($project['dept_name']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <button onclick="deleteProject(<?php echo $project['id']; ?>, '<?php echo addslashes($project['project_name']); ?>')" class="btn-delete">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <i class="fas fa-folder-open"></i>
                            لا توجد مشاريع مضافة حالياً
                            <br>
                            <a href="projects_list.php" class="link-primary">أضف أول مشروع</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
function searchProjectInList() {
    const query = document.getElementById('searchProjectInput').value.trim().toLowerCase();
    const table = document.querySelector('#projectsTable tbody');
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        if(row.cells.length > 1 && row.cells[0] && row.cells[0].getAttribute('colspan') !== '8') {
            const archive = row.cells[0]?.textContent.toLowerCase() || '';
            const name = row.cells[1]?.textContent.toLowerCase() || '';
            const supervisor = row.cells[2]?.textContent.toLowerCase() || '';
            const student = row.cells[3]?.textContent.toLowerCase() || '';
            
            if(archive.includes(query) || name.includes(query) || supervisor.includes(query) || student.includes(query) || query === '') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function deleteProject(id, name) {
    if(confirm(`هل أنت متأكد من حذف المشروع "${name}"؟`)) {
        window.location.href = `?delete_project=${id}`;
    }
}
</script>
