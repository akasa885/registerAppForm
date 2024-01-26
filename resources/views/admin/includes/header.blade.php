<div class="app-header header-shadow bg-secondary header-text-light">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">

        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0 d-flex flex-row justify-content-between gap-3">
                    <div class="widget-content-wrapper">
                        @include('partials.language_switcher_admin')
                    </div>
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a href="{{ route('admin.profile.edit') }}" class="p-0 btn">
                                    <img width="42" class="rounded-circle"
                                        src="{{ asset('/admin_assets/assets/images/avatars/1.jpg') }}" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="widget-content-left  ml-3 header-user-info">
                            <div class="widget-heading">
                                {{ Auth::user() ? Auth::user()->name : 'guest' }}
                            </div>
                            <div class="widget-subheading">
                                @can('isAdmin')
                                    Admin
                                @elsecan('isAuthor')
                                    Author
                                @elsecan('isSuperAdmin')
                                    Super Admin
                                @else
                                    Editor
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
