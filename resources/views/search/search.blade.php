@extends('template')
@section('main')
<main ng-controller="search-page" ng-init="search_loading = true;">
	
	<div class="search-content d-flex" ng-init="per_hour='@lang("messages.space_detail.per_hour")'; review_text='@choice("messages.header.review",1)'; reviews_text='@choice("messages.header.review",2)';">

		<div class="search_filter cls_searchfilter d-flex justify-content-between align-items-center" ng-init="opened_filter = '';times_array={{ json_encode($times_array) }};start_time='{{ $start_time }}';end_time='{{ $end_time }}'">
			<nav class="navbar">

				<ul class="navbar-nav d-flex flex-row align-items-between flex-wrap" ng-cloak>
					<li class="nav-item dropdown keep-open">
						<button class="dbdate dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="filter_active('date_time')" data-target-filter="date_time" ng-click="update_opened_filter('date_time')" title="@{{ getSearchTitle() }}">
							<span ng-if="!is_filter_active('date_time')">
								@lang('messages.your_trips.dates')
							</span>
							<span ng-if="is_filter_active('date_time')">
								<span ng-if="is_filter_active('date')">
									@{{format_date(checkin, 'DD MMM')}}
									@{{ (checkin == checkout) ?'': ' - '+format_date(checkout, 'DD MMM')}}
								</span>
								<span ng-if="is_filter_active('time')">
									@{{ formatTime(start_time, 'hh:mm A') }} - @{{ formatTime(end_time, 'hh:mm A') }}
								</span>
							</span>
						</button>
						<div class="dropdown-menu cls_date_filter">
							<div class="d-flex flex-column flex-lg-row flex-md-row">
								<div class="search-date col-lg-6">

								</div>
								<div class="search-time col-lg-6 p-sm-0">
									<div class="cls_searchtime d-flex align-items-center my-5">
										<div class="col-md-6 p-0 pr-1">
											<label class="text-truncate mb-2"> @lang('messages.space_detail.start_time') </label>
											<select class="custom-select form-control" ng-model="start_time" ng-disabled="checkin != checkout">
												<option value=""> @lang('messages.space_detail.start_time') </option>
												<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-hide="key == '23:59:00'" ng-selected="start_time == key"> @{{value}} </option>
											</select>
										</div>

										<div class="col-md-6 p-0 pl-1">
											<label class="text-truncate mb-2"> @lang('messages.space_detail.end_time') </label>
											<select class="custom-select form-control" ng-model="end_time" ng-disabled="checkin != checkout">
												<option value=""> @lang('messages.space_detail.end_time') </option>
												<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-hide="key <= start_time" ng-selected="end_time == key"> @{{value}} </option>
											</select>
										</div>
									</div>

									<div class="d-flex align-items-between justify-content-between mt-6">
										<a href="#" class="btn btn-primary btn-sm" ng-click="resetDateTimefilter()"> @lang('messages.payments.clear') </a>
										<a href="#" class="btn btn-primary btn-sm" ng-click="apply_filters('date_time')"> @lang('messages.payments.apply') </a>
									</div>
								</div>
							</div>
						</div>
					</li>

					<li class="nav-item dropdown keep-open">
						<button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-target-filter="guests" ng-class="filter_active('guests')" ng-click="update_opened_filter('guests')">
							<span ng-if="!is_filter_active('guests')">
								@choice('messages.home.guest',2)
							</span>
							<span ng-if="is_filter_active('guests')">
								@{{search_guest}} @choice('messages.home.guest',2)
							</span>
						</button>
						<div class="dropdown-menu" ng-init="max_guest_limit={{$max_guest_limit}}">
							<div class="d-flex align-items-center">
								
								<div class="value-changer d-flex align-items-center" ng-init="search_guest={{$guest}}">
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<button class="btn input-group-text fa fa-minus" ng-click="search_guest = search_guest-1;" ng-disabled="search_guest < 2"></button>
										</div>
										<input type="number" name="search_guest" class="form-control" ng-model='search_guest' min="1" max="{{$max_guest_limit}}" ng-change="">
										<div class="input-group-append">
											<button class="btn input-group-text fa fa-plus" ng-click="search_guest = search_guest+1;" ng-disabled="search_guest >= max_guest_limit"></button>
										</div>
									</div>
								</div>
							</div>
							<div class="my-4 d-flex align-items-center justify-content-between filter-btn">
								<a href="javascript:void(0)" class="cancel-filter" ng-click="reset_filters('guests')">
									{{ trans('messages.your_reservations.cancel') }}
								</a>
								<a href="javascript:void(0)" class="apply-filter" ng-click="apply_filters('guests')">
									{{ trans('messages.payments.apply') }}
								</a>
							</div>
						</div>
					</li>

					<li class="nav-item dropdown keep-open d-none d-md-block" ng-init="currency_symbol = '{{ html_string($currency_symbol) }}';min_value={{$min_price}};max_value={{$max_price}};max_slider_price = {{ $default_max_price }};" ng-hide="checkInValidInput(activity_type)">
						<button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="filter_active('prices')" ng-click="update_opened_filter('prices')">
							<span ng-if="!is_filter_active('prices')">
								@lang('messages.inbox.price')
							</span>
							<span ng-if="is_filter_active('prices')">
								@{{filter_btn_text('prices')}}
							</span>
						</button>
						<div class="dropdown-menu">
							<div class="price-label d-flex align-items-center">
								<div class="price-min">
									<span>{{ html_string($currency_symbol) }}</span>
									<span class="price" class="min_text">
										@{{ min_value }}
									</span>
								</div>
								<span class="mx-2">-</span>
								<div class="price-min">
									<span>{{ html_string($currency_symbol) }}</span>
									<span class="price" class="max_text">
										@{{ max_value }} @{{ (max_value == max_slider_price) ? '+' : '' }}
									</span>
								</div>
							</div>
							<div id="slider" class="mt-4 price-range-slider"></div>
							<div class="my-4 d-flex align-items-center justify-content-between filter-btn">
								<a href="javascript:void(0)" class="cancel-filter" ng-click="reset_filters('prices')">
									@lang('messages.your_reservations.cancel')
								</a>
								<a href="javascript:void(0)" class="apply-filter" ng-click="apply_filters('prices')">
									@lang('messages.payments.apply')
								</a>
							</div>
						</div>
					</li>
					<li class="nav-item dropdown keep-open d-none d-md-block">
						<button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ng-class="filter_active('instant_book')" ng-click="update_opened_filter('instant_book')">@lang('messages.lys.instant_book')</button>
						<div class="dropdown-menu">
							<div class="instant-book d-flex">
								<div class="instant-info">
									<h4>
										@lang('messages.lys.instant_book')
										<span>
											<i class="icon icon-instant-book"></i>
										</span>
									</h4>
									<p>
										@lang('messages.search.instant_book_desc')
									</p>
								</div>
								<div class="instant-checkbox">
									<label class="checkbox" ng-class="instant_book == '1' ? 'instant-checked' : ''">
										<input type="checkbox" name="instant_book" id="instant_book" ng-model="instant_book" ng-init="instant_book = '{{$instant_book}}'" ng-true-value="'1'" ng-false-value="'0'">
									</label>
								</div>
							</div>
							<div class="my-4 d-flex align-items-center justify-content-between filter-btn">
								<a href="javascript:void(0)" class="cancel-filter" ng-click="reset_filters('instant_book')">
									@lang('messages.your_reservations.cancel')
								</a>
								<a href="javascript:void(0)" class="apply-filter" ng-click="apply_filters('instant_book')">
									@lang('messages.payments.apply')
								</a>
							</div>
						</div>
					</li>
					<li class="nav-item dropdown keep-open">
						<button class="dropdown-toggle more-filter-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-target-filter="more_filters" ng-class="filter_active('more_filters')" ng-click="update_opened_filter('more_filters')">
							@lang('messages.search.more_filters')
							<span ng-if="is_filter_active('more_filters')">
								@{{filter_btn_text('more_filters')}}
							</span>
						</button>
					</li>

				</ul>
				<div class="search-bar cls_searchtop d-lg-block d-none d-md-block" data-reactid=".1">
					<form action="{{ url('s') }}" method="get">
						<div class="form-wrap d-flex justify-content-lg-between">
							<div class="select-date cls_mulselect cls_multires">
								<select name="space_type[]" class="selectpicker" title="@lang('messages.space_detail.space_type')" data-show-subtext="true" ng-model="space_type" multiple ng-change="apply_filters('space_type')">
									<option value="" disabled> @lang('messages.space_detail.space_type') </option>
									@foreach($header_space_type as $space_type)
									<option value="{{ $space_type->id }}" {{ in_array($space_type->id, $space_type_selected)  ? "checked" : ""}}> {{ $space_type->name }} </option>
									@endforeach
								</select>
							</div>
							<div class="select-date cls_mulselect ml-2" ng-init="activity_type = '{{ $selected_activity ?? '' }}'">
								<select name="activity" class="selectpicker" title="@lang('messages.space_detail.event_type')" data-show-subtext="true" data-live-search="true" ng-model="activity_type" ng-change="apply_filters('activity_type')">
									<option value="" disabled> @lang('messages.space_detail.event_type') </option>
									@foreach($header_activties as $activity)
									<option value="{{ $activity->id }}"> {{ $activity->name }} </option>
									@endforeach
								</select>
							</div>
						</div>
					</form>
				</div>
			</nav>
			<div class="clsmapcheck d-lg-block d-none">
				<div class="d-flex toggle-btn" ng-class="show_map ? 'active' : ''">
					<span> @lang('messages.search.show_map') </span>
					<input type="checkbox" ng-model="show_map" class="cb-value"/>
					<span class="round-btn"></span>
				</div>	
			</div>

		</div>

		<div class="cls_fullwidth" ng-class="show_map ? 'col-lg-8' : 'col-12'">
			<div class="cls_searchwarp" ng-init="checkin = '{{ $checkin }}';checkout = '{{ $checkout }}'">

				<input class="d-none" ng-model="checkin" readonly="readonly" autocomplete="off" type="text" ng-change="search_result();" placeholder="{{ trans('messages.home.checkin') }}">
				<input class="d-none" ng-model="checkout" readonly="readonly" autocomplete="off" type="text" ng-change="search_result();" placeholder="{{ trans('messages.home.checkout') }}">

				<div class="search-wrap d-md-flex flex-wrap row">
					<div class="search-list col-12 col-md-6 col-lg-3 cls_slength" ng-repeat="space in space_result.data" ng-class="show_map ? 'col-lg-4' : 'col-lg-3'" ng-cloak>
						<div ng-mouseover="on_mouse($index);" ng-mouseleave="out_mouse($index);">
							<div class="search-img">
								<div id="search-img-slide" class="search-img-slide owl-carousel">
									<a href="@{{space.link}}?checkin=@{{checkin}}&checkout=@{{checkout}}&start_time=@{{start_time}}&end_time=@{{end_time}}&guests=@{{guests}}&activity_type=@{{activity_type}}" ng-repeat="photo in space.space_photos" target="listing_@{{ space.id }}" id="space_image_@{{ space.id}}">
										<img ng-src="@{{ photo.name }}" alt="">
									</a>
								</div>
								<div class="search-wishlist">
									<input type="checkbox" id="wishlist-widget-@{{ space.id }}" name="wishlist-widget-@{{ space.id }}" ng-checked="space.saved_wishlists">
									<label for="wishlist-widget-@{{ space.id }}">
										<i class="icon icon-heart"></i>
										<i class="icon icon-heart-alt"  @if(Auth::user()) data-toggle="modal"@endif data-target="#wishlist-modal" id="wishlist-widget-icon-@{{ space.id }}" ng-click="saveWishlist(space)"></i>
									</label>
								</div>
							</div>
							<div class="search-info">
								<h4 class="text-truncate">
									<span>
										@{{ space.space_type_name }}
									</span>
									<span>·</span>
									<span> @{{ space.sq_ft_text }} </span>
								</h4>
								<a href="@{{space.link}}?checkin=@{{checkin}}&checkout=@{{checkout}}&start_time=@{{start_time}}&end_time=@{{end_time}}&guests=@{{guests}}&activity_type=@{{activity_type}}" target="listing_@{{ space.id }}" class="text-truncate" title="@{{ space.name }}">
									@{{ space.name }}
								</a>
								<p class="search-price">
									<span ng-bind-html="space.activity_price.currency.symbol"></span>
									<span ng-bind-html="space.space_activities[0].activity_price.hourly"></span>
									@lang("messages.space_detail.per_hour")
									<span ng-if="space.booking_type == 'instant_book'">
										<i class="icon icon-instant-book"></i>
									</span>
								</p>
								<div class="search-ratings">
									<a href="@{{space.link}}?checkin=@{{checkin}}&checkout=@{{checkout}}&start_time=@{{start_time}}&end_time=@{{end_time}}&guests=@{{guests}}&activity_type=@{{activity_type}}" class="d-flex align-items-center">
										<span class="d-inline-block" ng-show="space.overall_star_rating">
											<span class="d-inline-block align-middle" ng-bind-html="space.overall_star_rating"></span>
										</span>
										<span class="d-inline-block ml-2" ng-show="space.reviews_count">
											@{{ space.reviews_count }} @{{ space.reviews_count_lang }}
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="more-filter">
					<div class="cls_moreftl">
						<div class="search-bar cls_searchtop d-lg-none d-md-none d-sm-block mb-3 mt-3" data-reactid=".1">
							<form action="{{ url('s') }}" method="get">
								<div class="form-wrap d-flex justify-content-between">
									<div class="select-date cls_mulselect" ng-init="space_type = {{ json_encode($space_type_selected) }}">
										<select name="space_type[]" class="selectpicker" data-show-subtext="true" title="@lang('messages.space_detail.space_type')"  ng-model="space_type" multiple ng-change="apply_filters('space_type')">
											<option value="" disabled> @lang('messages.space_detail.space_type') </option>
											@foreach($header_space_type as $space_type)
											<option value="{{ $space_type->id }}" {{ in_array($space_type->id, $space_type_selected)  ? "checked" : ""}}> {{ $space_type->name }} </option>
											@endforeach
										</select>
									</div>
									<div class="select-date cls_mulselect" ng-init="activity_type = '{{ $selected_activity ?? '' }}'">
										<select name="activity" class="selectpicker" data-show-subtext="true" data-live-search="true" ng-model="activity_type" ng-change="apply_filters('activity_type')">
											<option value="" disabled> @lang('messages.space_detail.event_type') </option>
											@foreach($header_activties as $activity)
											<option value="{{ $activity->id }}"> {{ $activity->name }} </option>
											@endforeach
										</select>
									</div>
								</div>
							</form>
						</div>
						<!-- Price Filter -->
						<div class="filter-price d-lg-none d-md-none mb-5" ng-hide="checkInValidInput(activity_type)">
							<h4> {{ trans("messages.inbox.price") }} </h4>
							<div class="d-flex align-items-center">
								<div class="col-12 p-0">
									<div class="price-label d-flex align-items-center">
										<div class="price-min">
											<span>{{ html_string($currency_symbol) }}</span>
											<span class="price" class="min_text" id="min_text">
												@{{ min_value }}
											</span>
										</div>
										<span class="mx-2">-</span>
										<div class="price-min">
											<span>{{ html_string($currency_symbol) }}</span>
											<span class="price" class="max_text" id="max_text">
												@{{ max_value }} @{{ (max_value == max_slider_price) ? '+' : '' }}
											</span>
										</div>
									</div>
									<div id="mob_slider" class="mt-4 price-range-slider"></div>
								</div>
							</div>
						</div>
						<!-- Price Filter -->

						<!-- Instant Book Filter -->
						<div class="filter-instant_book d-lg-none d-md-none my-4">
							<div class="instant-book d-flex">
								<div class="instant-info">
									<h4>
										@lang('messages.lys.instant_book')
										<span>
											<i class="icon icon-instant-book"></i>
										</span>
									</h4>
									<p>
										@lang('messages.search.instant_book_desc')
									</p>
								</div>
								<div class="instant-checkbox">
									<label class="checkbox" ng-class="instant_book == '1' ? 'instant-checked' : ''">
										<input type="checkbox" name="instant_book" id="instant_book" ng-model="instant_book" ng-init="instant_book = '{{$instant_book}}'" ng-true-value="'1'" ng-false-value="'0'">
									</label>
								</div>
							</div>
						</div>
						<!-- Instant Book Filter -->

						@if($amenities->count() > 0)
						<div class="filter-list">
							<h4>
								{{ trans('messages.lys.amenities') }}
							</h4>
							<div class="all-list d-flex flex-wrap row mt-3">
								@foreach($amenities as $row_amenities)
								<div class="col-md-6 align-items-center mb-1 {{ ($loop->iteration < 5) ? '' : 'show_all-amenities d-none' }}">
									<input type="checkbox" id="amenities_{{ $row_amenities->id }}" value="{{ $row_amenities->id }}" class="amenities" {{(in_array($row_amenities->id, $amenities_selected)) ? 'checked' : ''}} />
									<label for="amenities_{{ $row_amenities->id }}">
										{{ $row_amenities->name }}
									</label>
								</div>
								@endforeach
								<div class="show-all-toggle pl-3 mt-2 d-flex align-items-center" data-target-filter="show_all-amenities">
									<div class="all-property mr-2">
										@lang('messages.header.seeall') @lang('messages.lys.amenities')
									</div>
									<div class="close-property mr-2">
										@lang('messages.home.close') @lang('messages.lys.amenities')
									</div>
									<i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						@endif

						@if($services->count() > 0)
						<div class="filter-list">
							<h4>
								{{ trans('messages.space_detail.services') }}
							</h4>
							<div class="all-list d-flex flex-wrap row mt-3">
								@foreach($services as $row_services)
								<div class="col-md-6 align-items-center mb-1 {{ ($loop->iteration < 5) ? '' : 'show_all-services d-none' }}">
									<input type="checkbox" id="services_{{ $row_services->id }}" value="{{ $row_services->id }}" class="services" {{(in_array($row_services->id, $services_selected)) ? 'checked' : ''}} />
									<label for="services_{{ $row_services->id }}">
										{{ $row_services->name }}
									</label>
								</div>
								@endforeach
								<div class="show-all-toggle pl-3 mt-2 d-flex align-items-center" data-target-filter="show_all-services">
									<div class="all-property mr-2">
										@lang('messages.header.seeall') @lang('messages.space_detail.services')
									</div>
									<div class="close-property mr-2">
										@lang('messages.home.close') @lang('messages.space_detail.services')
									</div>
									<i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						@endif

						@if($space_rules->count() > 0)
						<div class="filter-list">
							<h4>
								{{ trans('messages.space_detail.space_rules') }}
							</h4>
							<div class="all-list d-flex flex-wrap row mt-3">
								@foreach($space_rules as $row_rules)
								<div class="col-md-6 align-items-center mb-1 {{ ($loop->iteration < 5) ? '' : 'show_all-space_rules d-none' }}">
									<input type="checkbox" id="space_rules_{{ $row_rules->id }}" value="{{ $row_rules->id }}" class="space_rules" {{(in_array($row_rules->id, $space_rules_selected)) ? 'checked' : ''}} />
									<label for="space_rules_{{ $row_rules->id }}">
										{{ $row_rules->name }}
									</label>
								</div>
								@endforeach
								<div class="show-all-toggle pl-3 mt-2 d-flex align-items-center" data-target-filter="show_all-space_rules">
									<div class="all-property mr-2">
										@lang('messages.header.seeall') @lang('messages.space_detail.space_rules')
									</div>
									<div class="close-property mr-2">
										@lang('messages.home.close') @lang('messages.space_detail.space_rules')
									</div>
									<i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						@endif

						@if($space_styles->count() > 0)
						<div class="filter-list">
							<h4>
								{{ trans('messages.space_detail.space_style') }}
							</h4>
							<div class="all-list d-flex flex-wrap row mt-3">
								@foreach($space_styles as $row_style)
								<div class="col-md-6 align-items-center mb-1 {{ ($loop->iteration < 5) ? '' : 'show_all-space_style d-none' }}">
									<input type="checkbox" id="space_style_{{ $row_style->id }}" value="{{ $row_style->id }}" class="space_style" {{(in_array($row_style->id, $styles_selected)) ? 'checked' : ''}} />
									<label for="space_style_{{ $row_style->id }}">
										{{ $row_style->name }}
									</label>
								</div>
								@endforeach
								<div class="show-all-toggle pl-3 mt-2 d-flex align-items-center" data-target-filter="show_all-space_style">
									<div class="all-property mr-2">
										@lang('messages.header.seeall') @lang('messages.space_detail.space_style')
									</div>
									<div class="close-property mr-2">
										@lang('messages.home.close') @lang('messages.space_detail.space_style')
									</div>
									<i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						@endif

						@if($special_features->count() > 0)
						<div class="filter-list">
							<h4>
								{{ trans('messages.space_detail.special_features') }}
							</h4>
							<div class="all-list d-flex flex-wrap row mt-3">
								@foreach($special_features as $row_features)
								<div class="col-md-6 align-items-center mb-1 {{ ($loop->iteration < 5) ? '' : 'show_all-special_features d-none' }}">
									<input type="checkbox" id="special_feature_{{ $row_features->id }}" value="{{ $row_features->id }}" class="special_feature" {{(in_array($row_features->id, $special_features_selected)) ? 'checked' : ''}} />
									<label for="special_feature_{{ $row_features->id }}">
										{{ $row_features->name }}
									</label>
								</div>
								@endforeach
								<div class="show-all-toggle pl-3 mt-2 d-flex align-items-center" data-target-filter="show_all-special_features">
									<div class="all-property mr-2">
										@lang('messages.header.seeall') @lang('messages.space_detail.special_features')
									</div>
									<div class="close-property mr-2">
										@lang('messages.home.close') @lang('messages.space_detail.special_features')
									</div>
									<i class="fa fa-angle-down" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						@endif
					</div>
				</div>
				<div class="d-flex align-items-center justify-content-between filter-btn cls_moreftl_btn">
					<a href="javascript:void(0)" class="ml-auto mr-4 cancel-filter reset-more_filter" ng-click="reset_filters('more_filters')">
						{{ trans('messages.your_reservations.cancel') }}
					</a>
					<a href="javascript:void(0)" class="btn btn-secondary apply-filter reset-more_filter" ng-click="apply_filters('more_filters')">
						{{ trans('messages.new_home.see_space') }}
					</a>
				</div>
				<h2 ng-hide="space_result.data.length || search_loading" class="text-center" id="no_results" ng-cloak>
					{{ trans('messages.search.no_results_found') }}
				</h2>
				<div class="results-pagination mb-4" ng-cloak>
					<div class="pagination-container">
						<div class="results-count">
							<p>
								<span ng-if="space_result.to != 0">
									@{{ space_result.from }} –
								</span>
								@{{ space_result.to }} {{ trans('messages.search.of') }} @{{ space_result.total }} {{ trans('messages.new_space.spaces') }}
							</p>
						</div>
						<posts-pagination ng-if="space_result.total != 0"></posts-pagination>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-lg-4 search-map cls_searchmap p-0" ng-show="show_map && !search_loading">
	<div id="map_canvas" role="presentation" class="map-canvas">
	</div>
</div>
<div class="filter-section text-center d-lg-none d-md-block">
	<div class="d-inline-flex align-items-center justify-content-center">
		<button type="button" class="btn btn-primary show-map" ng-click="show_map=true;mapToggleClicked();">
			<span> @lang('messages.search.map') </span>
		</button>
		<button type="button" class="btn btn-primary show-result" ng-click="show_map=false;mapToggleClicked();">
			<span> @lang('messages.search.results') </span>
		</button>
	</div>
</div>
</div>

<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="lat" value="{{ $lat }}">
<input type="hidden" id="long" value="{{ $long }}">
<!-- Language Translate for inside Search maps -->
<input type="hidden" id="current_language" value= "{{ trans('messages.search.search_name') }}">
<input type="hidden" id="redo_search_value" value= "{{ trans('messages.search.redo_search_name') }}">
<!-- Pagination next prev used-->
<input type="hidden" id="pagin_next" value= "{{ trans('messages.pagination.pagi_next') }}">
<input type="hidden" id="pagin_prev" value= "{{ trans('messages.pagination.pagi_prev') }}">
<input type="hidden" id="viewport" value='{!! json_encode($viewport) !!}' ng-model="viewport">
<!--Wishlist Modal -->
<div class="wishlist-popup modal fade" id="wishlist-modal" tabindex="-1" role="dialog" aria-labelledby="Wishlist-ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header border-0 p-0">
				
			</div>
			<div class="modal-body p-0">
				<div class="d-md-flex">
					<div class="col-12 col-md-7 background-listing-img" style="background-image:url();">
						<div class=" mb-3 d-flex align-items-center">
							<div class="profile-img mr-3 mt-2">
								<img class="host-profile-img" src="">
							</div>
							<div class="profile-info mt-2">
								<h4 class="wl-modal-listing-name">
								</h4>
								<span class="wl-modal-listing-address">
								</span>
							</div>
						</div>
					</div>
					<div class="add-wishlist d-flex flex-column col-12 col-md-5">
						<div class="wish-title pt-5 pb-3">
							<h3>
								{{ trans('messages.wishlist.save_to_wishlist') }}
							</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							</button>
						</div>
						<div class="wl-modal-wishlists d-flex flex-grow-1 flex-column">
							<ul class="mb-auto">
								<li class="d-flex align-items-center justify-content-between" ng-repeat="item in wishlist_list" ng-class="(item.saved_id) ? 'active' : ''" ng-click="wishlist_row_select($index)" id="wishlist_row_@{{ $index }}">
									<span class="d-inline-block text-truncate">@{{ item.name }}</span>
									<div class="wl-icons ml-2">
										<i class="icon icon-heart-alt" ng-hide="item.saved_id"></i>
										<i class="icon icon-heart" ng-show="item.saved_id"></i>
									</div>
								</li>
							</ul>
							<div class="wl-modal-footer pt-3 pb-3">
								<form class="wl-modal-form d-none">
									<div class="d-flex align-items-center">
										<input type="text" class="wl-modal-input flex-grow-1 border-0" autocomplete="off" id="wish_list_text" value="" placeholder="Name Your Wish List" required>
										<button id="wish_list_btn" class="btn btn-contrast ml-3">
											{{ trans('messages.wishlist.create') }}
										</button>
									</div>
								</form>
								<div class="create-wl">
									<a href="javascript:void(0)">
										{{ trans('messages.wishlist.create_new_wishlist') }}
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</main>
@stop
@push('scripts')
<script type="text/javascript">
	var min_slider_price = {!! $default_min_price !!};
	var max_slider_price = {!! $default_max_price !!};
	var min_slider_price_value = {!! $min_price !!};
	var max_slider_price_value = {!! $max_price !!};
	$(document).ready(function() {
		$("#wish_list_text").keyup(function(){
			$('#wish_list_btn').prop('disabled', true);
			var v_value =  $(this).val();
			var len =v_value.trim().length;
			if (len == 0) {
				$('#wish_list_btn').prop('disabled', true);
			}
			else {
				$('#wish_list_btn').prop('disabled', false);
			}
		});
	});
	var APPLY_LANG = "@lang('messages.payments.apply')";
	var CLEAR_LANG = "@lang('messages.payments.clear')";
</script>
<script src="{{url('js/search.js?v='.$version)}}"></script>
@endpush