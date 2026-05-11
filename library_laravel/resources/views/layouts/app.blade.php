<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f2b3d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <title>نظام المكتبة - @yield('title')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <header class="mobile-header">
            <div class="mobile-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>نظام المكتبة</span>
            </div>
            <button id="sidebarToggle" class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </header>

        @include('layouts.sidebar')
        
        <main class="main-content">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
    <script>
        // Enhanced mobile sidebar handling
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                this.querySelector('i').classList.toggle('fa-bars');
                this.querySelector('i').classList.toggle('fa-times');
                
                // Prevent body scroll when sidebar is open on mobile
                if (sidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                const isMobile = window.innerWidth <= 767;
                
                if (isMobile && !isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.querySelector('i').classList.add('fa-bars');
                    sidebarToggle.querySelector('i').classList.remove('fa-times');
                    document.body.style.overflow = '';
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 767) {
                    sidebar.classList.remove('active');
                    sidebarToggle.querySelector('i').classList.add('fa-bars');
                    sidebarToggle.querySelector('i').classList.remove('fa-times');
                    document.body.style.overflow = '';
                }
            });
        }

        // Livewire SweetAlert Listener
        window.addEventListener('swal:success', event => {
            Swal.fire({
                icon: 'success',
                title: 'تمت العملية بنجاح',
                text: event.detail[0].message,
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#28a270'
            });
        });

        window.addEventListener('swal:error', event => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: event.detail[0].message,
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#185f84'
            });
        });

        // SweetAlert Session Messages (for redirects)
        @if(session()->has('success'))
            Swal.fire({
                icon: 'success',
                title: 'تمت العملية بنجاح',
                text: "{{ session('success') }}",
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#28a270'
            });
        @endif

        @if(session()->has('error'))
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: "{{ session('error') }}",
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#185f84'
            });
        @endif

        // Global Delete Confirmation
        window.confirmDelete = function(id, callback) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، قم بالحذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    callback(id);
                }
            });
        }
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
                toggle.querySelector('i').classList.add('fa-bars');
                toggle.querySelector('i').classList.remove('fa-times');
            }
        });
        // Livewire error handling
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        alert('انتهت صلاحية الجلسة، يرجى تحديث الصفحة.');
                        location.reload();
                        preventDefault();
                    }
                    if (status === 500) {
                        console.error('Server error occurred');
                    }
                })
            })
        })
    </script>
</body>
</html>
