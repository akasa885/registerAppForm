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

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="shortcut icon" href="{{ asset('icon/favicon.png') }}" />

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
    </style>
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
    </script>
    @stack('scripts')
</body>

</html>
