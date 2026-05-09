<div>
    <style>
        .borrow-container { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .card h3 { color: #2c3e50; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #667eea; display: inline-block; font-size: 24px; }
        .card h3 i { color: #667eea; margin-right: 10px; }
        .btn-pdf { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: white; border: none; padding: 14px 32px; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; position: relative; overflow: hidden; flex: 1; }
        .btn-pdf:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4); }
    </style>

    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-pen-alt"></i> {{ $borrowId ? 'تعديل طلب الإعارة' : 'نموذج طلب إعارة' }}</h3>
            <span class="logo-badge"><i class="fas fa-info-circle"></i> تسجيل إعارة جديدة لأعضاء هيئة التدريس</span>
        </div>
        
        {{-- Alerts handled by SweetAlert2 globally --}}

        <form wire:submit.prevent="save" class="subtle-form">
            <div class="form-row">
                <div class="form-group">
                    <label class="required">اسم عضو هيئة التدريس</label>
                    <input type="text" wire:model="faculty_name" required placeholder="د. أحمد محمد" class="@error('faculty_name') input-error @enderror">
                    @error('faculty_name') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="required">القسم</label>
                    <input type="text" wire:model="faculty_department" required placeholder="قسم الحاسب الآلي" class="@error('faculty_department') input-error @enderror">
                    @error('faculty_department') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>معلومات التواصل</label>
                    <input type="text" wire:model="faculty_contact" placeholder="رقم الهاتف أو البريد الإلكتروني">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">عنوان الكتاب</label>
                    <input type="text" wire:model="book_title" required placeholder="عنوان الكتاب" class="@error('book_title') input-error @enderror">
                    @error('book_title') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>رقم ISBN</label>
                    <input type="text" wire:model="book_isbn" placeholder="رقم ISBN">
                </div>
                <div class="form-group">
                    <label class="required">تاريخ الإعارة</label>
                    <input type="date" wire:model="borrow_date" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">تاريخ الإرجاع المتوقع</label>
                    <input type="date" wire:model="expected_return_date" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>ملاحظات</label>
                    <textarea wire:model="notes" rows="2" placeholder="ملاحظات إضافية"></textarea>
                </div>
            </div>
            
            <div class="btn-group" style="display: flex; gap: 15px; margin-top: 20px;">
                @if ($borrowId)
                    @can('borrowings.edit')
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="flex: 2;">
                            <span wire:loading.remove><i class="fas fa-save"></i> تحديث الطلب</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin"></i> جاري الحفظ...</span>
                        </button>
                    @endcan
                @else
                    @can('borrowings.create')
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="flex: 2;">
                            <span wire:loading.remove><i class="fas fa-save"></i> حفظ وتسجيل</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin"></i> جاري الحفظ...</span>
                        </button>
                    @endcan
                @endif
                @if ($borrowId)
                    <button type="button" class="btn-secondary" wire:click="resetForm" style="flex: 1;">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                @else
                    <button type="button" class="btn-secondary" onclick="window.print()" style="flex: 1;">
                        <i class="fas fa-print"></i> طباعة النموذج
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-history"></i> طلبات الإعارة المسجلة</h3>
            <div class="search-box" style="margin-bottom: 0;">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="بحث باسم الموظف أو الكتاب..." style="padding: 10px 15px; border-radius: 50px;">
            </div>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>عضو هيئة التدريس</th>
                        <th>الكتاب</th>
                        <th>تاريخ الإعارة</th>
                        <th>تاريخ الإرجاع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $fb)
                    <tr>
                        <td data-label="عضو هيئة التدريس"><strong>{{ $fb->faculty_name }}</strong><br><small class="text-muted">{{ $fb->faculty_department }}</small></td>
                        <td data-label="الكتاب">{{ $fb->book_title }}</td>
                        <td data-label="تاريخ الإعارة">{{ $fb->borrow_date->format('Y-m-d') }}</td>
                        <td data-label="تاريخ الإرجاع">
                            @php
                                $isOverdue = $fb->expected_return_date->isPast();
                            @endphp
                            <span class="badge {{ $isOverdue ? 'error' : 'staff' }}" style="{{ $isOverdue ? 'background: rgba(255, 107, 107, 0.1); color: #ff6b6b;' : '' }}">
                                {{ $fb->expected_return_date->format('Y-m-d') }}
                            </span>
                        </td>
                        <td data-label="الإجراءات" class="action-buttons">
                            @can('borrowings.edit')
                                <button wire:click="editBorrowing({{ $fb->id }})" class="btn-edit"><i class="fas fa-edit"></i></button>
                            @endcan
                            @can('borrowings.delete')
                                <button type="button" onclick="confirmDelete({{ $fb->id }}, (id) => @this.deleteBorrowing(id))" class="btn-delete"><i class="fas fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 50px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: var(--bg-beige); display: block; margin-bottom: 15px;"></i>
                            <span class="text-muted">لا توجد طلبات إعارة حالياً</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 25px;">
            {{ $borrowings->links() }}
        </div>
    </div>
</div>
