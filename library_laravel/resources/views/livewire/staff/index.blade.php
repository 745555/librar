<div>
    <div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-users"></i> إدارة الموظفين</h3>
            <span class="logo-badge"><i class="fas fa-user-shield"></i> التحكم في صلاحيات الوصول وموظفي المكتبة</span>
        </div>
        
        @if (session()->has('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        
        <div class="card" style="margin-bottom: 30px; background: var(--bg-off-white);">
            <h3><i class="fas {{ $isEditMode ? 'fa-user-edit' : 'fa-user-plus' }}"></i> {{ $isEditMode ? 'تعديل بيانات الموظف' : 'إضافة موظف جديد' }}</h3>
            <form wire:submit.prevent="{{ $isEditMode ? 'updateStaff' : 'addStaff' }}" class="subtle-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المستخدم *</label>
                        <input type="text" wire:model="username" {{ $isEditMode ? 'disabled' : 'required' }} placeholder="أدخل اسم المستخدم">
                        @error('username') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>{{ $isEditMode ? 'كلمة مرور جديدة (اختياري)' : 'كلمة المرور *' }}</label>
                        <input type="password" wire:model="{{ $isEditMode ? 'new_password' : 'password' }}" {{ $isEditMode ? '' : 'required' }} placeholder="{{ $isEditMode ? 'اتركها فارغة لعدم التغيير' : 'أدخل كلمة المرور' }}">
                        @error($isEditMode ? 'new_password' : 'password') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم الكامل *</label>
                        <input type="text" wire:model="full_name" required placeholder="أدخل الاسم الكامل">
                        @error('full_name') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" wire:model="email" placeholder="example@library.edu">
                        @error('email') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>رقم الجوال</label>
                        <input type="tel" wire:model="phone" placeholder="05xxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>الصلاحية *</label>
                        <select wire:model="role" required>
                            <option value="staff">موظف</option>
                            <option value="admin">مدير</option>
                        </select>
                        @error('role') <span class="alert error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="btn-group" style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn-primary" style="flex: 2;">
                        <i class="fas fa-save"></i> {{ $isEditMode ? 'تحديث البيانات' : 'إضافة الموظف' }}
                    </button>
                    @if($isEditMode)
                        <button type="button" wire:click="resetForm" class="btn-secondary" style="flex: 1;">إلغاء</button>
                    @endif
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>بيانات التواصل</th>
                        <th>الصلاحية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff_list as $staff)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="user-avatar-mini" style="width: 40px; height: 40px; border-radius: 50%; background: var(--bg-beige); display: flex; align-items: center; justify-content: center; color: var(--primary-blue);">
                                    @if($staff->avatar)
                                        <img src="{{ asset('storage/avatars/' . $staff->avatar) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $staff->full_name }}</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">@ {{ $staff->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem;"><i class="fas fa-envelope" style="width: 20px;"></i> {{ $staff->email ?? '—' }}</div>
                            <div style="font-size: 0.9rem;"><i class="fas fa-phone" style="width: 20px;"></i> {{ $staff->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $staff->role }}">
                                {{ $staff->role == 'admin' ? 'مدير النظام' : 'موظف مكتبة' }}
                            </span>
                        </td>
                        <td class="action-buttons">
                            <button class="btn-edit" wire:click="editStaff({{ $staff->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($staff->id != auth()->id())
                            <button class="btn-delete" onclick="confirm('هل أنت متأكد من حذف هذا الموظف؟') || event.stopImmediatePropagation()" wire:click="deleteStaff({{ $staff->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
