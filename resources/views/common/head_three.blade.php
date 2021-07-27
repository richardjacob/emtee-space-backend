<!DOCTYPE html>
<html  dir="{{ (((Session::get('language')) ? Session::get('language') : $default_language[0]->value) == 'ar') ? 'rtl' : '' }}" lang="{{ (Session::get('language')) ? Session::get('language') : $default_language[0]->value }}"  xmlns:fb="http://ogp.me/ns/fb#"><!--<![endif]--><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" >
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0' >
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta name = "viewport" content = "user-scalable=no, width=device-width">
<meta name="daterangepicker_format" content = "{{ $daterangepicker_format  }}">
<meta name="datepicker_format" content = "{{$datepicker_format }}"> 
<meta name="datedisplay_format" content = "{{ strtolower(DISPLAY_DATE_FORMAT) }}"> 
<meta name="php_date_format" content = "{{ PHP_DATE_FORMAT }}"> 
<meta name="google-site-verification" content="-FoK1tzEFLudrU3ICODmjjpnM3eIZAf23THHTlMZmIM" />
<link rel="shortcut icon" href="{{ $favicon }}">
 
<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700&display=swap" rel="stylesheet">

<!-- Range slider -->
 <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>

<script src="{{ URL::to('vendor/security.js') }}"></script>
<link href="https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/slick/slick.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/animate.css') }}">

<link rel="stylesheet" href="{{ asset('vendor/style.css') }}">
<title>Emtee | Airbnb of Storage | Affordable &amp; Secure Storage - {{$site_name }}</title>
<body>
