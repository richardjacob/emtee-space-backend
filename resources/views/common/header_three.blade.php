<div id="site-wrapper" class="site-wrapper home-main">

    <header id="header" class="main-header header-sticky header-sticky-smart header-style-01 header-float text-uppercase">
        <div class="header-wrapper sticky-area">
            <div class="container container-1720">
                <nav class="navbar navbar-expand-xl">
                    <div class="header-mobile d-flex d-xl-none flex-fill justify-content-between align-items-center">
                        <div class="navbar-toggler toggle-icon" data-toggle="collapse" data-target="#navbar-main-menu">
                            <span></span>
                        </div>
                        <a class="navbar-brand navbar-brand-mobile" href="{{ url('/') }}">
                            <img src="{{ url(SECONDARY_LOGO) }}" alt="TheDir" width="50" height="50">
                        </a>
                        <a class="mobile-button-search" href="#search-popup" data-gtf-mfp="true"
                            data-mfp-options='{"type":"inline","mainClass":"mfp-move-from-top mfp-align-top search-popup-bg","closeOnBgClick":false,"showCloseBtn":false}'><i
                                class="far fa-search"></i></a>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-main-menu">
                        <a class="navbar-brand d-none d-xl-block mr-auto" href="{{ url('/') }}">
                            <img src="{{ url(SECONDARY_LOGO) }}" alt="TheDir">
                        </a>
                        <ul class="navbar-nav">
                           
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('manage_space.new') }}"> @lang('messages.new_space.list_your_space')</a>
                            </li>

                            @if(Auth::check())

                            <li class="nav-item">
                                <a class="nav-link"   href="{{ route('current_bookings') }}">@lang('messages.new_home.bookings')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"    href="{{ url('inbox') }}"> {{ trans_choice('messages.dashboard.message', 2) }}</a>
                            </li>
                            @endif


                            @if(!Auth::check())
                            <li class="nav-item">
                                <a class="nav-link"  href="{{ url('help') }}"> {{ trans('messages.header.help') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"  href="{{ url('contact') }}"> {{ trans('messages.contactus.contactus') }}</a>
                            </li>

                            @endif
                            
                        </ul>

                        @if(!Auth::check())
                        <div class="header-customize justify-content-end align-items-center d-none d-xl-flex">
                            <div class="header-customize-item">
                                <a href="{{ url('signup_login') }}" class="link" data-gtf-mfp="true"
                                    data-mfp-options='{"type":"inline"}'>
                                    <svg class="icon icon-user-circle-o">
                                        <use xlink:href="#icon-user-circle-o"></use>
                                    </svg>
                                    Log in</a>
                            </div>
                            <div class="header-customize-item button">
                                <a href="{{ url('signup_login') }}" class="btn btn-primary btn-icon-right">Sign Up
                                    <i class="far fa-angle-right"></i></a>
                            </div>
                        </div>
                        @endif

                        @if(Auth::check())
                        <div class="header-customize justify-content-end align-items-center d-none d-xl-flex">
                            <div class="header-customize-item">
                                <a href="{{ url('signup_login') }}" class="link" data-gtf-mfp="true"
                                    data-mfp-options='{"type":"inline"}'>
                                    <svg class="icon icon-user-circle-o">
                                        <use xlink:href="#icon-user-circle-o"></use>
                                    </svg>
                                   Profile</a>
                            </div>
                            
                        </div>
                        @endif


                    </div>
                </nav>
            </div>
        </div>
    </header>