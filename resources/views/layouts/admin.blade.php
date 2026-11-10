<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sarraf-QMS') | National Bank Queue Management System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=3" />
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Noto+Nastaliq+Urdu:wght@400;700&display=swap"
      rel="stylesheet" />

    <!-- Boxicons & FontAwesome CDN for guaranteed icon fidelity -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Custom Banking Styling Enhancements -->
    <style>
      .urdu-font {
        font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Urdu Typesetting', serif;
        direction: rtl;
        line-height: 2.2;
      }
      .card {
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        border: none;
        border-radius: 0.5rem;
      }
      .btn-primary {
        background-color: #696cff;
        border-color: #696cff;
      }
      .bg-label-primary {
        background-color: #e7e7ff !important;
        color: #696cff !important;
      }
      .bg-label-success {
        background-color: #e8fadf !important;
        color: #71dd37 !important;
      }
      .bg-label-danger {
        background-color: #ffe5e5 !important;
        color: #ff3e1d !important;
      }
      .bg-label-warning {
        background-color: #fff2d6 !important;
        color: #ffab00 !important;
      }
      .bg-label-info {
        background-color: #d7f5fc !important;
        color: #03c3ec !important;
      }
      .bg-label-secondary {
        background-color: #ebeef0 !important;
        color: #8592a3 !important;
      }
      .pulse-dot {
        animation: pulseAnimation 2s infinite;
      }
      @keyframes pulseAnimation {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
      }
    </style>

    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Sidebar Menu -->
        @include('layouts.partials.sidebar')

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('layouts.partials.navbar')

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Flash Messages -->
              @if(session('success'))
                <div class="alert alert-success alert-dismissible shadow-sm fade show" role="alert">
                  <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              @if(session('error'))
                <div class="alert alert-danger alert-dismissible shadow-sm fade show" role="alert">
                  <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              @if(session('info'))
                <div class="alert alert-info alert-dismissible shadow-sm fade show" role="alert">
                  <i class="bx bx-info-circle me-1"></i> {{ session('info') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              @if(session('warning'))
                <div class="alert alert-warning alert-dismissible shadow-sm fade show" role="alert">
                  <i class="bx bx-alarm-exclamation me-1"></i> {{ session('warning') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('layouts.partials.footer')

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- SweetAlert2 for rich alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
  </body>
</html>
