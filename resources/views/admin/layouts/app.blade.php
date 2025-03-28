<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Halaman Admin')</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('icon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('icon/favicon.ico') }}">
        
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/admin_assets/main.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/customAdmin.css') }}" rel="stylesheet">
    <link href="{{ asset('/vendor/bootstrap/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @stack('up_styles')

    <!-- Scripts -->
    <script src="{{ asset('/vendor/jquery-3.4.min.js') }}"></script>
    <script src="{{ asset('/vendor/bootstrap/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    @stack('up_scripts')
    <style>
      .required::after {
        content: "*";
        color: red;
      }
    </style>
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <!-- header -->
        @include('admin.includes.header')
        <!-- header end -->
        <div class="app-main">
        @include('admin.includes.sidemenu')
          <div class="app-main__outer">
            <div class="app-main__inner">
                @yield('content')
            </div>
            <!-- Footer -->
            @include('admin.includes.footer')
            <!-- Footer End-->
          </div>            
        </div>
    </div>
    @stack('modal')
    {{-- <script src="{{ asset('vendor/bootstrap/js/popper.min.js') }}" defer></script> --}}
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/admin_assets/assets/scripts/main.js') }}"></script> 
    <script src="{{ asset('js/session.js') }}" defer></script>
    @stack('scripts')
    @yield('scriptOptional')
</body>
</html>