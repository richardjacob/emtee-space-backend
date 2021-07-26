@extends('template')
@section('main')


<div class="cls_mobileprofile pl-3 pr-3 mb-5 mt-5">
	<div class="cls_mobileproimage d-flex justify-content-between align-items-center mb-4">
		<div class="cls_mobileedit">
			<h2> @lang('messages.host_dashboard.hi_first_name',['first_name' => @auth()->user()->first_name]) </h2>
			<a class="theme-link" href="{{ route('edit_profile') }}">
              {{ trans('messages.header.edit_profile') }}
            </a>
		</div>
		<div class="cls_mobileeimg">
			<a href="{{ route('edit_profile_media') }}">
				<h1>
					<img src="{{ @auth()->user()->profile_picture->src }}" alt="image">
				</h1>
			</a>
		</div>
	</div>
	<ul class="cls_mobilenavone">
		<li class="">
			<a href="{{ route('show_profile',['id' => auth()->id()]) }}" class="d-flex justify-content-start align-items-center">
				<h1>
					<img src="{{ asset('images/icons/Profileinfo.png') }}" alt="image">
				</h1>
				<span> @lang('messages.dashboard.view_profile')	</span>
			</a>
		</li>
		<li class="">
			<a href="{{ route('help_home') }}" class="d-flex justify-content-start align-items-center">
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
			<a href="{{ route('space') }}" class="d-flex justify-content-start align-items-center">
				<h1>
					<img src="{{ asset('images/icons/List.png') }}" alt="image">
				</h1>
				<span> @lang('messages.new_home.listings') </span>
			</a>
		</li>
		<li class="">
			<a href="{{ route('current_bookings') }}" class="d-flex justify-content-start align-items-center">
				<h1>
					<img src="{{ asset('images/icons/Booking.png') }}" alt="image">
				</h1>
				<span> @lang('messages.new_space.your_bookings') </span>
			</a>
		</li>
		<li class="">
			<a href="{{ route('account') }}" class="d-flex justify-content-start align-items-center">
				<h1>
					<img src="{{ asset('images/icons/Account.png') }}" alt="image">
				</h1>
				<span> @lang('messages.header.account') </span>
			</a>
		</li>
		<li class="">
			<a href="{{ route('invite') }}" class="d-flex justify-content-start align-items-center">
				<h1>
					<img src="{{ asset('images/icons/Travel-Credit.png') }}" alt="image">
				</h1>
				<span> @lang('messages.header.invite_friends') </span>
			</a>
		</li>
		<li class="">
			<a href="{{ route('disputes') }}" class="d-flex justify-content-start align-items-center">
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
@endsection