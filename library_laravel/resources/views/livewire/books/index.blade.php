@section('title', 'قائمة الكتب')

<div>
    <div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-book"></i> قائمة الكتب المتاحة</h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                @can('books.create')
                    <a href="{{ route('books.create') }}" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> إضافة كتاب جديد</a>
                @endcan
                <button wire:click="exportBooks" class="btn-secondary" style="text-decoration: none;">
                    <i class="fas fa-download"></i> تصدير HTML
                </button>
            </div>
        </div>
        
        <div class="search-section" style="margin-bottom: 25px;">
            <!-- Basic Search -->
            <div class="search-box">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث بالرقم الأرشيفي (ISBN) أو عنوان المادة..." style="padding: 15px 25px; border-radius: 50px; background: var(--bg-off-white);">
                <button wire:click="toggleAdvancedSearch" class="btn-secondary" style="margin-right: 10px; padding: 15px 25px; border-radius: 50px;">
                    <i class="fas fa-filter"></i> بحث متقدم
                </button>
                @if($showAdvancedSearch || $department_id || $publication_year || $publisher || $min_quantity || $max_quantity)
                    <button wire:click="clearFilters" class="btn-add" style="padding: 15px 25px; border-radius: 50px;">
                        <i class="fas fa-times"></i> مسح الفلاتر
                    </button>
                @endif
            </div>

            <!-- Advanced Search -->
            @if($showAdvancedSearch)
                <div class="advanced-search" style="background: var(--bg-off-white); padding: 20px; border-radius: 12px; margin-top: 15px;">
                    <h4 style="margin-bottom: 15px; color: var(--text-primary);">
                        <i class="fas fa-search-plus"></i> البحث المتقدم
                    </h4>
                    <div class="search-filters" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div class="filter-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-muted);">القسم</label>
                            <select wire:model.live.debounce.300ms="department_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="">جميع الأقسام</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-muted);">سنة النشر</label>
                            <input type="number" wire:model.live.debounce.300ms="publication_year" placeholder="سنة النشر" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        </div>
                        
                        <div class="filter-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-muted);">الناشر</label>
                            <input type="text" wire:model.live.debounce.300ms="publisher" placeholder="اسم الناشر" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        </div>
                        
                        <div class="filter-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-muted);">الكمية من</label>
                            <input type="number" wire:model.live.debounce.300ms="min_quantity" placeholder="الحد الأدنى" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        </div>
                        
                        <div class="filter-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-muted);">الكمية إلى</label>
                            <input type="number" wire:model.live.debounce.300ms="max_quantity" placeholder="الحد الأقصى" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Alerts handled by SweetAlert2 globally --}}
        
        <div class="table-responsive">
            <table id="booksTable" wire:loading.remove>
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
                    @forelse($books as $book)
                    <tr wire:key="book-{{ $book->id }}">
                        <td data-label="الرقم الأرشيفي" class="mono-id" style="font-family: 'Courier New', monospace; font-weight: bold; color: var(--primary-blue);">{{ $book->isbn ?? '—' }}</td>
                        <td data-label="عنوان الكتاب">
                            <a href="javascript:void(0)" wire:click="showBook({{ $book->id }})" class="link-primary" style="font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-book-open"></i>
                                {{ $book->book_title }}
                            </a>
                        </td>
                        <td data-label="المؤلف / الناشر">
                            <div style="font-weight: 500;">{{ $book->author }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ $book->publisher }}</div>
                        </td>
                        <td data-label="الكمية">
                            <span class="badge {{ $book->quantity > 0 ? 'staff' : 'error' }}">
                                {{ $book->quantity }} نسخة
                            </span>
                        </td>
                        <td data-label="القسم">
                            @if($book->custom_department)
                                <span class="badge admin">{{ $book->custom_department }}</span>
                            @else
                                <span class="badge staff">{{ $book->department->name_ar ?? 'بدون قسم' }}</span>
                            @endif
                        </td>
                        <td data-label="إجراءات" class="action-buttons">
                            @can('books.edit')
                                <a href="{{ route('books.edit', $book->id) }}" class="btn-edit" title="تعديل"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('books.delete')
                                <button type="button" onclick="confirmDelete({{ $book->id }}, (id) => @this.deleteBook(id))" class="btn-delete" title="حذف"><i class="fas fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-book-open"></i>
                                <h4>لا توجد كتب حالياً</h4>
                                <p>لم يتم العثور على أي كتب تطابق بحثك أو في النظام حالياً.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Loading Skeleton -->
            <div wire:loading style="width: 100%; padding: 20px;">
                @for($i = 0; $i < 5; $i++)
                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <div class="skeleton" style="height: 40px; flex: 1.5;"></div>
                        <div class="skeleton" style="height: 40px; flex: 3;"></div>
                        <div class="skeleton" style="height: 40px; flex: 2;"></div>
                        <div class="skeleton" style="height: 40px; flex: 1;"></div>
                    </div>
                @endfor
            </div>
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
                <!-- Tabs -->
                <div class="tabs" style="margin-bottom: 20px;">
                    <button class="tab-btn active" onclick="switchTab('details')" data-tab="details">📋 التفاصيل</button>
                    <button class="tab-btn" onclick="switchTab('history')" data-tab="history">📜 سجل الإعارات</button>
                </div>

                <!-- Tab Content -->
                <div id="details-tab" class="tab-content active">
                    <div class="preview-info">
                        <div class="preview-row"><span>📖 العنوان:</span><span>{{ $selectedBook['book_title'] }}</span></div>
                        <div class="preview-row"><span>✍️ المؤلف:</span><span>{{ $selectedBook['author'] ?? '—' }}</span></div>
                        <div class="preview-row"><span>🏛️ الناشر:</span><span>{{ $selectedBook['publisher'] ?? '—' }}</span></div>
                        <div class="preview-row"><span>📅 السنة:</span><span>{{ $selectedBook['publication_year'] ?? 'غير محدد' }}</span></div>
                        <div class="preview-row"><span>🔢 الرقم الأرشيفي:</span><span>{{ $selectedBook['isbn'] ?? '—' }}</span></div>
                        <div class="preview-row"><span>📚 الكمية:</span><span>{{ $selectedBook['quantity'] }} نسخة (متاح: {{ $selectedBook['available_quantity'] }})</span></div>
                        <div class="preview-row"><span>🏢 القسم:</span><span>{{ $selectedBook['custom_department'] ?? $selectedBook['department']['name_ar'] ?? '—' }}</span></div>
                        <div class="preview-description">
                            <strong>📝 نبذة:</strong>
                            <p>{{ $selectedBook['description'] ?? 'لا توجد نبذة' }}</p>
                        </div>
                    </div>
                </div>

                <div id="history-tab" class="tab-content">
                    @if(isset($selectedBook['borrowings']) && count($selectedBook['borrowings']) > 0)
                        <div class="borrowing-history">
                            <h4 style="margin-bottom: 15px; color: var(--text-primary);">
                                <i class="fas fa-history"></i> سجل الإعارات ({{ count($selectedBook['borrowings']) })
                            </h4>
                            <div class="history-list">
                                @foreach($selectedBook['borrowings'] as $borrowing)
                                    <div class="history-item" style="background: var(--bg-off-white); padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <div>
                                                <strong>{{ $borrowing['faculty_name'] }}</strong>
                                                <span class="text-muted" style="font-size: 0.85rem;"> - {{ $borrowing['faculty_department'] }}</span>
                                            </div>
                                            <div style="text-align: left;">
                                                @if($borrowing['actual_return_date'])
                                                    <span class="badge success" style="background: #10b981; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">تم الإرجاع</span>
                                                @else
                                                    <span class="badge {{ $borrowing['expected_return_date'] && \Carbon\Carbon::parse($borrowing['expected_return_date'])->isPast() ? 'error' : 'staff' }}" style="padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">
                                                        {{ $borrowing['expected_return_date'] && \Carbon\Carbon::parse($borrowing['expected_return_date'])->isPast() ? 'متأخر' : 'مُعار' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                                            <div><strong>تاريخ الإعارة:</strong> {{ $borrowing['borrow_date'] }}</div>
                                            <div><strong>الموعد المتوقع:</strong> {{ $borrowing['expected_return_date'] }}</div>
                                            @if($borrowing['actual_return_date'])
                                                <div><strong>تاريخ الإرجاع:</strong> {{ $borrowing['actual_return_date'] }}</div>
                                            @endif
                                            @if($borrowing['faculty_contact'])
                                                <div><strong>التواصل:</strong> {{ $borrowing['faculty_contact'] }}</div>
                                            @endif
                                        </div>
                                        @if($borrowing['notes'])
                                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--border-color);">
                                                <strong>ملاحظات:</strong> {{ $borrowing['notes'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-history" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                            <h4>لا توجد إعارات سابقة</h4>
                            <p>لم يتم إعارة هذا الكتاب من قبل</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
            <button wire:click="$set('selectedBook', null)" class="preview-close">إغلاق</button>
        </div>
    </div>
    @endif
</div>

<style>
.tabs {
    display: flex;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 20px;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.3s ease;
    font-weight: 500;
    color: var(--text-muted);
}

.tab-btn:hover {
    color: var(--text-primary);
}

.tab-btn.active {
    color: var(--primary-green);
    border-bottom-color: var(--primary-green);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.badge.success {
    background: #10b981;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}
</style>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}
</script>
