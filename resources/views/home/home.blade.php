@extends('template')

@section('main')
<main id="site-content" role="main" ng-controller="home_owl" ng-cloak>

	<div class="cls_topbanner">
		<div class="owl-carousel owl-theme cls_hmslder">
			@foreach($sliders as $slider)
	            <div class="item">
	               <img src="{{ asset('images/plain-gray-background.jpg') }}" class="owl-lazy" data-src="{{ $slider->image_url }}">
	            </div>
            @endforeach
        </div>
	</div>
	<div class="home-info">
		<div class="container">
			<div class="cls_homeinfo">
				<div class="py-4 py-md-3 ">
					<h1 class="site-name">
						{{ $site_name }}
					</h1>
					<p class="site-desc animatable moveUp">
						@lang('messages.home.change_desc')
					</p>
				</div>
				<div class="banner-search animatable moveUp">
					<div class="search-bar m-auto d-none d-lg-block" data-reactid=".1">
						<form action="{{ route('search_page') }}" method="get">
							<div class="form-wrap d-flex justify-content-between align-items-center">
								<div class="search_location flex-grow-1">
									<input class="location" placeholder="@lang('messages.header.anywhere')" type="text" name="location" id="header-search-form" aria-autocomplete="both" value="">
									<span class="search_location_error set_location d-none"> @lang('messages.home.please_set_location') </span>
									<span class="search_location_error invalid_location d-none"> @lang('messages.home.search_validation') </span>
								</div>
								<div class="select-date d-flex flex-column cls_mulselect">
								<select name="activity_type" id="activity_select" class="selectpicker" data-show-subtext="true" data-live-search="true">
									<option value="" disabled selected> @lang('messages.new_space.select_activity') </option>
									@foreach($header_activties as $activity)
										<option value="{{ $activity->id }}" > {{ $activity->name }} </option>
									@endforeach
								</select>
							  </div>
								<button id="submit_location" type="submit" class="btn btn-primary ml-auto my-auto">
									@lang('messages.home.search')
								</button>
							</div>
						</form>
					</div>

					<div class="search-modal-trigger col-md-12 p-0 m-auto d-flex d-lg-none" data-toggle="modal" data-target="#search-modal-sm">
						<span class="search-btn-field justify-content-between align-items-center">
							@lang('messages.header.anywhere')
						</span>
					</div>
				</div>
				<!-- <div class="banne_cont">
					<h2>Finding an affordable working space is hard. <span> {{ $site_name }} </span> makes it easy.</h2>
					<span>Welcome to the work-space revolution!</span>
				</div> -->
			</div>
		</div>
	</div>

	<div class="whole-slider-wrap" ng-cloak>
		<div class="container">
			<div class="my-4 d-flex justify-content-between align-items-center" ng-if="just_booked.length > 0">
				<h2 class="title-sm" >
					{{ trans('messages.header.justbooked') }}
				</h2>
			</div>
			<ul id="booked" class="owl-carousel">
				<li ng-repeat="(key, fetch_data) in just_booked">
					<div class="pro-img">
						<a href="@{{ fetch_data.rooms.link }}">
							<img ng-src="@{{fetch_data.rooms.photo_name}}" />
						</a>
					</div>
					<div class="pro-info">
						<h4 class="text-truncate">
							<span> @{{ fetch_data.rooms.space_type_name }} </span>
							<span>·</span>
							<span> @{{ fetch_data.rooms.beds }} @{{ fetch_data.rooms.bed_lang }} </span>
						</h4>
						<a href="@{{ fetch_data.rooms.link }}" title="@{{ fetch_data.rooms.name}}">
							<h5 class="text-truncate">
								@{{ fetch_data.rooms.name}}
							</h5>
						</a>
						<p class="price">							
							<span ng-bind-html="fetch_data.currency.symbol"></span> @{{ fetch_data.rooms.rooms_price.night }}
							{{ trans("messages.rooms.per_night") }}
							<span ng-if="fetch_data.rooms.booking_type == 'instant_book'"> 
								<i class="icon icon-instant-book"></i>
							</span>
						</p>

						<div class="d-flex align-items-center">                                              
							<span ng-bind-html="fetch_data.rooms.overall_star_rating"> </span>
							<span class="review-count mx-2" ng-if="fetch_data.rooms.reviews_count > 0">
								@{{ fetch_data.rooms.reviews_count }}
							</span>
							<span class="review-label" ng-if="fetch_data.rooms.overall_star_rating">
								@{{ fetch_data.rooms.reviews_count_lang }}
							</span>
						</div>
					</div>
				</li>
			</ul>
			<div class="mt-3 mt-md-0 mb-5" ng-if="just_booked.length > 8">
				<a class="see-all-link d-md-inline-flex align-items-center" href="{{ route('search_page') }}">
					<span>
						{{ trans('messages.header.seeall') }}
					</span>
					<i class="icon icon-chevron-right ml-2"></i>
				</a>
			</div>

			<div class="my-4 d-flex justify-content-between align-items-center" ng-if="recommended.length > 0">
				<h2 class="title-sm">
					{{ trans('messages.header.recommend') }}
				</h2>
			</div>
			<ul id="recommended" class="owl-carousel">
				<li ng-repeat="room in recommended">
					<div class="pro-img">
						<a href="@{{ room.link }}">
							<img ng-src="@{{room.photo_name}}" />
						</a>
					</div>
					<div class="pro-info">
						<h4 class="text-truncate">
							<span>@{{ room.space_type_name }}</span>
							<span>·</span>
							<span>@{{ room.number_of_guests }} @{{ room.bed_lang }} </span>
						</h4>
						<a href="@{{ room.link }}" title="@{{ room.name }}">
							<h5 class="text-truncate"> @{{ room.name }} </h5>
						</a>
						<p class="price">							
							<span ng-bind-html="room.rooms_price.currency.symbol"></span> @{{ room.rooms_price.night }}
							{{ trans("messages.rooms.per_night") }}
							<span ng-if="room.booking_type == 'instant_book'"> 
								<i class="icon icon-instant-book"></i>
							</span>
						</p>
						<div class="d-flex align-items-center">                                              
							<span ng-bind-html="room.overall_star_rating"> </span>
							<span class="review-count mx-2" ng-if="room.reviews_count > 0">
								@{{ room.reviews_count }}
							</span>
							<span class="review-label" ng-if="room.overall_star_rating">
								@{{ room.reviews_count_lang }}
							</span>
						</div>
					</div>
				</li>
			</ul>
			<div class="mt-3 mt-md-0 mb-5" ng-if="recommended.length > 8">
				<a class="see-all-link d-md-inline-flex align-items-center" href="{{ route('search_page') }}">
					<span> {{ trans('messages.header.seeall') }} </span>
					<i class="icon icon-chevron-right ml-2"></i>
				</a>
			</div>

			<div class="my-4 d-flex justify-content-between align-items-center" ng-if="most_viewed.length > 0">
				<h2 class="title-sm">
					{{ trans('messages.header.most_viewed') }}
				</h2>
			</div>
			<ul id="most-viewed" class="owl-carousel">
				<li ng-repeat="room in most_viewed">
					<div class="pro-img">
						<a href="@{{ room.link }}">
							<img ng-src="@{{room.photo_name}}" />
						</a>
					</div>
					<div class="pro-info">
						<h4 class="text-truncate">
							<span>@{{ room.space_type_name }}</span>
							<span>·</span>
							<span>@{{ room.number_of_guests }} @{{ room.bed_lang }} </span>
						</h4>
						<a href="@{{ room.link }}" title="@{{ room.name }}">
							<h5 class="text-truncate"> @{{ room.name}} </h5>
						</a>
						<p class="price">							
							<span ng-bind-html="room.rooms_price.currency.symbol"></span> @{{ room.rooms_price.night }}
							{{ trans("messages.rooms.per_night") }}
							<span ng-if="room.booking_type == 'instant_book'"> 
								<i class="icon icon-instant-book"></i>
							</span>
						</p>
						<div class="d-flex align-items-center">                                              
							<span ng-bind-html="room.overall_star_rating"> </span>
							<span class="review-count mx-2" ng-if="room.reviews_count > 0">
								@{{ room.reviews_count }}
							</span>
							<span class="review-label" ng-if="room.overall_star_rating">
								@{{ room.reviews_count_lang }}
							</span>
						</div>
					</div>
				</li>
			</ul>
			<div class="mt-3 mt-md-0 mb-5" ng-if="most_viewed.length > 8">
				<a class="see-all-link d-md-inline-flex align-items-center" href="{{ route('search_page') }}">
					<span>
						{{ trans('messages.header.seeall') }}
					</span>
					<i class="icon icon-chevron-right ml-2"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="cls_catelist popular_venue" ng-init="popular_activities={{$popular_activities}}">
		<div class="container">
			<div class="my-5 text-center">
				<h2 class="title animatable moveUp">
					@lang('messages.new_home.popular_activities')
				</h2>
			</div>
			<div class="d-flex flex-wrap">				
				<div class="cls_catelistin col-12 col-md-6 col-lg-4 p-l0" ng-repeat="activity in popular_activities">
					<div class="cls_cateimglist">
						<a href="@{{ activity.search_url }}">
							<img ng-src="@{{activity.image_url}}">
							<div class="cls_catelisttext">
								<h2> @{{ activity.name }} </h2>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- <div class="cls_catelist popular_venue" ng-init="popular_space_type={{$popular_space_type}}">
		<div class="container">
			<div class="my-5 text-center">
				<h2 class="title animatable moveUp">
					@lang('messages.new_home.popular_activities')
				</h2>
			</div>
			<div class="d-flex flex-wrap">				
				<div class="cls_catelistin col-12 col-md-3 col-sm-3 col-lg-3 p-l0" ng-repeat="space_type in popular_space_type">
					<div class="cls_cateimglist">
						<a href="@{{ space_type.search_url }}">
							<img ng-src="@{{space_type.image}}">
							<div class="cls_catelisttext">
								<h2> @{{ space_type.name }} </h2>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div> -->

	<div class="cls_howitworks">
		<div class="container">
			<div class="my-5 text-center">
				<h2 class="title animatable moveUp"> @lang('messages.home.how_it_works') </h2>
				<p class="desc animatable moveUp">
					@lang('messages.new_home.how_it_works_desc')
				</p>
			</div>
			<div class="d-md-flex pb-5 animatable moveUp">
				<div class="col-12 col-md-4 col-lg-4">
					<div class="cls_howitimg">
						<img src="@asset(images/how_1.png)">
						<p>@lang('messages.new_home.how_it_works_desc1')</p>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="cls_howitimg">
						<img src="@asset(images/how_2.png)">
						<p>@lang('messages.new_home.how_it_works_desc2')</p>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="cls_howitimg">
						<img src="@asset(images/how_3.png)">
						<p>@lang('messages.new_home.how_it_works_desc3')</p>
					</div>
				</div>

			</div>
		</div>
	</div>	
		
	<div class="our-community" ng-show="our_community.length > 0" ng-init="our_community={{$our_community_banners}}">
		<div class="container">		
			<div class="my-5 text-center">
				<h2 class="title animatable bounceInLeft"> {{trans('messages.home.our_community')}} </h2>
				<p class="desc animatable bounceInLeft">
					Find inspiration for your next trip and get advice from travelers who have been there before.
				</p>
			</div>
			<div class="community-content">
				<div class="item cls_commuitem col-md-4 col-lg-4 col-sm-12 col-12" ng-repeat="our_community in our_community ">
					<a href="@{{ our_community.link }}" class="d-flex flex-column h-100 w-100 text-center" target="_blank">
						<img ng-src="@{{our_community.image_url}}" alt="image">
						<div class="cls_commuitext">
							<div class="cls_commuitextin">
								<h2 class="mt-auto"> @{{ our_community.title }} </h2>
								<p ng-bind-html="our_community.description"></p>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
			<div class="animatable bounceInLeft text-center"><a href="#" class="cls_communitybtn">Explore our community</a></div>
	</div>
	
	
</main>
@stop