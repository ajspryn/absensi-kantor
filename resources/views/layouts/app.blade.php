<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover, user-scalable=no" />
    <title>@yield('title', 'Aplikasi Absensi')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('template/styles/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/fonts/bootstrap-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/styles/style.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Absensi">
    <meta name="msapplication-TileImage" content="{{ asset('icons/icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#007bff">

    <!-- App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ App\Models\AppSetting::getFaviconUrl() }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('icons/icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('icons/icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('icons/icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('icons/icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('icons/icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('icons/icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('icons/icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-180x180.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Mobile-specific improvements */
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 95%;
                margin: 1rem auto;
            }

            .form-control {
                min-height: 48px;
                font-size: 16px;
                /* Prevents zoom on iOS */
            }

            .btn-l {
                min-height: 48px;
                padding: 12px 20px;
            }

            /* Improve touch targets */
            .dropdown-toggle {
                min-width: 44px;
                min-height: 44px;
            }

            /* Camera video responsive */
            #camera,
            #captured-photo {
                max-width: 100%;
                height: auto !important;
            }

            /* Better spacing for mobile */
            .card .content {
                padding: 20px 15px;
            }

            /* Improve modal on small screens */
            .modal-body {
                padding: 15px;
            }

            /* Better button spacing */
            .btn+.btn {
                margin-left: 8px;
            }

            /* Fix input focus zoom on iOS */
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="tel"],
            input[type="date"],
            select,
            textarea {
                font-size: 16px;
            }
        }

        /* Loading states */
        .loading {
            pointer-events: none;
            opacity: 0.6;
        }

        /* Better touch feedback */
        .btn:active {
            transform: scale(0.98);
        }

        /* Improved card shadows on mobile */
        @media (max-width: 768px) {
            .card-style {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
        }

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

        /* Subtle fade-in for page content on load */
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

<body class="theme-light">

    <div id="preloader" class="preloader-hide">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>

    <!-- ======================== SKELETON SCREEN ======================== -->
    <div id="page-skeleton">
        <!-- Skeleton Header -->
        <div class="header-bar header-fixed header-app header-bar-detached">
            <div class="skeleton sk-circle" style="width:34px;height:34px;flex-shrink:0;"></div>
            <div class="skeleton mx-auto" style="width:130px;height:15px;border-radius:8px;"></div>
            <div class="d-flex gap-2">
                <div class="skeleton sk-circle" style="width:30px;height:30px;"></div>
                <div class="skeleton sk-circle" style="width:30px;height:30px;"></div>
            </div>
        </div>

        <!-- Skeleton Content -->
        <div class="page-content header-clear-medium">

            <!-- Card 1: Welcome -->
            <div class="card card-style">
                <div class="content py-3">
                    <div class="d-flex align-items-start">
                        <div class="skeleton sk-circle me-3" style="width:65px;height:65px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton sk-text" style="width:42%;height:10px;"></div>
                            <div class="skeleton sk-text" style="width:72%;height:14px;"></div>
                            <div class="skeleton sk-text mb-2" style="width:55%;height:10px;"></div>
                            <div class="d-flex gap-1">
                                <div class="skeleton" style="width:75px;height:20px;border-radius:12px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="skeleton sk-circle me-2" style="width:28px;height:28px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton sk-text" style="width:50%;height:12px;"></div>
                            <div class="skeleton" style="width:32%;height:10px;border-radius:6px;"></div>
                        </div>
                    </div>
                    <div class="skeleton rounded-s mt-2" style="height:45px;"></div>
                </div>
            </div>

            <!-- Card 2: Status Absensi -->
            <div class="card card-style">
                <div class="content py-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="skeleton sk-circle me-3" style="width:40px;height:40px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton sk-text" style="width:52%;height:14px;"></div>
                            <div class="skeleton" style="width:65%;height:10px;border-radius:6px;"></div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><div class="skeleton rounded-s" style="height:80px;"></div></div>
                        <div class="col-6"><div class="skeleton rounded-s" style="height:80px;"></div></div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Riwayat Absensi -->
            <div class="card card-style">
                <div class="content py-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="skeleton" style="width:55%;height:14px;border-radius:7px;"></div>
                        <div class="skeleton rounded-s" style="width:48px;height:24px;"></div>
                    </div>
                    @for ($sk = 0; $sk < 4; $sk++)
                    <div class="d-flex py-2 align-items-center">
                        <div class="skeleton sk-circle me-3" style="width:35px;height:35px;flex-shrink:0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton sk-text" style="width:{{ [40,55,45,50][$sk] }}%;"></div>
                            <div class="skeleton" style="width:{{ [62,70,58,68][$sk] }}%;height:10px;border-radius:5px;"></div>
                        </div>
                        <div class="skeleton ms-2" style="width:52px;height:20px;border-radius:10px;flex-shrink:0;"></div>
                    </div>
                    @if ($sk < 3) <div class="divider my-0"></div> @endif
                    @endfor
                </div>
            </div>

            <!-- Card 4: Aksi Cepat -->
            <div class="card card-style mb-0">
                <div class="content py-3">
                    <div class="skeleton mb-3" style="width:38%;height:14px;border-radius:7px;"></div>
                    <div class="row g-2">
                        @foreach ([1,2,3,4] as $__)
                        <div class="col-3 text-center">
                            <div class="skeleton sk-circle mx-auto mb-2" style="width:50px;height:50px;"></div>
                            <div class="skeleton mx-auto" style="width:44px;height:9px;border-radius:4px;"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div><!-- /page-content -->

        <!-- Skeleton Footer -->
        <div class="footer-bar footer-bar-detached">
            <div class="d-flex justify-content-around align-items-center w-100 py-1">
                @foreach ([1,2,3,4,5] as $__)
                <div class="text-center" style="width:20%;">
                    <div class="skeleton sk-circle mx-auto mb-1" style="width:22px;height:22px;"></div>
                    <div class="skeleton mx-auto" style="width:28px;height:8px;border-radius:4px;"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div><!-- /#page-skeleton -->

    <!-- Sidebar Edit Attendance (Offcanvas) -->
    @include('admin.attendance.edit-modal')

    <div id="page">
        @yield('header')
        @yield('footer')
        @yield('sidebar')
        <!-- Your Page Content Goes Here-->
        <div class="page-content @yield('page-class', 'header-clear-medium')">
            @yield('content')
        </div>
        <!-- End of Page Content-->
    </div>
    <!--End of Page ID-->

    <!-- Modals Section -->
    @stack('modals')

    <script src="{{ asset('template/scripts/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/scripts/custom.js') }}"></script>

    <!-- Keep Session Alive Script -->
    <script>
        // Keep session alive like mobile app - ping server every 10 minutes
        @auth
        setInterval(function() {
            fetch('{{ route('dashboard') }}', {
                method: 'HEAD',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(function(error) {
                // Silently handle errors to avoid disrupting user experience
                console.log('Session keep-alive ping failed:', error);
            });
        }, 600000); // 10 minutes
        @endauth
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);

                        // Check for updates
                        registration.addEventListener('updatefound', function() {
                            console.log('New service worker found, installing...');
                            const installingWorker = registration.installing;
                            installingWorker.addEventListener('statechange', function() {
                                if (installingWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        // New update available
                                        if (confirm('Aplikasi telah diperbarui. Muat ulang untuk mendapatkan versi terbaru?')) {
                                            window.location.reload();
                                        }
                                    }
                                }
                            });
                        });
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;

            // Show install button/banner
            showInstallPrompt();
        });

        function showInstallPrompt() {
            // Create install banner
            const installBanner = document.createElement('div');
            installBanner.id = 'pwa-install-banner';
            installBanner.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 20px;
                right: 20px;
                background: #007bff;
                color: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            `;

            installBanner.innerHTML = `
                <div>
                    <strong>📱 Install App</strong><br>
                    <small>Tambahkan ke layar utama untuk akses cepat</small>
                </div>
                <div>
                    <button id="pwa-install-btn" style="background: white; color: #007bff; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 8px; font-weight: bold;">Install</button>
                    <button id="pwa-dismiss-btn" style="background: transparent; color: white; border: 1px solid white; padding: 8px 16px; border-radius: 4px;">Nanti</button>
                </div>
            `;

            document.body.appendChild(installBanner);

            // Install button click
            document.getElementById('pwa-install-btn').addEventListener('click', () => {
                installBanner.style.display = 'none';
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the A2HS prompt');
                    } else {
                        console.log('User dismissed the A2HS prompt');
                    }
                    deferredPrompt = null;
                });
            });

            // Dismiss button click
            document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                installBanner.style.display = 'none';
                // Don't show again for this session
                sessionStorage.setItem('pwa-prompt-dismissed', 'true');
            });

            // Auto-hide after 10 seconds
            setTimeout(() => {
                if (installBanner && installBanner.style.display !== 'none') {
                    installBanner.style.display = 'none';
                }
            }, 10000);
        }

        // Track PWA usage
        window.addEventListener('appinstalled', (evt) => {
            console.log('App was installed');
            // Track this event in analytics if needed
        });

        // Detect if running as PWA
        function isPWA() {
            return (window.matchMedia('(display-mode: standalone)').matches) ||
                (window.navigator.standalone) ||
                document.referrer.includes('android-app://');
        }

        if (isPWA()) {
            console.log('Running as PWA');
            // Add PWA-specific styling or behavior
            document.body.classList.add('pwa-mode');
        }
    </script>

    @stack('scripts')

    <script>
    /* ========================
       SKELETON PAGE TRANSITIONS
       ======================== */
    (function () {
        'use strict';
        var sk = document.getElementById('page-skeleton');
        if (!sk) return;

        /* Apply body background to skeleton so it covers content cleanly */
        function syncBg() {
            var bg = window.getComputedStyle(document.body).backgroundColor;
            if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                sk.style.backgroundColor = bg;
            }
        }

        /* On DOMContentLoaded: hide skeleton, fade in real content */
        document.addEventListener('DOMContentLoaded', function () {
            syncBg();
            sk.classList.remove('sk-visible');
            document.body.classList.add('sk-page-loaded');
        });

        /* Intercept navigation clicks to show skeleton */
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

        /* Show skeleton on form submit (skip logout and marked forms) */
        document.addEventListener('submit', function (e) {
            if (e.target.dataset.noSkeleton !== undefined) return;
            syncBg();
            sk.classList.add('sk-visible');
        });

        /* Hide skeleton if user navigates back/forward (bfcache) */
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) sk.classList.remove('sk-visible');
        });
    })();
    </script>
</body>

</html>
