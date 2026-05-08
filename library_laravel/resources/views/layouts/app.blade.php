<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'نظام المكتبة') }} - @yield('title')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
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
</body>
</html>
