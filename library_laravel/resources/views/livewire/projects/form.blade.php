@section('title', '{{ $projectId ? "تعديل مشروع" : "إضافة مشروع جديد" }}')

<div>
    <div class="card standalone-card">
        <div class="page-header">
            <h3><i class="fas fa-plus-circle"></i> {{ $projectId ? 'تعديل مشروع' : 'إضافة مشروع جديد' }}</h3>
            <span class="logo-badge"><i class="fas fa-info-circle"></i> الرقم الأرشيفي للمشروع يجب أن يكون فريداً</span>
        </div>
        
        <form wire:submit.prevent="save" class="subtle-form">
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-hashtag"></i> الرقم الأرشيفي *</label>
                    <input type="text" wire:model.live="archive_number" required placeholder="مثال: CS-2024-001" class="@error('archive_number') input-error @enderror">
                    @error('archive_number') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="required"><i class="fas fa-tag"></i> اسم المشروع *</label>
                    <input type="text" wire:model="project_name" required placeholder="أدخل اسم المشروع كاملاً" class="@error('project_name') input-error @enderror">
                    @error('project_name') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-chalkboard-user"></i> اسم المشرف *</label>
                    <input type="text" wire:model="supervisor" required placeholder="مثال: د. أحمد محمد" class="@error('supervisor') input-error @enderror">
                    @error('supervisor') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="required"><i class="fas fa-user-graduate"></i> اسم الطالب *</label>
                    <input type="text" wire:model="student_name" required placeholder="مثال: علي حسن" class="@error('student_name') input-error @enderror">
                    @error('student_name') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar-alt"></i> الفصل الدراسي *</label>
                    <select wire:model="semester" required class="@error('semester') input-error @enderror">
                        <option value="">اختر الفصل</option>
                        <option value="الربيعي">الفصل الربيعي (Spring)</option>
                        <option value="الخريفي">الفصل الخريفي (Fall)</option>
                        <option value="الصيفي">الفصل الصيفي (Summer)</option>
                    </select>
                    @error('semester') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label class="required"><i class="fas fa-calendar"></i> السنة *</label>
                    <select wire:model="year" required class="@error('year') input-error @enderror">
                        <option value="">اختر السنة</option>
                        @for($y = 2020; $y <= 2026; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label class="required"><i class="fas fa-building"></i> القسم الأكاديمي *</label>
                <select wire:model="department_id" required class="@error('department_id') input-error @enderror">
                    <option value="">اختر القسم</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                    @endforeach
                </select>
                @error('department_id') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> 📝 وصف المشروع</label>
                <textarea wire:model="description" rows="4" placeholder="أدخل وصفاً للمشروع..."></textarea>
                @error('description') <span class="alert error">{{ $message }}</span> @enderror
            </div>
            
            <div class="btn-group" style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" style="flex: 1;">
                    <span wire:loading.remove><i class="fas fa-save"></i> {{ $projectId ? 'تحديث المشروع' : 'إضافة المشروع' }}</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> جاري الحفظ...</span>
                </button>
                <a href="{{ route('projects.index') }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none;"><i class="fas fa-arrow-right"></i> إلغاء</a>
            </div>
        </form>
    </div>
</div>
