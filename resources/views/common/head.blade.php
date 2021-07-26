<!DOCTYPE html>
<html dir="{{ (((session('language')) ? session('language') : $default_language[0]->value) == 'ar') ? 'rtl' : '' }}" lang="{{ (session('language')) ? session('language') : $default_language[0]->value }}"  xmlns:fb="http://ogp.me/ns/fb#" data-wf-page="5da4d16b48e1d45e7d62a83f" data-wf-site="5da4d16b48e1d419c062a83e">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0' >
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name = "viewport" content = "user-scalable=no, width=device-width">
    <meta name="daterangepicker_format" content = "{{ $daterangepicker_format  }}">
    <meta name="datepicker_format" content = "{{$datepicker_format }}">
    <meta name="datedisplay_format" content = "{{ strtolower(DISPLAY_DATE_FORMAT) }}">
    <meta name="php_date_format" content = "{{ PHP_DATE_FORMAT }}">
<link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&text=0123456789" rel="stylesheet">
    <link rel="dns-prefetch" href="https://maps.googleapis.com/">
    <link rel="dns-prefetch" href="https://maps.gstatic.com/">
    <link rel="dns-prefetch" href="https://mts0.googleapis.com/">
    <link rel="dns-prefetch" href="https://mts1.googleapis.com/">
    <link rel="shortcut icon" href="{{ $favicon }}">
    <!--[if IE]><![endif]-->
    <meta charset="utf-8">
    <!--[if IE 8]>
    {!! Html::style('css/common_ie8.css?v='.$version) !!}
    <![endif]-->
    <!--[if !(IE 8)]><!-->
    {!! Html::style('css/common.css?v='.$version) !!}
    {!! Html::style('plugins/bootstrap-select/css/bootstrap-select.min.css') !!}
    @if (!isset($exception))
    @if (Route::current()->uri() == 'help' || Route::current()->uri() == 'help/topic/{id}/{category}' || Route::current()->uri() == 'help/article/{id}/{question}')
    {!! Html::style('css/jquery-ui.css?v='.$version) !!}
    @endif
    @if(Route::currentRouteName() == 'search_page')
    {!! Html::style('css/nouislider.min.css') !!}
    @endif
    @if(Route::currentRouteName() == 'manage_space')
    {!! Html::style('plugins/fullcalendar/core/main.css') !!}
    {!! Html::style('plugins/fullcalendar/daygrid/main.css') !!}
    {!! Html::style('plugins/fullcalendar/timegrid/main.css') !!}
    @endif
    @endif
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="{{ Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'keywords') }}">
    <meta name="twitter:widgets:csp" content="on">
    @if (!isset($exception))
    @if (Route::currentRouteName() == 'space_details')
    <meta property="og:image" content="{{ $result->photo_name }}">
    <meta itemprop="image" src="{{ $result->photo_name }}">
    <link rel="image_src" href="#" src="{{ $result->photo_name }}">
    @endif
    @if (Route::currentRouteName() == 'wishlists')
    <meta property="og:image" content="{{@$result[0]->saved_wishlists[0]->photo_name}}">
    <meta itemprop="image" src="{{@$result[0]->saved_wishlists[0]->photo_name}}">
    <link rel="image_src" href="#" src="{{ @$result[0]->saved_wishlists[0]->photo_name }}">
    @endif
    @endif
    <link rel="search" type="application/opensearchdescription+xml" href="#" title="">
    <title>
    {{ $title ?? Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'title') }} {{ $additional_title ?? '' }}
    </title>
    <meta name="description" content="{{ Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'description') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#f5f5f5">
  </head>
  @section('responsive_footer_menu')
  <div class="footheader">
    <nav class="footheaderdiv">
      <ul class="frthide">
        <li class="{{ (Route::currentRouteName() == 'home_page') ? 'active' : '' }}">
          <a href="{{ route('home_page') }}">
            <h1>
            <img src="{{ asset('images/icons/home.png') }}" alt="home">
            <img class="cls_fhiconh" src="{{ asset('images/icons/homeh.png') }}" alt="home">
            </h1>
            <span> @lang('messages.header.home') </span>
          </a>
        </li>
        @auth
        <li class="{{ (Route::currentRouteName() == 'dashboard') ? 'active' : '' }}">
          <a href="{{ url('dashboard') }}">
            <h1>
            <img src="{{ asset('images/icons/Dashboard.png') }}" alt="Dashboard">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Dashboardh.png') }}" alt="Dashboard">
            </h1>
            <span> @lang('messages.header.dashboard') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'manage_space.new') ? 'active' : '' }}">
          <a href="{{ route('manage_space.new') }}">
            <h1>
            <img src="{{ asset('images/icons/listyourspace.png') }}" alt="Wishlist">
            <img class="cls_fhiconh" src="{{ asset('images/icons/listyourspace.png') }}" alt="Wishlist">
            </h1>
            <span> @choice('messages.new_space.list_your_space',2) </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'profile_settings') ? 'active' : '' }}">
          <a class="profile_settings1">
            <h1>
            <img src="{{ asset('images/icons/profile.png') }}" alt="Profile">
            <img class="cls_fhiconh" src="{{ asset('images/icons/profileh.png') }}" alt="Profile">
            </h1>
            <span> @lang('messages.header.profile') </span>
          </a>
        </li>
        <li class="">
          <a class="footer-toggle_menu">
            <h1>
            <img src="{{ asset('images/icons/Footer.png') }}" alt="Footer">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Footerh.png') }}" alt="Footer">
            </h1>
            <span> @lang('messages.new_home.footer') </span>
          </a>
        </li>
        {{--
        <li class="">
          <a class="more_menu">
            <h1>
            <img src="{{ asset('images/icons/More.png') }}" alt="More">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Moreh.png') }}" alt="More">
            </h1>
            <span> @lang('messages.profile.more') </span>
          </a>
        </li>
        --}}
        @else
        <li class="{{ (Route::currentRouteName() == 'user_login') ? 'active' : '' }}">
          <a href="{{ route('user_login') }}">
            <h1>
            <img src="{{ asset('images/icons/login.png') }}" alt="Login">
            <img class="cls_fhiconh" src="{{ asset('images/icons/loginh.png') }}" alt="Login">
            </h1>
            <span> @lang('messages.header.login') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'signup_login') ? 'active' : '' }}">
          <a href="{{ route('signup_login') }}">
            <h1>
            <img src="{{ asset('images/icons/logout.png') }}" alt="Logout">
            <img class="cls_fhiconh" src="{{ asset('images/icons/logouth.png') }}" alt="Logout">
            </h1>
            <span> @lang('messages.header.signup') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'help_home') ? 'active' : '' }}">
          <a href="{{ route('help_home') }}">
            <h1>
            <img src="{{ asset('images/icons/help.png') }}" alt="Help">
            <img class="cls_fhiconh" src="{{ asset('images/icons/helph.png') }}" alt="Help">
            </h1>
            <span> @lang('messages.header.help') </span>
          </a>
        </li>
        <li class="">
          <a class="footer-toggle_menu">
            <h1>
            <img src="{{ asset('images/icons/Footer.png') }}" alt="Footer">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Footerh.png') }}" alt="Footer">
            </h1>
            <span> @lang('messages.new_home.footer') </span>
          </a>
        </li>
        @endauth
      </ul>
      <ul class="ftrshow" style="height: 0px;">
        @auth
        <li class="{{ (Route::currentRouteName() == 'space') ? 'active' : '' }}">
          <a href="{{ route('space') }}">
            <h1>
            <img src="{{ asset('images/icons/List.png') }}" alt="Listings">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Listh.png') }}" alt="Listings">
            </h1>
            <span> @lang('messages.new_home.listings') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'inbox') ? 'active' : '' }}">
          <a href="{{ route('inbox') }}">
            <h1>
            <img src="{{ asset('images/icons/inbox.png') }}" alt="Inbox">
            <img class="cls_fhiconh" src="{{ asset('images/icons/inboxh.png') }}" alt="Inbox">
            </h1>
            <span> @lang('messages.header.inbox') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'profile_settings') ? 'active' : '' }}">
          <a href="">
            <h1>
            <img src="{{ asset('images/icons/profile.png') }}" alt="Profile">
            <img class="cls_fhiconh" src="{{ asset('images/icons/profileh.png') }}" alt="Profile">
            </h1>
            <span> @lang('messages.header.profile') </span>
          </a>
        </li>
        <li class="{{ (Route::currentRouteName() == 'help_home') ? 'active' : '' }}">
          <a href="{{ route('help_home') }}">
            <h1>
            <img src="{{ asset('images/icons/help.png') }}" alt="Help">
            <img class="cls_fhiconh" src="{{ asset('images/icons/helph.png') }}" alt="Help">
            </h1>
            <span> @lang('messages.header.help') </span>
          </a>
        </li>
        <li>
          <a class="hide_menu">
            <h1>
            <img src="{{ asset('images/icons/Back.png') }}" alt="Back">
            <img class="cls_fhiconh" src="{{ asset('images/icons/Backh.png') }}" alt="Back">
            </h1>
            <span> @lang('messages.new_space.back') </span>
          </a>
        </li>
        @endauth
      </ul>
    </nav>
  </div>
  @auth
  <div class="cls_mobileprofile pl-3 pr-3  pt-5" style="display: none;">
    <div class="cls_mobileproimage d-flex justify-content-between align-items-center mb-4">
      <div class="cls_mobileedit">
        <h2> @lang('messages.host_dashboard.hi_first_name',['first_name' => auth()->user()->first_name]) </h2>
        <a class="theme-link" href="{{ route('edit_profile') }}">
          {{ trans('messages.header.edit_profile') }}
        </a>
      </div>
      <div class="cls_mobileeimg">
        <a href="{{ route('edit_profile_media') }}">
          <h1>
          <img src="{{ auth()->user()->profile_picture->src }}" alt="image">
          </h1>
        </a>
      </div>
    </div>
    <ul class="cls_mobilenavone">
      <li class="">
        <a href="{{ route('show_profile',['id' => auth()->id()]) }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/Profileinfo.png') }}" alt="image">
          </h1>
          <span> @lang('messages.dashboard.view_profile') </span>
        </a>
      </li>
      <li class="">
        <a href="{{ route('help_home') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/help.png') }}" alt="image">
          </h1>
          <span>@lang('messages.header.help')</span>
        </a>
      </li>
    </ul>
    <hr>
    <ul class="cls_mobilenavone">
      <p class="mb-4 small"> @lang('messages.header.dashboard') </p>
      <li>
        <a href="{{ route('space') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/List.png') }}" alt="image">
          </h1>
          <span> @lang('messages.new_home.listings') </span>
        </a>
      </li>
      <li class="">
        <a href="{{ route('current_bookings') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/Booking.png') }}" alt="image">
          </h1>
          <span> @lang('messages.new_space.your_bookings') </span>
        </a>
      </li>
      <li class="cls_mobilecount" ng-init="inbox_count='{{ @Auth::user()->inbox_count()}}'">
        <a href="{{ route('inbox') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/inbox.png') }}" alt="image">
          </h1>
          <span> @lang('messages.header.inbox') </span>
          <i class="alert-count text-center" ng-class="inbox_count != '0' ? '' : 'fade'" ng-cloak> @{{ inbox_count }} </i>
        </a>
      </li>
      <li class="">
        <a href="{{ route('account') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/Account.png') }}" alt="image">
          </h1>
          <span> @lang('messages.header.account') </span>
        </a>
      </li>
      @if(auth()->user()->saved_wishlists)
      <li class="">
        <a href="{{ route('my_wishlists') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/wishlist.png') }}" alt="image">
          </h1>
          <span> @choice('messages.wishlist.wishlist',2) </span>
        </a>
      </li>
      @endif
      <li class="">
        <a href="{{ route('invite') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/Travel-Credit.png') }}" alt="image">
          </h1>
          <span> @lang('messages.header.invite_friends') </span>
        </a>
      </li>
      <li class="">
        <a href="{{ route('disputes') }}" class="d-inline-flex justify-content-start align-items-center">
          <h1>
          <img src="{{ asset('images/icons/Dispute.png') }}" alt="image">
          </h1>
          <span> @lang('messages.disputes.disputes') </span>
        </a>
      </li>
      
    </ul>
    <hr>
    <div class="cls_mobilenavthree">
      <a href="{{ route('logout') }}" class="cls_mobilelogout">
        @lang('messages.header.logout')
      </a>
    </div>
  </div>
  @endauth
  @endsection
  @php
  $body_class = '';
  if(!isset($exception)) {
  $body_class .= (request()->route()->getName() == 'home_page') ? 'home-page ' : 'inner_page ';
  $body_class .= (request()->route()->getName() == 'space_details') ? 'space-detail-page ' : ' ';
  $body_class .= (request()->route()->getName() == 'dashboard') ? 'dashboard_page ' : ' ';
  $body_class .= (request()->route()->getName() == 'search_page') ? 'search-page ' : ' ';
  $body_class .= (request()->route()->getName() == 'manage_space') ? 'space-home ' : ' ';
  $body_class .= (request()->route()->getName() == 'profile_settings') ? 'profile_nav-home ' : ' ';
  $body_class .= (request()->segment(1) == 'help') ? 'help-page ' : ' ';
  }
  @endphp
  <body class="secondary-home {{ $body_class }}" ng-app="App" ng-cloak>