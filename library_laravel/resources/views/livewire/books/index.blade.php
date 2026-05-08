<div>
    <div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-book"></i> قائمة الكتب المتاحة</h3>
            <a href="{{ route('books.create') }}" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> إضافة كتاب جديد</a>
        </div>
        
        <div class="search-box" style="margin-bottom: 25px;">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث بالرقم الأرشيفي (ISBN) أو عنوان المادة..." style="padding: 15px 25px; border-radius: 50px; background: var(--bg-off-white);">
        </div>

        @if (session()->has('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        
        <div class="table-responsive">
            <table id="booksTable">
                <thead>
                    <tr>
                        <th>الرقم الأرشيفي</th>
                        <th>عنوان الكتاب</th>
                        <th>المؤلف / الناشر</th>
                        <th>الكمية</th>
                        <th>القسم</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td class="mono-id" style="font-family: 'Courier New', monospace; font-weight: bold; color: var(--primary-blue);">{{ $book->isbn ?? '—' }}</td>
                        <td>
                            <a href="javascript:void(0)" wire:click="showBook({{ $book->id }})" class="link-primary" style="font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-book-open"></i>
                                {{ $book->book_title }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $book->author }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ $book->publisher }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $book->quantity > 0 ? 'staff' : 'error' }}">
                                {{ $book->quantity }} نسخة
                            </span>
                        </td>
                        <td>
                            @if($book->custom_department)
                                <span class="badge admin">{{ $book->custom_department }}</span>
                            @else
                                <span class="badge staff">{{ $book->department->name_ar ?? 'بدون قسم' }}</span>
                            @endif
                        </td>
                        <td class="action-buttons">
                            <a href="{{ route('books.edit', $book->id) }}" class="btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                            <button onclick="confirm('هل أنت متأكد من حذف {{ $book->book_title }}؟') || event.stopImmediatePropagation()" wire:click="deleteBook({{ $book->id }})" class="btn-delete" title="حذف"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 25px;">
            {{ $books->links() }}
        </div>
    </div>
    </div>

    <!-- Preview Modal -->
    @if($selectedBook)
    <div class="preview-modal" style="display: flex;">
        <div class="preview-content">
            <div class="preview-header">
                <h3><i class="fas fa-book"></i> تفاصيل الكتاب</h3>
                <button wire:click="$set('selectedBook', null)" class="preview-close" style="width: auto; background: none; border: none; padding: 0; margin: 0; position: absolute; top: 15px; left: 15px; color: white;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="preview-body">
                <div class="preview-info">
                    <div class="preview-row"><span>📖 العنوان:</span><span>{{ $selectedBook['book_title'] }}</span></div>
                    <div class="preview-row"><span>✍️ المؤلف:</span><span>{{ $selectedBook['author'] ?? '—' }}</span></div>
                    <div class="preview-row"><span>🏛️ الناشر:</span><span>{{ $selectedBook['publisher'] ?? '—' }}</span></div>
                    <div class="preview-row"><span>📅 السنة:</span><span>{{ $selectedBook['publication_year'] ?? 'غير محدد' }}</span></div>
                    <div class="preview-row"><span>🔢 الرقم الأرشيفي:</span><span>{{ $selectedBook['isbn'] ?? '—' }}</span></div>
                    <div class="preview-row"><span>📚 الكمية:</span><span>{{ $selectedBook['quantity'] }} نسخة</span></div>
                    <div class="preview-row"><span>🏢 القسم:</span><span>{{ $selectedBook['custom_department'] ?? $selectedBook['department']['name_ar'] ?? '—' }}</span></div>
                    <div class="preview-description">
                        <strong>📝 نبذة:</strong>
                        <p>{{ $selectedBook['description'] ?? 'لا توجد نبذة' }}</p>
                    </div>
                </div>
            </div>
            <button wire:click="$set('selectedBook', null)" class="preview-close">إغلاق</button>
        </div>
    </div>
    @endif
</div>
