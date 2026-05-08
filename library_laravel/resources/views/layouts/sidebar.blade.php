@php
    $current_route = request()->route() ? request()->route()->getName() : 'dashboard';
@endphp
<aside class="sidebar">
    <div class="logo">
        <div class="logo-header">
            <i class="fas fa-graduation-cap"></i>
            <div>
                <h2>كلية التقنية الهندسية جنزور</h2>
                <div class="logo-badge">
                    <i class="fas fa-book"></i>
                    <span>المكتبة</span>
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
            <div class="user-role-mini">{{ (auth()->user() && auth()->user()->role == 'admin') ? 'مدير النظام' : 'موظف' }}</div>
        </div>
    </div>
    
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
        @if(auth()->user() && auth()->user()->role == 'admin')
        <li class="nav-item">
            <a href="{{ route('staff.index') }}" class="nav-link {{ str_starts_with($current_route, 'staff') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span>إدارة الموظفين</span>
            </a>
        </li>
        @endif
    </ul>
    
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
