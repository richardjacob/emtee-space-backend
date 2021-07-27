<header class="cls_header">
	<div class="header">
		<nav class="navbar navbar-expand-lg d-flex justify-content-between align-items-center">
			<div class="logo d-none d-lg-block">
				<a class="navbar-brand" href="{{ route('home_page') }}">
					<img 
						src="{{ Route::currentRouteName() == 'home_page' ? LOGO_URL : SECONDARY_LOGO }}" 
						
						@if(Route::currentRouteName() != 'home_page')
							height="70" 
						@else
							height="150" width="150" 
						@endif

					/>
				</a>
			</div>
			@if(Route::currentRouteName() != 'home_page')
			<div class="logo d-block d-lg-none cls_navhide" class="navbar-toggler">
				<a class="navbar-brand" href="{{ route('home_page') }}">
					<img src="{{ SECONDARY_LOGO }}" />
				</a>
			</div>
			@endif

			{{--
				Hide responsive toggler
				<button class="navbar-toggler cls_navhide" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<i class="fa fa-angle-down"></i>
				</button>
				--}}			

				@if(request()->segment(1) != 'help' && Route::currentRouteName() != 'home_page')
				<div class="search-bar-wrapper">
					<form action="{{ route('search_page') }}" class="search-form {{ request()->segment(1) != 's' ? '' : 'search_header_form' }}">
						<div class="search-bar">
							<i class="icon icon-search icon-gray d-lg-block d-none"></i>
							<input id="header-search-form" type="text" name="location" class="location d-none d-lg-block text-truncate" placeholder="{{ trans('messages.header.where_are_you_going') }}" />
							<button type="button" data-toggle="modal" data-target="#search-modal-sm" class="location search-modal-trigger d-block d-lg-none text-left">
								<img src="@asset(images/search.png)" alt="search" style="width: 27px;">
								<!-- {{ trans('messages.header.where_are_you_going') }} -->
							</button> 
						</div>

						<div id="header-search-settings" class="search-settings">
							<div class="row">
								<div class="col-md-4 pr-0">
									<label for="header-search-checkin" class="field-label">
										<strong>{{ trans('messages.home.checkin') }}</strong>
									</label>
									<input type="text" readonly="readonly" autocomplete="off" id="header-search-checkin" data-field-name="check_in_dates" class="checkin ui-datepicker-target" onfocus="this.blur()"  placeholder="{{ trans('messages.rooms.dd-mm-yyyy') }}">
									<input type="hidden" name="checkin">
								</div>

								<div class="col-md-4 pr-0">
									<label for="header-search-checkout" class="field-label">
										<strong>{{ trans('messages.home.checkout') }}</strong>
									</label>
									<input type="text" readonly="readonly" autocomplete="off" id="header-search-checkout" data-field-name="check_out_dates" class="checkout ui-datepicker-target" onfocus="this.blur()"  placeholder="{{ trans('messages.rooms.dd-mm-yyyy') }}">
									<input type="hidden" name="checkout">
								</div>

								<div class="col-md-4">
									<label for="header-search-guests" class="field-label">
										<strong>{{ trans_choice('messages.home.guest', 2) }}</strong>
									</label>
									<div class="select select-block">
										<select id="header-search-guests" data-field-name="number_of_guests" name="guests">
											@for($i=1;$i<=16;$i++)
											<option value="{{ $i }}"> {{ ($i == '16') ? $i.'+ ' : $i }} </option>
											@endfor
										</select>
									</div>
								</div>
							</div>
							<div class="explore_list">
								<div class="home_pro">
									<strong>{{ trans('messages.header.room_type') }}</strong>
									<div class="check_list">
										@foreach($header_space_type as $row_room)
										<div class="explore_check d-flex align-items-center">
											<input type="checkbox" value="{{ @$row_room->id }}" id="room-type-{{ @$row_room->id }}" class="head_room_type" {{@in_array($row_room->id, @$space_type_selected) ? 'checked' : ''}} />
											<i class="icon-activities">
												<img src="{{ $row_room->image_name }}">
											</i>
											<label class="search_check_label" for="room-type-{{ @$row_room->id }}">{{ @$row_room->name }}</label>
										</div>
										@endforeach
									</div>
								</div>
							</div>
							<div class="mt-3">
								<button type="submit" class="btn btn-primary btn-block">
									<i class="icon icon-search"></i>
									<span> @lang('messages.new_home.find_space') </span>
								</button>
							</div>
						</div>
					</form>
				</div>
				@endif

				<div class="main-menu collapse navbar-collapse d-none" id="navbarSupportedContent">
					<ul class="navbar-nav align-items-lg-center ml-auto d-none d-lg-flex cls_desknav">
						<li class="nav-item">
							<a class="nav-link {{ auth()->check() ? '' : 'login_popup_open' }}" href="{{ route('manage_space.new') }}">
								@lang('messages.new_space.list_your_space')
							</a>
						</li>

						@guest
						<li class="nav-item">
							<a class="nav-link" href="{{ url('help') }}">
								{{ trans('messages.header.help') }}
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link signup_popup_head" href="{{ url('signup_login') }}" data-toggle="modal" data-target="#signup-popup">
								{{ trans('messages.header.signup') }}
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link login_popup_head" href="{{ url('login') }}" data-toggle="modal" data-target="#login-popup">
								{{ trans('messages.header.login') }}
							</a>
						</li>
						@else
						<li class="nav-item">
							<a class="nav-link" href="{{ route('current_bookings') }}">
								<span class="trip-pos">
									@lang('messages.new_home.bookings')
								</span>
							</a>
						</li>

						<li class="nav-item" id="inbox-item" ng-init="inbox_count='{{ @Auth::user()->inbox_count()}}'">
							<a class="nav-link" href="{{ route('inbox') }}">
								<span class="position-relative"> 
									{{ trans_choice('messages.dashboard.message', 2) }}
									<i class="alert-count text-center" ng-class="inbox_count != '0' ? '' : 'fade'" ng-cloak> @{{ inbox_count }} </i>
								</span>
							</a>
							<div class="tooltip tooltip-top-right dropdown-menu list-unstyled header-dropdown
							notifications-dropdown d-none"></div>
							<div class="panel drop-down-menu-msg d-none js-become-a-host-dropdown">
								<div class="trip-width">
									<div class="panel-header section-header-home">
										<strong>
											<span>Messages</span>
										</strong>
										<a href="{{ url('inbox') }}" class="view-trips">
											<strong>
												<span>View Inbox</span>
											</strong>
										</a>
									</div>
									<div class="panel-header section-header-home" style="width:100%;">
										<strong>
											<span>Notifications</span>
										</strong>
										<a href="{{ url('dashboard') }}" class="view-trips">
											<strong>
												<span>View Dashboard</span>
											</strong>
										</a>
									</div>
									<div class="pull-left" style="width:100%;padding:15px 20px;">
										<p style="margin:0px;padding-top:10px !important;"> 
											There are 3 notifications waiting for you in your 
											<a style="color:#333;text-decoration:underline;" href="{{ url('dashboard') }}">  {{ trans('messages.header.dashboard') }} </a>.
										</p>
									</div>
								</div>
							</div>
						</li>

						<li class="nav-item" id="header-help-menu">
							<a class="nav-link" href="{{ url('help') }}">
								<span>
									{{ trans('messages.header.help') }}
								</span>
								<i class="help-icon"></i>
							</a>
						</li>

						<li class="nav-item dropdown cls_headerdown">
							<a class="nav-link dropdown-toggle menu-droplist align-items-center d-flex" id="navbarDropdown" href="{{ url('login') }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="value_name mr-2 d-none">
									{{ auth()->user()->first_name }}
								</span>
								<img src="{{ auth()->user()->profile_picture->header_src }}" />
							</a>
							<ul class="dropdown-menu custom-arrow top-right" aria-labelledby="navbarDropdown">
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('dashboard') }}">
										{{ trans('messages.header.dashboard') }}
									</a>
								</li>
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ route('space') }}">
										{{ trans_choice('messages.header.your_listing',2) }}
									</a>
								</li>
								<li class="dropdown-item reservations d-none">
									<a class="dropdown-link" href="{{ route('my_bookings') }}">
										{{ trans('messages.header.your_reservations') }}
									</a>
								</li>
								<li class="dropdown-item d-none">
									<a class="dropdown-link" href="{{ route('current_bookings') }}">
										{{ trans('messages.header.your_trips') }}
									</a>
								</li>

								@if(auth()->user()->saved_wishlists)
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('wishlists/my') }}">
										{{ trans_choice('messages.header.wishlist',2) }}
									</a>
								</li>
								@endif

								<li class="dropdown-item d-none">
									<a class="dropdown-link" href="{{ url('groups') }}">
										{{ trans('messages.header.groups') }}
									</a>
								</li>
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('invite') }}">
										{{ trans('messages.referrals.travel_credit') }}
										<span class="label label-pink label-new">
										</span>
									</a>
								</li>
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('users/edit') }}">
										{{ trans('messages.header.edit_profile') }}
									</a>
								</li>
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('account') }}">
										{{ trans('messages.header.account') }}
									</a>
								</li>
								<li class="dropdown-item business-travel d-none">
									<a class="dropdown-link" href="{{ url('business') }}">
										{{ trans('messages.header.business_travel') }}
									</a>
								</li>
								<li class="dropdown-item">
									<a class="dropdown-link" href="{{ url('logout') }}">
										{{ trans('messages.header.logout') }}
									</a>
								</li>
							</ul>
						</li>
						@endguest
					</ul>

					<ul class="navbar-nav align-items-lg-center ml-auto d-flex d-lg-none pt-2">	
						<li class="profile-link nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link d-flex align-items-center py-2 profile-img" href="{{ url('/') }}/users/show/{{ (auth()->user()) ? auth()->user()->id : '0' }}">
								<img src="{{(auth()->user()) ? auth()->user()->profile_picture->header_src : '' }}" />
								<span class="text-truncate">
									{{ (auth()->user()) ? auth()->user()->first_name : 'User' }}
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ url('/') }}">
								{{ trans('messages.header.home') }}
							</a>
						</li>
						<li>
							<hr/>
						</li>
						<li class="nav-item {{ (auth()->user()) ? 'd-none' : '' }}">
							<a class="nav-link" href="{{ route('manage_space.new') }}">
								{{ trans('messages.header.head_homes') }}
							</a>
						</li>

						<li class="nav-item {{ (auth()->user()) ? 'd-none' : '' }}">
							<a class="nav-link" href="{{ url('/') }}/signup_login">
								{{ trans('messages.header.signup') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? 'd-none' : '' }}">
							<a class="nav-link" href="{{ url('/') }}/login">
								{{ trans('messages.header.login') }}
							</a>
						</li>
						<li class="{{ (auth()->user()) ? 'd-none' : '' }}">
							<hr/>
						</li>

						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('dashboard') }}">
								{{ trans('messages.header.dashboard') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('users/edit') }}">
								{{ trans('messages.header.profile') }}
							</a>
						</li>

						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('account') }}">
								{{ trans('messages.header.account') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ route('current_bookings') }}">
								@lang('messages.new_home.bookings')
							</a>
						</li>
						@Auth
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}" ng-init="inbox_count='{{ Auth::user()->inbox_count()}}'">
							<a class="nav-link position-relative" href="{{ route('inbox') }}">
								{{ trans_choice('messages.dashboard.message', 2) }}
								<i class="alert-count text-center" ng-class="inbox_count != '0' ? '' : 'fade'" ng-cloak> @{{ inbox_count }} </i>
							</a>
						</li>
						@endAuth

						@if(@auth()->user()->saved_wishlists)
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('wishlists/my') }}">
								{{ trans_choice('messages.header.wishlist',2) }}
							</a>
						</li>
						@endif

						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ route('space') }}">
								{{ trans_choice('messages.header.your_listing',2) }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('disputes') }}">
								{{ trans('messages.disputes.disputes') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<hr/>
						</li>

						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ route('manage_space.new') }}">
								{{ trans('messages.header.head_homes') }}
							</a>
						</li>

						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<hr/>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ url('/') }}/help">
								{{ trans('messages.header.help') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('/') }}/invite">
								{{ trans('messages.header.invite_friends') }}
							</a>
						</li>
						<li class="nav-item {{ (auth()->user()) ? '' : 'd-none' }}">
							<a class="nav-link" href="{{ url('/') }}/logout">
								{{ trans('messages.header.logout') }}
							</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</header>
	@if((!in_array(Route::currentRouteName(),['payment.home'])) )
	@yield('responsive_footer_menu')
	@endif

	<div class="flash-container">
		@if(Session::has('message') && !isset($exception))
		@if(!auth()->check() || Route::currentRouteName() == 'space_details' || Route::currentRouteName() == 'payment.home')
		<div class="alert {{ Session::get('alert-class') }} text-center" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			{{ session('message') }}
		</div>
		@endif
		@endif
	</div>