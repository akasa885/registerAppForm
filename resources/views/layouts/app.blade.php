<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $information_site['description'] }}">
    <meta name="keywords" content="{{ $information_site['keywords'] }}">
    <meta name="og:title" content="@yield('og_title', "")">
    <meta name="og:description" content="{{ $information_site['description'] }}">
    <meta name="og:url" content="{{ url()->current() }}">
    <meta name="og:locale" content="id_ID">
    <meta name="og:site_name" content="{{ $information_site['sitename'] }}">
    <meta name="og:image" content="@yield('og_image', asset('icon/favicon.png'))">
    <meta name="og:image:alt" content="{{ $information_site['sitename'] }}">
    <meta name="og:image:width" content="512">
    <meta name="og:image:height" content="512">
    <meta name="og:type" content="website">
    <meta name="author" content="Rizki Akbar">
    <meta name="author_email" content="rixak98@gmail.com">
    <meta name="application-name" content="Eform for Registration Management">
    <meta name="googlebot" content="index, follow">
    <meta name="robots" content="index, unfollow">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('icon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('icon/favicon.ico') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('/vendor/jquery-3.4.min.js') }}"></script>

    <style>
        .required::after {
            content: "*";
            color: red;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .alert {
            display: none;
            position: relative;
            padding: 1rem 1rem 1rem 3rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
            /*shadow*/
            --shadow: 0 0 #0000;
            box-shadow: var(--shadow);
        }

        .alert.alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert.alert-info {
            background-color: #d2f4fc;
            border-color: #c3e9f6;
            color: #31708f;
        }

        .alert.alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .alert.alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert.alert-success .alert-icon {
            --text-opacity: 1;
            color: #0f5132;
            color: rgba(15, 81, 50, var(--text-opacity));
        }

        .alert.alert-info .alert-icon {
            --text-opacity: 1;
            color: #1c64f2;
            color: rgba(28, 100, 242, var(--text-opacity));
        }

        .alert.show {
            display: inline-flex;
            align-items: center;
            animation: fadeIn 1s 2.5s;
        }

        .alert-dismissible .btn-close {
            position: relative;
            top: -0.125rem;
            right: -1.5rem;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }

        .alert-dismissible .btn-close:hover {
            color: inherit;
        }

        .alert-dismissible .btn-close:focus {
            outline: 0;
            box-shadow: 0 0 0 1px #fff, 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .alert-dismissible .btn-close::before {
            content: '';
            display: inline-block;
            width: 1rem;
            height: 1rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3e%3cpath d='M18.707 5.293a1 1 0 00-1.414 0L12 10.586 7.707 6.293a1 1 0 00-1.414 1.414L10.586 12l-4.293 4.293a1 1 0 101.414 1.414L12 13.414l4.293 4.293a1 1 0 001.414-1.414L13.414 12l4.293-4.293a1 1 0 000-1.414z' fill='%23353D48'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-size: 1rem;
            background-position: center center;
        }
    </style>

    @stack('stylesUp')
</head>

<body class="bg-gray-100 h-screen antialiased leading-none font-sans">
    <div id="app">
        <header class="bg-blue-900 py-5">
            <div class="container mx-auto flex flex-wrap justify-between gap-2 items-center px-6">
                <!--begin::logo-->
                <div class="block">
                    <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-100 no-underline">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
                <!--end::logo-->
                <nav class="md:space-x-4 flex items-center text-left flex-wrap gap-2 text-gray-300 text-sm sm:text-base">
                    <!--begin::lang-->
                    @include('partials.language_switcher_front')
                    <!--end::lang-->
                    @guest
                        <!-- Authentication Links -->
                    @else
                    <div class="space-x-1">
                        <a href="{{ route('admin.dashboard') }}">
                            <span>{{ Auth::user()->name }}</span>
                        </a>

                        <a href="{{ route('admin.logout') }}" class="no-underline hover:underline" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </div>
                    @endguest
                </nav>
            </div>
        </header>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
            {{ csrf_field() }}
        </form>
        @yield('content')

        @include('includes.front_footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        $(document).ready(function() {
            // catch data-bs-dismiss="alert" event
            $(".alert .btn-close").click(function() {
                $(this).parent().remove();
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
