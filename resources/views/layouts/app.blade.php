<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $information_site['description'] }}">
    <meta name="keywords" content="{{ $information_site['keywords'] }}">
    <meta name="og:title" content="@yield('og_title', '')">
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
                <div class="flex items-center justify-between w-full">
                    <!--begin::logo-->
                    <div class="block">
                        <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-100 no-underline">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    <!--end::logo-->

                    <!--begin::desktop navbar-->
                    <div class="hidden md:flex items-center space-x-6">
                        <!--begin::navigation menu-->
                        <nav class="flex space-x-6 items-center">
                            <a href="{{ url('/') }}"
                                class="text-gray-300 hover:text-white transition duration-200">
                                {{ __('Home') }}
                            </a>
                        </nav>
                        <!--end::navigation menu-->

                        <!--begin::user menu-->
                        <div class="flex items-center space-x-4">
                            <!--begin::language switcher-->
                            <div class="relative">
                                @include('partials.language_switcher_front')
                            </div>
                            <!--end::language switcher-->

                            @guest
                                <div class="flex space-x-3">
                                    <a href="{{ route('admin.login') }}"
                                        class="text-gray-300 hover:text-white transition duration-200">
                                        {{ __('Login') }}
                                    </a>
                                </div>
                            @else
                                <div class="relative dropdown">
                                    <button
                                        class="flex items-center space-x-2 text-gray-300 hover:text-white transition duration-200">
                                        <span>{{ Auth::user()->name }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div
                                        class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border hidden">
                                        <div class="py-1">
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition duration-200">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z">
                                                    </path>
                                                </svg>
                                                {{ __('Dashboard') }}
                                            </a>
                                            <hr class="border-gray-200">
                                            <a href="{{ route('admin.logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                                class="block px-4 py-2 text-red-600 hover:bg-red-50 transition duration-200">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                {{ __('Logout') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endguest
                        </div>
                        <!--end::user menu-->
                    </div>
                    <!--end::desktop navbar-->

                    <!--begin::mobile menu button-->
                    <div class="md:hidden">
                        <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    <!--end::mobile menu button-->
                </div>

                <!--begin::mobile menu-->
                <div id="mobile-menu"
                    class="md:hidden fixed left-0 right-0 bg-blue-900 shadow-lg opacity-0 invisible z-50 transition-all duration-300 ease-in-out transform -translate-y-4"
                    style="top: 10%">
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ url('/') }}"
                            class="block text-gray-300 hover:text-white transition duration-200">
                            {{ __('Home') }}
                        </a>

                        <hr class="border-blue-700 my-3">

                        <!--begin::language switcher mobile-->
                        <div class="py-2">
                            @include('partials.language_switcher_front')
                        </div>
                        <!--end::language switcher mobile-->

                        @guest
                            <div class="space-y-2 pt-2">
                                <a href="{{ route('admin.login') }}"
                                    class="block text-gray-300 hover:text-white transition duration-200">
                                    {{ __('Login') }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-2 pt-2">
                                <div class="text-gray-100 font-medium">{{ Auth::user()->name }}</div>
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block text-gray-300 hover:text-white transition duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                    {{ __('Dashboard') }}
                                </a>
                                <a href="{{ route('admin.logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block text-red-300 hover:text-red-200 transition duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    {{ __('Logout') }}
                                </a>
                            </div>
                        @endguest
                    </div>
                </div>
                <!--end::mobile menu-->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const mobileMenuButton = document.getElementById('mobile-menu-button');
                        const mobileMenu = document.getElementById('mobile-menu');

                        if (mobileMenuButton && mobileMenu) {
                            mobileMenuButton.addEventListener('click', function() {
                                if (mobileMenu.classList.contains('invisible')) {
                                    // Show menu
                                    mobileMenu.classList.remove('invisible', 'opacity-0', '-translate-y-4');
                                    mobileMenu.classList.add('opacity-100', 'translate-y-0');
                                } else {
                                    // Hide menu
                                    mobileMenu.classList.remove('opacity-100', 'translate-y-0');
                                    mobileMenu.classList.add('opacity-0', '-translate-y-4');
                                    setTimeout(() => {
                                        mobileMenu.classList.add('invisible');
                                    }, 300);
                                }
                            });

                            // Close menu when clicking outside
                            document.addEventListener('click', function(event) {
                                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                                    if (!mobileMenu.classList.contains('invisible')) {
                                        mobileMenu.classList.remove('opacity-100', 'translate-y-0');
                                        mobileMenu.classList.add('opacity-0', '-translate-y-4');
                                        setTimeout(() => {
                                            mobileMenu.classList.add('invisible');
                                        }, 300);
                                    }
                                }
                            });
                        }
                    });
                </script>
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
