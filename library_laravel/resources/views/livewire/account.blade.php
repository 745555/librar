@section('title', 'حسابي')

<div>
    <div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-user-circle"></i> حسابي</h3>
        </div>

        @if (session()->has('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="stats-two-col">
            <div class="card">
                <h3><i class="fas fa-id-card"></i> معلومات الحساب</h3>
                <div class="subtle-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>اسم المستخدم</label>
                            <input type="text" value="{{ auth()->user()->username }}" disabled>
                        </div>
                        <div class="form-group">
                            <label>الدور</label>
                            <input type="text" value="{{ auth()->user()->role === 'admin' ? 'مدير' : 'موظف' }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>تاريخ إنشاء الحساب</label>
                        <input type="text" value="{{ auth()->user()->created_at->format('Y-m-d') }}" disabled>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3><i class="fas fa-camera"></i> الصورة الشخصية</h3>
                <div class="avatar-section" style="text-align: center;">
                    <div class="current-avatar" style="margin-bottom: 25px;">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-preview" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-green); box-shadow: var(--shadow-md);">
                        @else
                            <div class="avatar-placeholder" style="font-size: 140px; color: var(--bg-beige);"><i class="fas fa-user-circle"></i></div>
                        @endif
                    </div>
                    <div class="avatar-form subtle-form">
                        <div class="form-group">
                            <label>تغيير الصورة</label>
                            <input type="file" wire:model="avatar" accept="image/*" style="padding: 10px;">
                            <div wire:loading wire:target="avatar" class="hint-text"><i class="fas fa-spinner fa-spin"></i> جاري الرفع...</div>
                            @error('avatar') <span class="alert error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-two-col">
            <div class="card">
                <h3><i class="fas fa-user-edit"></i> تعديل البيانات الشخصية</h3>
                <form wire:submit.prevent="saveProfile" class="subtle-form">
                    <div class="form-group">
                        <label class="required">الاسم الكامل</label>
                        <input type="text" wire:model="full_name" required>
                        @error('full_name') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" wire:model="email">
                        @error('email') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>رقم الجوال</label>
                        <input type="tel" wire:model="phone">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> حفظ البيانات
                    </button>
                </form>
            </div>

            <div class="card">
                <h3><i class="fas fa-lock"></i> تغيير كلمة المرور</h3>
                <form wire:submit.prevent="changePassword" class="subtle-form">
                    <div class="form-group">
                        <label class="required">كلمة المرور الحالية</label>
                        <input type="password" wire:model="current_password" required>
                        @error('current_password') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="required">كلمة المرور الجديدة</label>
                        <input type="password" wire:model="new_password" required>
                        @error('new_password') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="required">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" wire:model="new_password_confirmation" required>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                        <i class="fas fa-key"></i> تحديث كلمة المرور
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
