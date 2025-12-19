<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/userdetails.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar_navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/misc.css') }}">
    <title>WasteWise Admin - {{ $pageTitle ?? 'Dashboard' }}</title>
    @stack('styles')
</head>
<body style="background-color: #ffffff;">
    {{-- Include Sidebar Component --}}
    @include('components.adminsidebar')
    
    <div class="main-content" id="mainContent" style="background-color: #ffffff;">
        {{-- Include Top Navbar with Page Title --}}
        @include('components.admin-top-navbar', ['title' => $pageTitle ?? 'Admin Dashboard'])
        
        <div style="padding: 2rem; background-color: #ffffff; min-height: calc(100vh - 80px);">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
