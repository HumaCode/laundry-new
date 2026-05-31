<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LaundryPro') }} - Dashboard</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Bootstrap 5.3.2 & FontAwesome 6.4.0 CDN -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <!-- Chart.js CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

        <!-- Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/css/admin/global.css',
            'resources/js/admin/global.js'
        ])

        <!-- Page Specific Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Topbar -->
        @include('partials.topbar')

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @include('partials.footer')

        <!-- Logout Confirmation Modal -->
        <div class="modal-overlay-custom" id="logoutModal">
            <div class="modal-container-custom">
                <!-- Pulse Wave Icon -->
                <div class="pulse-wave-wrapper">
                    <div class="pulse-wave-ring"></div>
                    <div class="pulse-wave-ring"></div>
                    <div class="pulse-wave-ring"></div>
                    <div class="pulse-wave-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                </div>

                <h3 class="modal-title-custom">Konfirmasi Keluar</h3>
                <p class="modal-desc-custom">Apakah Anda yakin ingin keluar dari akun Anda? Seluruh sesi aktif Anda saat ini akan diakhiri.</p>

                <div class="modal-btn-group">
                    <button type="button" class="modal-btn-custom modal-btn-cancel" onclick="closeLogoutModal()">
                        Batal
                    </button>
                    <button type="button" class="modal-btn-custom modal-btn-confirm" id="btnConfirmLogout" onclick="executeLogout()">
                        <div class="spinner"></div>
                        <span class="btn-text">Ya, Logout</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Global Reusable Components -->
        <x-loading-dialog />
        <x-toast />

        <!-- jQuery & Bootstrap Bundle CDN -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

        <!-- Page Specific Scripts -->
        @stack('scripts')
    </body>
</html>
