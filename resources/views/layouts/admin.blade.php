<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover, user-scalable=no" />
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('template/styles/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/fonts/bootstrap-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/styles/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/styles/admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">
    <div id="preloader" class="preloader-hide">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>
    <div id="page">
        @hasSection('header')
            @yield('header')
        @else
            @include('admin.header')
        @endif

        @hasSection('sidebar')
            @yield('sidebar')
        @else
            @include('admin.sidebar')
        @endif
        <div class="page-content @yield('page-class', 'header-clear-medium')">
            @yield('content')
        </div>
        @yield('footer')
    </div>
    <script src="{{ asset('template/scripts/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/scripts/custom.js') }}"></script>
    @stack('scripts')
</body>

</html>
