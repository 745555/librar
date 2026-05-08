<div>
    <style>
        .radio-group { display: flex; gap: 20px; margin-top: 8px; }
        .radio-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; cursor: pointer; }
        .dept-existing, .dept-custom { transition: all 0.3s; }
        .hidden { display: none; }
        .hint-small { color: #64748b; display: block; margin-top: 5px; }
        .standalone-card { max-width: 800px; margin: 0 auto; padding: 40px; }
    </style>

    <div class="card standalone-card">
        <div class="page-header">
            <h3><i class="fas fa-book"></i> {{ $bookId ? 'تعديل كتاب' : 'إضافة كتاب جديد' }}</h3>
            <span class="logo-badge"><i class="fas fa-info-circle"></i> الرقم الأرشيفي للكتاب هو رقم ISBN</span>
        </div>
        
        <form wire:submit.prevent="save" class="subtle-form">
            <div class="form-row">
                <div class="form-group">
                    <label>📚 الرقم الأرشيفي (ISBN) *</label>
                    <input type="text" wire:model="isbn" required placeholder="978-999-123-456-7">
                    @error('isbn') <span class="alert error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>📖 عنوان الكتاب *</label>
                    <input type="text" wire:model="book_title" required placeholder="عنوان الكتاب">
                    @error('book_title') <span class="alert error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>✍️ المؤلف *</label>
                    <input type="text" wire:model="author" required placeholder="اسم المؤلف">
                    @error('author') <span class="alert error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>🏛️ الناشر</label>
                    <input type="text" wire:model="publisher" placeholder="اسم الناشر">
                    @error('publisher') <span class="alert error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>📅 سنة النشر *</label>
                    <select wire:model="publication_year" required>
                        <option value="">اختر السنة</option>
                        @for($y = 2000; $y <= 2026; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    @error('publication_year') <span class="alert error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>🔢 الكمية المتاحة *</label>
                    <input type="number" wire:model="quantity" required min="1">
                    @error('quantity') <span class="alert error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-group" x-data="{ type: '{{ $custom_department ? 'custom' : 'existing' }}' }">
                <label>🏢 القسم *</label>
                <div class="radio-group" style="display: flex; gap: 25px; margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                        <input type="radio" name="department_type" value="existing" x-model="type" @click="$wire.set('custom_department', '')"> اختيار من القائمة
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                        <input type="radio" name="department_type" value="custom" x-model="type" @click="$wire.set('department_id', null)"> إدخال قسم جديد
                    </label>
                </div>
                
                <div x-show="type === 'existing'" class="dept-existing">
                    <select wire:model="department_id">
                        <option value="">اختر القسم</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div x-show="type === 'custom'" class="dept-custom">
                    <input type="text" wire:model="custom_department" placeholder="أدخل اسم القسم (مثال: قسم إدارة الأعمال)">
                    <small class="hint-text">📝 يمكنك إضافة قسم غير موجود في القائمة (مثل قسم خارج الكلية)</small>
                </div>
                
                @error('department_id') <span class="alert error">{{ $message }}</span> @enderror
                @error('custom_department') <span class="alert error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> 📝 نبذة مختصرة عن الكتاب</label>
                <textarea wire:model="description" rows="4" placeholder="أدخل نبذة مختصرة عن الكتاب..."></textarea>
                @error('description') <span class="alert error">{{ $message }}</span> @enderror
            </div>
            
            <div class="btn-group" style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="flex: 1;">
                    <span wire:loading.remove><i class="fas fa-save"></i> {{ $bookId ? 'تحديث الكتاب' : 'إضافة الكتاب' }}</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> جاري الحفظ...</span>
                </button>
                <a href="{{ route('books.index') }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none;"><i class="fas fa-arrow-right"></i> إلغاء</a>
            </div>
        </form>
    </div>
</div>
