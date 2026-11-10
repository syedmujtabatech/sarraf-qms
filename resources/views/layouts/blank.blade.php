<!DOCTYPE html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sarraf-QMS') | National Bank Queue Management</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=3" />
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet" />

    <!-- Boxicons & FontAwesome -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <style>
      .urdu-font {
        font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Urdu Typesetting', serif;
        direction: rtl;
        line-height: 2.2;
      }
      .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
      .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
      .bg-label-danger { background-color: #ffe5e5 !important; color: #ff3e1d !important; }
      .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
      .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
      .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    </style>

    @stack('styles')
  </head>

  <body class="@yield('body-class', 'bg-body')">
    @yield('content')

    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
  </body>
</html>
