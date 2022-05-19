<div class="app-sidebar sidebar-shadow bg-vicious-stance sidebar-text-light">
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
              </div>    <div class="scrollbar-sidebar ps ps--active-y">
                <!-- Sidebar Menu START-->
                  <div class="app-sidebar__inner">
                      <ul class="vertical-nav-menu metismenu">
                          @can('isAdmin')
                          <li class="app-sidebar__heading">Dashboards</li>
                          @if(Route::currentRouteName() == 'admin.dashboard')
                            <li class="mm-active">
                                <a id="dashboardLink" href="{{route('admin.dashboard')}}">
                                    <i class="metismenu-icon pe-7s-rocket"></i>
                                    Dashboard
                                </a>
                            </li>
                          @else
                            <li>
                                <a id="dashboardLink" href="{{route('admin.dashboard')}}">
                                    <i class="metismenu-icon pe-7s-rocket"></i>
                                    Dashboard
                                </a>
                            </li>
                          @endif
                          @elsecan('isSuperAdmin')
                          @if(Route::currentRouteName() == 'admin.dashboard')
                            <li class="mm-active">
                                <a id="dashboardLink" href="{{route('admin.dashboard')}}">
                                    <i class="metismenu-icon pe-7s-rocket"></i>
                                    Dashboard
                                </a>
                            </li>
                          @else
                            <li>
                                <a id="dashboardLink" href="{{route('admin.dashboard')}}">
                                    <i class="metismenu-icon pe-7s-rocket"></i>
                                    Dashboard
                                </a>
                            </li>
                          @endif
                          @endcan                  
                          <li class="app-sidebar__heading">Content</li>                          
                          @if(Request::route()->getPrefix() == 'dpanel/link')                          
                          <li class="mm-active">
                            <a id="contentLink" href="{{route('admin.link.view')}}">
                                <i class="metismenu-icon pe-7s-diamond"></i>
                                Link
                            </a>
                          </li>
                          @else
                          <li>
                            <a id="contentLink" href="{{route('admin.link.view')}}">
                                <i class="metismenu-icon pe-7s-diamond"></i>
                                Link
                            </a>
                          </li>
                          @endif
                          <li class="app-sidebar__heading">Settings</li>
                          @can('isAdmin')                            
                          <li>
                              <a id="settingLink" href="#">
                                  <i class="metismenu-icon pe-7s-display2"></i>
                                  Setting
                              </a>
                          </li>
                          @elsecan('isSuperAdmin')
                          <li>
                            <a id="settingLink" href="#">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Setting
                            </a>
                           </li>
                           @if(Route::currentRouteName() == 'admin.users.view')
                           <li class="mm-active">
                            <a id="settingLink" href="{{route('admin.users.view')}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Users
                            </a>
                           </li>
                           @else
                           <li>
                            <a id="settingLink" href="{{route('admin.users.view')}}">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Users
                            </a>
                           </li>
                           @endif
                          @endcan
                          <li>
                              <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                  <i class="metismenu-icon pe-7s-power"></i>
                                  {{ __('Logout') }}
                              </a>
                              <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                  @csrf
                              </form>
                          </li>
                      </ul>
                  </div>
                <!-- Sidebar Menu END-->
              <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 607px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 545px;"></div></div></div>
          </div>