@section('title', 'إدارة الموظفين')

<div>
    <div class="borrow-theme">
    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-users"></i> إدارة الموظفين</h3>
            <span class="logo-badge"><i class="fas fa-user-shield"></i> التحكم في صلاحيات الوصول وموظفي المكتبة</span>
        </div>
        
        {{-- Alerts handled by SweetAlert2 globally --}}
        
        @can('staff.create')
        @if(!$isEditMode)
        <div class="card" style="margin-bottom: 30px; background: var(--bg-off-white);">
            <h3><i class="fas fa-user-plus"></i> إضافة موظف جديد</h3>
            <form wire:submit.prevent="addStaff" class="subtle-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المستخدم *</label>
                        <input type="text" wire:model="username" required placeholder="أدخل اسم المستخدم" class="@error('username') input-error @enderror">
                        @error('username') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور *</label>
                        <input type="password" wire:model="password" required placeholder="أدخل كلمة المرور" class="@error('password') input-error @enderror">
                        @error('password') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم الكامل *</label>
                        <input type="text" wire:model="full_name" required placeholder="أدخل الاسم الكامل" class="@error('full_name') input-error @enderror">
                        @error('full_name') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
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
                        <i class="fas fa-save"></i> إضافة الموظف
                    </button>
                </div>
            </form>
        </div>
        @endif
        @endcan

        @can('staff.edit')
        @if($isEditMode)
        <div class="card" style="margin-bottom: 30px; background: var(--bg-off-white);">
            <h3><i class="fas fa-user-edit"></i> تعديل بيانات الموظف</h3>
            <form wire:submit.prevent="updateStaff" class="subtle-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم المستخدم *</label>
                        <input type="text" wire:model="username" required placeholder="أدخل اسم المستخدم" class="@error('username') input-error @enderror">
                        @error('username') <span class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>كلمة مرور جديدة (اختياري)</label>
                        <input type="password" wire:model="new_password" placeholder="اتركها فارغة لعدم التغيير">
                        @error('new_password') <span class="alert error">{{ $message }}</span> @enderror
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
                        <i class="fas fa-save"></i> تحديث البيانات
                    </button>
                    <button type="button" wire:click="resetForm" class="btn-secondary" style="flex: 1;">إلغاء</button>
                </div>
            </form>
        </div>
        @endif
        @endcan
        
        <div class="table-responsive">
            <table wire:loading.remove>
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>بيانات التواصل</th>
                        <th>الصلاحية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff_list as $staff)
                    <tr wire:key="staff-{{ $staff->id }}">
                        <td data-label="الموظف">
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
                        <td data-label="بيانات التواصل">
                            <div style="font-size: 0.9rem;"><i class="fas fa-envelope" style="width: 20px;"></i> {{ $staff->email ?? '—' }}</div>
                            <div style="font-size: 0.9rem;"><i class="fas fa-phone" style="width: 20px;"></i> {{ $staff->phone ?? '—' }}</div>
                        </td>
                        <td data-label="الصلاحية">
                            <span class="badge {{ $staff->role }}">
                                {{ $staff->role == 'admin' ? 'مدير النظام' : 'موظف مكتبة' }}
                            </span>
                        </td>
                        <td data-label="إجراءات" class="action-buttons">
                            @can('staff.edit')
                                <button class="btn-edit" wire:click="editStaff({{ $staff->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            @can('staff.delete')
                                @if($staff->id != auth()->id())
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $staff->id }}, (id) => @this.deleteStaff(id))">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h4>لا يوجد موظفون</h4>
                                <p>لم يتم العثور على أي موظفين حالياً في النظام.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Loading Skeleton -->
            <div wire:loading style="width: 100%; padding: 20px;">
                @for($i = 0; $i < 3; $i++)
                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <div class="skeleton" style="height: 50px; flex: 2;"></div>
                        <div class="skeleton" style="height: 50px; flex: 1.5;"></div>
                        <div class="skeleton" style="height: 50px; flex: 1;"></div>
                        <div class="skeleton" style="height: 50px; flex: 1;"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
