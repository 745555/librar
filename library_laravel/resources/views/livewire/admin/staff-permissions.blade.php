@section('title', 'صلاحيات الموظفين')

<div class="p-4 md:p-8">
    <style>
        .staff-manager-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            min-height: calc(100vh - 120px);
        }
        .users-sidebar {
            background: white;
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
        }
        .search-wrapper {
            padding: 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        .search-input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: white;
            font-size: 14px;
            transition: all 0.3s;
        }
        .search-input:focus {
            border-color: #28a270;
            box-shadow: 0 0 0 4px rgba(40, 162, 112, 0.1);
            outline: none;
        }
        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }
        .user-card {
            padding: 16px;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid transparent;
        }
        .user-card:hover {
            background: #f8fafc;
            transform: translateX(-5px);
        }
        .user-card.active {
            background: linear-gradient(135deg, #28a270 0%, #1e8e5f 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(40, 162, 112, 0.3);
            border-color: #218c61;
        }
        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            background: #f1f5f9;
            color: #475569;
            flex-shrink: 0;
        }
        .active .avatar-circle {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .permission-group-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.02);
            transition: transform 0.3s;
        }
        .permission-group-card:hover {
            transform: translateY(-2px);
        }
        .permission-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-radius: 16px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        .permission-toggle:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .permission-toggle.active {
            background: rgba(40, 162, 112, 0.05);
            border-color: #28a270;
            color: #1e8e5f;
        }
        .toggle-switch {
            width: 44px;
            height: 24px;
            background: #cbd5e1;
            border-radius: 50px;
            position: relative;
            transition: all 0.3s;
        }
        .active .toggle-switch {
            background: #28a270;
        }
        .toggle-knob {
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 3px;
            right: 3px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .active .toggle-knob {
            right: 23px;
        }
        
        @media (max-width: 1024px) {
            .staff-manager-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="staff-manager-container">
        <!-- Sidebar -->
        <div class="users-sidebar">
            <div class="search-wrapper">
                <div class="relative">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live="search" class="search-input" placeholder="ابحث عن موظف...">
                </div>
            </div>
            
            <div class="user-list">
                @foreach($users as $user)
                    <div wire:click="selectUser({{ $user['id'] }})" 
                         class="user-card {{ $selectedUserId == $user['id'] ? 'active' : '' }}">
                        <div class="avatar-circle">
                            {{ substr($user['full_name'] ?? $user['username'], 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold truncate">{{ $user['full_name'] ?? $user['username'] }}</div>
                            <div class="text-xs opacity-70 truncate">{{ $user['email'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-area">
            @if($selectedUserId)
                <div class="animate-fade-in">
                    <!-- User Header -->
                    <div class="bg-white rounded-3xl p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-6 text-right">
                            <div class="w-20 h-20 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center text-3xl font-black shadow-inner">
                                {{ substr($selectedUserName, 0, 1) }}
                            </div>
                            <div>
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold mb-2 inline-block">حساب موظف معتمد</span>
                                <h2 class="text-3xl font-black text-slate-800">{{ $selectedUserName }}</h2>
                                <p class="text-slate-400 flex items-center gap-2 mt-1">
                                    <i class="fas fa-user-shield"></i> التحكم في صلاحيات الوصول للنظام
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Alerts handled by SweetAlert2 globally --}}

                    <!-- Roles Section -->
                    <div class="permission-group-card">
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                            <i class="fas fa-crown text-amber-500"></i> الأدوار والرتب
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($allRoles as $role)
                                <div wire:click="toggleRole('{{ $role['name'] }}')" 
                                     class="permission-toggle {{ in_array($role['name'], $userRoles) ? 'active' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fas {{ $role['name'] == 'Admin' ? 'fa-user-cog' : ($role['name'] == 'Librarian' ? 'fa-book-reader' : 'fa-user') }}"></i>
                                        <span class="font-bold">{{ $role['name'] }}</span>
                                    </div>
                                    <div class="toggle-switch">
                                        <div class="toggle-knob"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissions Sections -->
                    @foreach($allPermissions as $group => $perms)
                        <div class="permission-group-card">
                            <div class="flex items-center justify-between mb-8 border-b border-slate-50 pb-4">
                                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                                    @php
                                        $icon = $group == 'books' ? 'fa-book' : ($group == 'projects' ? 'fa-graduation-cap' : ($group == 'staff' ? 'fa-users' : ($group == 'borrowings' ? 'fa-hand-holding-heart' : 'fa-cog')));
                                        $title = $group == 'books' ? 'إدارة الكتب والمكتبة' : ($group == 'projects' ? 'إدارة مشاريع التخرج' : ($group == 'staff' ? 'إدارة شؤون الموظفين' : ($group == 'borrowings' ? 'إدارة الإعارات' : 'إدارة النظام')));
                                    @endphp
                                    <i class="fas {{ $icon }} text-green-600"></i> {{ $title }}
                                </h3>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ count($perms) }} صلاحيات</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($perms as $permission)
                                    <div wire:click="togglePermission('{{ $permission['name'] }}')" 
                                         class="permission-toggle {{ in_array($permission['name'], $userPermissions) ? 'active' : '' }}">
                                        <div class="flex flex-col">
                                            <span class="font-bold">
                                                @php
                                                    $action = explode('.', $permission['name'])[1] ?? $permission['name'];
                                                    echo $action == 'view' ? 'عرض السجلات' : ($action == 'create' ? 'إضافة جديد' : ($action == 'edit' ? 'تعديل البيانات' : ($action == 'delete' ? 'حذف السجلات' : ($action == 'manage' ? 'إدارة كاملة' : $action))));
                                                @endphp
                                            </span>
                                            <span class="text-[10px] opacity-60 font-mono tracking-tighter">{{ $permission['name'] }}</span>
                                        </div>
                                        <div class="toggle-switch">
                                            <div class="toggle-knob"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl p-20 flex flex-col items-center justify-center text-center shadow-sm border border-dashed border-gray-200">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-8">
                        <i class="fas fa-user-lock text-6xl text-slate-200"></i>
                    </div>
                    <h2 class="text-3xl font-black text-slate-400">مركز التحكم في الصلاحيات</h2>
                    <p class="text-slate-400 mt-4 max-w-sm">يرجى اختيار موظف من القائمة اليمنى للبدء في تخصيص صلاحيات الوصول الخاصة به</p>
                </div>
            @endif
        </div>
    </div>
</div>
