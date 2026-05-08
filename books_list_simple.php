<?php
// books_list_simple.php - For inclusion in index.php
require_once 'database.php';
require_once 'includes/functions.php';
$books = getAllBooks($pdo);
?>
<div class="borrow-theme">
<div class="card">
    <div class="page-header">
        <h3><i class="fas fa-book"></i> قائمة الكتب</h3>
        <a href="books_list.php" class="btn-add"><i class="fas fa-plus"></i> إضافة كتاب </a>
    </div>
    
    <div class="search-box">
        <input type="text" id="searchBookInput" placeholder="البحث بالرقم الأرشيفي (ISBN) أو عنوان المادة...">
        <button class="btn-secondary" onclick="searchBook()"><i class="fas fa-search"></i> بحث</button>
    </div>
    <div id="bookSearchResult"></div>
    
    <div class="table-responsive">
        <table id="booksTable">
            <thead>
                <tr><th>الرقم الأرشيفي</th><th>العنوان</th><th>المؤلف</th><th>الناشر</th><th>الكمية</th><th>القسم</th><th>إجراءات</th></tr>
            </thead>
            <tbody>
                <?php foreach($books as $book): ?>
                <tr>
                    <td class="mono-id"><?php echo htmlspecialchars($book['isbn'] ?? '—'); ?></td>
                    <td>
                        <a href="javascript:void(0)" onclick='showBookPreview(<?php echo json_encode($book); ?>)' class="link-primary">
                            <i class="fas fa-eye"></i>
                            <?php echo htmlspecialchars($book['book_title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['publisher']); ?></td>
                    <td><?php echo $book['quantity']; ?></td>
                    <td><?php echo !empty($book['custom_department']) ? '<span class="link-primary">' . htmlspecialchars($book['custom_department']) . '</span><span class="custom-dept-badge">(خارجي)</span>' : htmlspecialchars($book['dept_name'] ?? 'بدون قسم'); ?></td>
                    <td class="action-buttons">
                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> تعديل</a>
                        <button onclick="deleteBook(<?php echo $book['id']; ?>, '<?php echo addslashes($book['book_title']); ?>')" class="btn-delete"><i class="fas fa-trash"></i> حذف</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
function searchBook() {
    const query = document.getElementById('searchBookInput')?.value.trim();
    if(!query) {
        const container = document.getElementById('bookSearchResult');
        if(container) container.innerHTML = '';
        document.querySelectorAll('#booksTable tbody tr').forEach(row => row.style.display = '');
        return;
    }

    fetch(`?search_book=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(html => {
            const container = document.getElementById('bookSearchResult');
            if(container) container.innerHTML = html;
            document.querySelectorAll('#booksTable tbody tr').forEach(row => row.style.display = 'none');
        })
        .catch(error => console.error('Search error:', error));
}

function showBookPreview(book) {
    const modal = document.createElement('div');
    modal.className = 'preview-modal';
    modal.innerHTML = `
        <div class="preview-content">
            <div class="preview-header">
                <h3><i class="fas fa-book"></i> تفاصيل الكتاب</h3>
                <button onclick="this.closest('.preview-modal').remove()" class="btn-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="preview-body">
                <div class="preview-info">
                    <p><strong>العنوان:</strong> ${book.book_title || 'غير محدد'}</p>
                    <p><strong>المؤلف:</strong> ${book.author || 'غير محدد'}</p>
                    <p><strong>الناشر:</strong> ${book.publisher || 'غير محدد'}</p>
                    <p><strong>الرقم الأرشيفي:</strong> ${book.isbn || 'غير محدد'}</p>
                    <p><strong>الكمية:</strong> ${book.quantity || '0'}</p>
                    <p><strong>القسم:</strong> ${book.dept_name || 'بدون قسم'}</p>
                    ${book.description ? `<p><strong>الوصف:</strong> ${book.description}</p>` : ''}
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', e => { if(e.target === modal) modal.remove(); });
}

function deleteBook(id, title) {
    if(confirm(`هل أنت متأكد من حذف "${title}"؟`)) window.location.href = `?delete_book=${id}`;
}
</script>
