@php
    $current_route = request()->route() ? request()->route()->getName() : 'dashboard';
@endphp
<aside class="sidebar">
    <div class="logo">
        <div class="logo-header">
            <div class="logo-icon-wrapper">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="logo-text">
                <h2>كلية التقنية الهندسية</h2>
                <h3>جنزور</h3>
                <div class="logo-badge">
                    <i class="fas fa-book-open"></i>
                    <span>نظام المكتبة</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="user-profile-mini">
        <div class="user-avatar-mini">
            @if(auth()->user() && auth()->user()->avatar)
                <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" alt="Profile Image" class="user-avatar-img">
            @else
                <i class="fas fa-user-circle"></i>
            @endif
        </div>
        <div class="user-info-mini">
            <div class="user-name-mini">{{ auth()->user()->full_name ?? 'ضيف' }}</div>
            <div class="user-role-mini">{{ (auth()->user() && auth()->user()->roles->count() > 0) ? auth()->user()->roles->first()->name : (auth()->user()->role == 'admin' ? 'مدير النظام' : 'موظف') }}</div>
        </div>
    </div>
    
    <div class="nav-menu-wrapper" style="flex: 1; overflow-y: auto;">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ $current_route == 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>لوحة التحكم</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('books.index') }}" class="nav-link {{ str_starts_with($current_route, 'books') ? 'active' : '' }}">
                    <i class="fas fa-book"></i><span>الكتب </span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('projects.index') }}" class="nav-link {{ str_starts_with($current_route, 'projects') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-user"></i><span>المشاريع</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('borrowings.index') }}" class="nav-link {{ str_starts_with($current_route, 'borrowings') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-heart"></i><span>طلبات الإعارة</span>
                </a>
            </li>
            @can('staff.view')
            <li class="nav-item">
                <a href="{{ route('staff.index') }}" class="nav-link {{ $current_route == 'staff.index' ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>إدارة الموظفين</span>
                </a>
            </li>
            @endcan
            
            @can('system.manage')
            <li class="nav-item">
                <a href="{{ route('staff.permissions') }}" class="nav-link {{ $current_route == 'staff.permissions' ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i><span>صلاحيات الموظفين</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('activity-logs') }}" class="nav-link {{ $current_route == 'activity-logs' ? 'active' : '' }}">
                    <i class="fas fa-history"></i><span>سجلات الأنشطة</span>
                </a>
            </li>
            @endcan
        </ul>
    </div>
    
    <div class="account-section">
        <a href="{{ route('account') }}" class="nav-link account-link {{ $current_route == 'account' ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i><span>حسابي</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">
                <i class="fas fa-sign-out-alt"></i><span>تسجيل الخروج</span>
            </a>
        </form>
    </div>
</aside>
