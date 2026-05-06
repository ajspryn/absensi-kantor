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
    <style>
        /* ========================
           SKELETON LOADING SCREEN
           ======================== */
        .skeleton {
            background: linear-gradient(90deg, rgba(0,0,0,0.07) 25%, rgba(0,0,0,0.14) 50%, rgba(0,0,0,0.07) 75%);
            background-size: 200% 100%;
            animation: sk-shimmer 1.6s ease-in-out infinite;
            border-radius: 4px;
        }
        .theme-dark .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.06) 25%, rgba(255,255,255,0.14) 50%, rgba(255,255,255,0.06) 75%);
            background-size: 200% 100%;
            animation: sk-shimmer 1.6s ease-in-out infinite;
        }
        @keyframes sk-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .sk-circle { border-radius: 50% !important; }
        .sk-text    { height: 12px; border-radius: 6px; margin-bottom: 8px; }

        #page-skeleton {
            position: fixed;
            inset: 0;
            z-index: 9998;
            overflow-y: auto;
            overflow-x: hidden;
            display: none;
        }
        #page-skeleton.sk-visible { display: block; }

        .sk-page-loaded .page-content {
            animation: sk-fadein 0.28s ease both;
        }
        @keyframes sk-fadein {
            from { opacity: 0; transform: translateY(7px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">
    <div id="preloader" class="preloader-hide">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>

    <!-- ======================== SKELETON SCREEN (Admin) ======================== -->
    <div id="page-skeleton">
        <!-- Skeleton Header -->
        <div class="header-bar header-fixed header-app header-bar-detached">
            <div class="d-flex align-items-center w-100 px-2 gap-2">
                <div class="skeleton sk-circle" style="width:30px;height:30px;flex-shrink:0;"></div>
                <div class="skeleton flex-grow-1" style="height:14px;border-radius:7px;max-width:200px;"></div>
                <div class="skeleton sk-circle ms-auto" style="width:30px;height:30px;flex-shrink:0;"></div>
            </div>
        </div>

        <!-- Skeleton Content -->
        <div class="page-content header-clear-medium">

            <!-- Stats Row -->
            <div class="card card-style">
                <div class="content py-3">
                    <div class="skeleton mb-3" style="width:40%;height:14px;border-radius:7px;"></div>
                    <div class="row g-2">
                        @foreach ([1,2,3,4] as $__)
                        <div class="col-6">
                            <div class="rounded-s p-2" style="border:1px solid rgba(0,0,0,0.06);">
                                <div class="skeleton sk-circle mb-2" style="width:36px;height:36px;"></div>
                                <div class="skeleton sk-text" style="width:50%;height:18px;"></div>
                                <div class="skeleton" style="width:65%;height:10px;border-radius:5px;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card card-style">
                <div class="content py-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="skeleton" style="width:45%;height:15px;border-radius:7px;"></div>
                        <div class="skeleton rounded-s" style="width:80px;height:30px;"></div>
                    </div>
                    <!-- Table header -->
                    <div class="row g-2 mb-2 px-1">
                        @foreach ([25,30,25,20] as $w)
                        <div class="col" style="flex:0 0 {{ $w }}%;">
                            <div class="skeleton" style="height:10px;border-radius:5px;"></div>
                        </div>
                        @endforeach
                    </div>
                    <div class="divider my-1"></div>
                    <!-- Table rows -->
                    @for ($r = 0; $r < 6; $r++)
                    <div class="d-flex align-items-center py-2 gap-2">
                        <div class="skeleton sk-circle" style="width:32px;height:32px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton sk-text" style="width:{{ [55,65,50,70,60,45][$r] }}%;"></div>
                            <div class="skeleton" style="width:{{ [40,50,38,55,45,35][$r] }}%;height:10px;border-radius:5px;"></div>
                        </div>
                        <div class="skeleton" style="width:58px;height:22px;border-radius:11px;flex-shrink:0;"></div>
                        <div class="d-flex gap-1" style="flex-shrink:0;">
                            <div class="skeleton sk-circle" style="width:26px;height:26px;"></div>
                            <div class="skeleton sk-circle" style="width:26px;height:26px;"></div>
                        </div>
                    </div>
                    @if ($r < 5) <div class="divider my-0"></div> @endif
                    @endfor
                </div>
            </div>

        </div><!-- /page-content -->
    </div><!-- /#page-skeleton -->

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
    <script>
    /* ========================
       SKELETON PAGE TRANSITIONS (Admin)
       ======================== */
    (function () {
        'use strict';
        var sk = document.getElementById('page-skeleton');
        if (!sk) return;

        function syncBg() {
            var bg = window.getComputedStyle(document.body).backgroundColor;
            if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                sk.style.backgroundColor = bg;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncBg();
            sk.classList.remove('sk-visible');
            document.body.classList.add('sk-page-loaded');
        });

        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            var href = a.getAttribute('href') || '';
            if (
                href === '' || href.charAt(0) === '#' ||
                href.startsWith('javascript') ||
                a.dataset.bsToggle || a.dataset.bsDismiss ||
                a.hasAttribute('data-back-button') ||
                a.target === '_blank' ||
                e.ctrlKey || e.metaKey || e.shiftKey || e.altKey
            ) return;
            syncBg();
            sk.classList.add('sk-visible');
        }, true);

        document.addEventListener('submit', function (e) {
            if (e.target.dataset.noSkeleton !== undefined) return;
            syncBg();
            sk.classList.add('sk-visible');
        });

        window.addEventListener('pageshow', function (e) {
            if (e.persisted) sk.classList.remove('sk-visible');
        });
    })();
    </script>
</body>

</html>
