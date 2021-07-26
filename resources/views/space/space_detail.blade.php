@extends('template')
@section('main')
<main id="site-content" role="main" ng-controller="space_detail">
	<div class="detail-sticky">
		<div class="container">
			<ul>
				<li>
					<a href="#detail-gallery">
						@choice('messages.header.photo',2)
					</a>
				</li>
				<li>
					<a href="#about-scroll">
						@lang('messages.rooms.about_this_listing')
					</a>
				</li>
				<li>
					<a href="#review-info">
						@choice('messages.header.review',2)
					</a>
				</li>
				<li>
					<a href="#host-profile">
						@lang('messages.rooms.the_host')
					</a>
				</li>
				<li>
					<a href="#detail-map">
						@lang('messages.your_trips.location')
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="detail-banner">
		<ul id="detail-gallery" class="detail-slider scroll-section">
			@foreach($space_photos as $row_photos)
			<li data-thumb="{{ $row_photos->slider_image_name }}" data-src="{{ $row_photos->slider_image_name }}" data-sub-html=".caption_{{ $row_photos->id }}">
				<img src="{{ $row_photos->slider_image_name }}" title="{{ $row_photos->highlights }}">
				<div class="caption_{{ $row_photos->id }}">
					<p> {{ $row_photos->highlights }} </p>
				</div>
			</li>
			@endforeach
		</ul>
	</div>
	<div class="detail-content" ng-init="space_id = {{ $space_id }};currency_symbol='{{ $currency_symbol}}';default_price='{{ $default_price }}';times_array={{ json_encode($times_array) }};">
	<div class="container">
		<div class="detail-wrap row">
			<div class="col-12 col-lg-8 content-wrap">
				<div class="user-wrap pt-4 mt-3 pb-3 d-md-flex">
					<div class="user-img text-center">
						<a href="{{ route('show_profile',$result->user_id) }}">
							<img alt="User Profile Image" class="profile-image" data-pin-nopin="true" src="{{ $result->users->profile_picture->src }}" title="{{ $result->users->first_name }}">
							<h4 class="text-truncate">
								{{ $result->users->first_name }}
							</h4>
						</a>
					</div>
					<div class="user-info pl-md-5 flex-grow-1">
						<h3>
							{{ $result->name }}
						</h3>
						<p href="javascript:void(0)" class="room-place mr-2">
							{{ $result->space_address->city }}
							@if($result->space_address->city !=''), @endif
							{{$result->space_address->state}}
							@if($result->space_address->state !=''), @endif
							{{ $result->space_address->country_name }}
						</p>
						@if($result->overall_star_rating)
						<a href="#reviews" class="review_link">
							<div class="star-rating-wrapper d-flex align-items-center">
								{!! $result->overall_star_rating !!}
								<span class="ml-2">({{ $result->reviews->count() }})</span>
							</div>
						</a>
						@endif
						<div class="room-type row mt-3 text-center">
							<div class="col-4 room-icon">
								<img class="detail-icon" src="@asset(images/list_space/type_space.png)">
								<div class="numfel">
									{{ $result->space_type_name }}
								</div>
							</div>
							<div class="col-4 room-icon">
								<img class="detail-icon" src="@asset(images/list_space/guests.png)">
								<div class="numfel">
									{{ $result->number_of_guests }} @lang('messages.space_detail.people')
								</div>
							</div>
							<div class="col-4 room-icon">
								<img class="detail-icon" src="@asset(images/list_space/size.png)">
								<div class="numfel">
									{{ $result->sq_ft_text }}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="detail-info">
					<div id="about-scroll" class="about-listing scroll-section py-4 text-center text-md-left">
						<h4 class="title">
							@lang('messages.rooms.about_this_listing')
						</h4>
						<p>
							{!! nl2br($result->summary) !!}
						</p>
						@auth
						@if(optional(auth()->user())->id != $result->user_id)
						<a id="contact-host-link" href="javascript:void(0);" class="mt-3 theme-link" data-toggle="modal" data-target="#contact-modal">
							<strong>
								@lang('messages.rooms.contact_host')
							</strong>
						</a>
						@endif
						@endauth
					</div>
					<div class="space-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label>
									@lang('messages.lys.the_space')
								</label>
							</div>
							<div class="col-md-9 col-sm-12">
								<div class="row">
									<div class="col-md-6">
										<div class="cls_bullet">
											@lang('messages.space_detail.space_type'): <strong> {{ $result->space_type_name }} </strong>
										</div>
										<div class="cls_bullet">
											@lang('messages.space_detail.maximum_guests'): <strong> {{ $result->number_of_guests }} </strong>
										</div>
									</div>
									<div class="col-md-6">
										<div class="cls_bullet" ng-show="{{ $result->number_of_rooms > 0}}">
											@lang('messages.basics.number_of_rooms'):
											<strong>{{ $result->number_of_rooms }}</strong>
										</div>
										<div class="cls_bullet" ng-show="{{ $result->number_of_restrooms > 0}}">
											@lang('messages.basics.number_of_restrooms'):
											<strong>{{ $result->number_of_restrooms }}</strong>
										</div>
										<div ng-show="{{ $result->floor_number > 0}}" class="cls_bullet">
											@lang('messages.basics.floor_number'):
											<strong>{{ $result->floor_number }}</strong>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					@if($amenities->count() > 0)
					<div class="amenities-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label> @lang('messages.lys.amenities') </label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_all_amenities == true">
										@foreach($amenities as $all_amenities)
										@if($loop->index < 5)
										<div class="col-md-6 amenities-icon mb-2 d-flex align-items-center">
											<img class="icon-img" src="{{ $all_amenities->image_name}}"/>
											<span class="future_basics text-truncate">
												<strong> {{ $all_amenities->name }} </strong>
											</span>
										</div>
										@endif
										@endforeach
										@if($amenities->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_all_amenities = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_all_amenities == true">
										@foreach($amenities as $all_amenities)
										<div class="col-md-6 amenities-icon mb-2 d-flex align-items-center new_id {{ $all_amenities->status != null ? '' : 'text-muted' }}">
											<img class="icon-img" src="{{ $all_amenities->image_name}}"/>
											<span class="future_basics text-truncate">
												<strong> {{ $all_amenities->name }} </strong>
											</span>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if($guest_access->count() > 0)
					<div class="guest_access-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label> @lang('messages.space_detail.guest_access') </label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_access == true">
										@foreach($guest_access as $access)
										@if($loop->index < 5)
										<div class="col-md-6 mb-2 d-flex align-items-center ">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $access->name }}
												</span>
											</div>
										</div>
										@endif
										@endforeach
										@if($guest_access->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_access = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_access == true">
										@foreach($guest_access as $access)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $access->name }}
												</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if($services->count() > 0 || $result->services_extra != '')
					<div class="services-info">
						<div class="py-4 row d-md-flex">
							@if($services->count() > 0)
							<div class="col-12 col-md-3">
								<label> @lang('messages.space_detail.services_offer') </label>
							</div>

							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_service == true">
										@foreach($services as $service)
										@if($loop->index < 5)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $service->name }}
												</span>
											</div>
										</div>
										@endif
										@endforeach
										@if($services->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_service = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_service == true">
										@foreach($services as $service)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $service->name }}
												</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
							@endif
							@if($result->services_extra != '')
							<div class="col-12 col-md-3 mt-2">
								<label> @lang('messages.space_detail.other_services_offer') </label>
							</div>
							<div class="col-md-9 mt-2">
								<div class="row">
									<div class="col-md-6 mb-2 d-flex align-items-center">
										<div class="cls_bullet">
											<span class="future_basics">
												{{ $result->services_extra }}
											</span>
										</div>
									</div>
								</div>
							</div>
							@endif
						</div>
					</div>
					@endif
					@if($special_feature->count() > 0)
					<div class="special_feature-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label> @lang('messages.space_detail.special_feature') </label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_feature == true">
										@foreach($special_feature as $feature)
										@if($loop->index < 5)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $feature->name }}
												</span>
											</div>
										</div>
										@endif
										@endforeach
										@if($special_feature->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_feature = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_feature == true">
										@foreach($special_feature as $feature)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $feature->name }}
												</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if($space_rules->count() > 0)
					<div class="space_rules-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label> @lang('messages.space_detail.space_rules') </label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_rule == true">
										@foreach($space_rules as $rule)
										@if($loop->index < 5)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $rule->name }}
												</span>
											</div>
										</div>
										@endif
										@endforeach
										@if($space_rules->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_rule = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_rule == true">
										@foreach($space_rules as $rule)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $rule->name }}
												</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if($space_style->count() > 0)
					<div class="space_style-info">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label> @lang('messages.space_detail.space_style') </label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more">
								<div class="expandable-content-summary">
									<div class="row" ng-hide="show_style == true">
										@foreach($space_style as $style)
										@if($loop->index < 5)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $style->name }}
												</span>
											</div>
										</div>
										@endif
										@endforeach
										@if($space_style->count() > 5)
										<div class="col-md-6">
											<a href="javascript:void(0)" class="expandable-trigger-more theme-link" ng-click="show_style = true;">
												<strong>+ {{ trans('messages.profile.more') }}</strong>
											</a>
										</div>
										@endif
									</div>
									<div class="row" ng-show="show_style == true">
										@foreach($space_style as $style)
										<div class="col-md-6 mb-2 d-flex align-items-center">
											<div class="cls_bullet">
												<span class="future_basics text-truncate">
													{{ $style->name }}
												</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if($result->space_description->space !='' || $result->space_description->access !='' || $result->space_description->interaction !='' || $result->space_description->neighborhood_overview !='' || $result->space_description->transit || $result->space_description->notes || $result->space_description->house_rules)
					@php
					$res =$result->space_description->toArray();
					$res = array_filter($res);
					@endphp
					<div class="description-info">
						<div class="py-4 row d-md-flex description">
							<div class="col-12 col-md-3">
								<label>
									{{ trans('messages.lys.description') }}
								</label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more all_description">
								@foreach (array_slice($res, 1, 2) as $key => $value)
								@if($key == 'space')
								<p><strong>{{ trans('messages.lys.the_space') }}</strong></p>
								<p>{!! nl2br($result->space_description->space) !!}</p>
								@endif
								@if($key == 'access')
								<p><strong>{{ trans('messages.lys.guest_access') }}</strong></p>
								<p>{!! nl2br($result->space_description->access) !!} </p>
								@endif
								@if($key == 'interaction')
								<p><strong>{{ trans('messages.lys.interaction_with_guests') }}</strong></p>
								<p> {!! nl2br($result->space_description->interaction) !!}</p>
								@endif
								@if($key == 'neighborhood_overview')
								<p><strong>{{ trans('messages.lys.the_neighborhood') }}</strong></p>
								<p> {!! nl2br($result->space_description->neighborhood_overview) !!}</p>
								@endif
								@if($key == 'transit')
								<p><strong>{{ trans('messages.lys.getting_around') }}</strong></p>
								<p>{!! nl2br($result->space_description->transit) !!}</p>
								@endif
								@endforeach
								<div class="expandable-content" id="des_content" ng-show="show_more_desc">
									@foreach (array_slice($res, 3, count($res)) as $key => $value)
									@if($key == 'space')
									<p><strong>{{ trans('messages.lys.the_space') }}</strong></p>
									<p>{!! nl2br($result->space_description->space) !!}</p>
									@endif
									@if($key == 'access')
									<p><strong>{{ trans('messages.lys.guest_access') }}</strong></p>
									<p>{!! nl2br($result->space_description->access) !!} </p>
									@endif
									@if($key == 'interaction')
									<p><strong>{{ trans('messages.lys.interaction_with_guests') }}</strong></p>
									<p> {!! nl2br($result->space_description->interaction) !!}</p>
									@endif
									@if($key == 'notes')
									<p><strong>{{ trans('messages.lys.other_things_note') }}</strong></p>
									<p> {!! nl2br($result->space_description->notes) !!}</p>
									@endif
									@if($key == 'neighborhood_overview')
									<p><strong>{{ trans('messages.lys.the_neighborhood') }}</strong></p>
									<p> {!! nl2br($result->space_description->neighborhood_overview) !!}</p>
									@endif
									@if($key == 'transit')
									<p><strong>{{ trans('messages.lys.getting_around') }}</strong></p>
									<p>{!! nl2br($result->space_description->transit) !!}</p>
									@endif
									@endforeach
								</div>
								@if (count($res) > 3)
								<a class="expandable-trigger-more desc theme-link" id="desc" href="" ng-show="show_more_desc != true;" ng-click="show_more_desc=true;">
									<strong>+ {{ trans('messages.profile.more') }}</strong>
								</a>
								@endif
							</div>
						</div>
					</div>
					@endif
					@if($result->space_description->house_rules !='')
					<div class="house-rules">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label>
									{{ trans('messages.setup.space_rules') }}
								</label>
							</div>
							<div class="col-md-9 expandable expandable-trigger-more expanded col-sm-12">
								<div class="expandable-content">
									<p>{!! nl2br($result->space_description->house_rules) !!}</p>
									<div class="expandable-indicator"></div>
								</div>
							</div>
						</div>
					</div>
					@endif
					<div class="cancellation-policy">
						<div class="py-4 row d-md-flex">
							<div class="col-12 col-md-3">
								<label>
									@lang('messages.your_reservations.cancellation')
								</label>
							</div>
							<div class="col-md-9  col-sm-12">
								<a href="{{ url('/home/cancellation_policies#'.$result->cancellation_policy) }}" id="cancellation-policy" class="theme-link">
									@lang('messages.cancel_policy.'.strtolower($result->cancellation_policy))
								</a>
							</div>
						</div>
					</div>
					<div id="pricing-info" class="pricing-listing scroll-section py-4 text-center text-md-left">
						<h4 class="title">
							@lang('messages.lys.pricing')
						</h4>
						<div class="activity_price" ng-init="space_activities = {{$space_activities}}">
							@foreach($space_activities as $activity)
							<div class="mt-2 d-flex justify-content-start align-items-center ">
								<div class=" card " style="width: 150px;">
									<img class="card-img-top mt-2 p-2" src="{{ $activity->activity_type->image_url }}" alt="{{ $activity->activity_type->name }}" style="height: 100px;object-fit: contain; width: 150px;">
									<div class="card-footer mt-2 text-center d-inline-block text-truncate" style="max-width: 100%;">
										<span class="text-muted" title="{{ $activity->activity_type->name }}">
											{{ $activity->activity_type->name }}
										</span>
									</div>
								</div>
								<div class="ml-4">
									<div class="mt-2">
										<p> @lang('messages.ready_to_host.hourly_rate') : <span class="font-weight-bold"> {{ html_string(optional($activity->activity_price)->currency_symbol) }}{{ optional($activity->activity_price)->hourly }} </span> </p>
										<p ng-show="{{ optional($activity->activity_price)->full_day > 0 }}"> @lang('messages.ready_to_host.full_day_rate') : <span class="font-weight-bold"> {{ html_string(optional($activity->activity_price)->currency_symbol) }}{{ optional($activity->activity_price)->full_day }} </span> </p>
										<p> @lang('messages.space_detail.min_booking_hours') : <span class="font-weight-bold"> {{ optional($activity->activity_price)->min_hours }} </span> </p>
									</div>
								</div>
							</div>
							@endforeach
						</div>
						<h4 class="title mt-4">
							@lang('messages.space_detail.availability')
						</h4>
						<div class="availability_times border">
							@foreach($space_availabilities as $availability)
							@if($availability->status != 'Closed')
							<div class="row m-2">
								<div class="col-md-3">
									{{ $availability->day_name }}
								</div>
								<div class="col-md-6">
									@if($availability->status == 'All')
									@lang('messages.space_detail.all_day')
									@else
									@foreach($availability->availability_times as $avail_time)
									<p> {{ $avail_time->formatted_times }} </p>
									@endforeach
									@endif
								</div>
							</div>
							@endif
							@endforeach
						</div>
					</div>
					<div id="review-info" class="review-section scroll-section">
						@if(!$result->reviews->count())
						<div class="review-content mt-3">
							<div class="panel-body">
								<h5>
									@lang('messages.rooms.no_reviews_yet')
								</h5>
								@if($result->users->reviews->count())
								<p>
									@choice('messages.rooms.review_other_properties', $result->users->reviews->count(), ['count'=>$result->users->reviews->count()])
								</p>
								<a href="{{ route('show_profile',$result->user_id) }}" class="btn btn-secondary mt-2">
									{{ trans('messages.rooms.view_other_reviews') }}
								</a>
								@endif
							</div>
						</div>
						@else
						<div class="review-wrapper">
							<div class="review-count d-flex pt-4 pt-md-5 mt-4 align-items-center">
								<h5>
									{{ $result->reviews->count() }} @choice('messages.header.review',$result->reviews->count())
								</h5>
								<div class="ml-3 star-rating-wrapper">
									{!! $result->overall_star_rating !!}
								</div>
							</div>
							<div class="review-main pt-2 mt-4">
								<div class="review-inner my-3">
									<div class="row">
										<div class="col-lg-3">
											<span class="text-muted">
												@lang('messages.lys.summary')
											</span>
										</div>
										<div class="col-lg-9">
											<div class="row">
												<div class="col-lg-6 summary_details">
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.reviews.accuracy') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->accuracy_star_rating !!}
														</div>
													</div>
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.reviews.communication') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->communication_star_rating !!}
														</div>
													</div>
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.reviews.cleanliness') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->cleanliness_star_rating !!}
														</div>
													</div>
												</div>
												<div class="col-lg-6 summary_details">
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.reviews.location') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->location_star_rating !!}
														</div>
													</div>
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.home.checkin') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->checkin_star_rating !!}
														</div>
													</div>
													<div class="d-flex justify-content-between">
														<strong> @lang('messages.reviews.value') </strong>
														<div class="star-rating-wrapper mt-1">
															{!! $result->value_star_rating !!}
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="review-content">
									<div class="panel-body">
										@foreach($result->reviews as $row_review)
										<div class="d-flex row">
											<div class="col-md-3 col-sm-12 my-2">
												<div class="profile-img d-inline-block text-center">
													<a class="media-photo media-round" href="{{ route('show_profile',[$row_review->user_from]) }}">
														<img title="{{ $row_review->users_from->first_name }}" src="{{ $row_review->users_from->profile_picture->src }}" data-pin-nopin="true" alt="shared.user_profile_image">
														<h5 class="text-truncate">
															{{ $row_review->users_from->first_name }}
														</h5>
													</a>
												</div>
											</div>
											<div class="col-md-9 col-sm-12 my-2">
												<div class="review-text" data-review-id="{{ $row_review->id }}">
													<div class="expandable-content">
														<p class="font-weight-bold">{{ $row_review->comments }}</p>
													</div>
												</div>
												<div class="text-muted review-subtext">
													<span class="date d-inline-block">
														{{ $row_review->date_fy }}
													</span>
												</div>
											</div>
										</div>
										@endforeach
										@if($result->users->reviews->count() - $result->reviews->count())
										<div class="total-reviews mt-3 pt-3">
											<p>
												@choice('messages.rooms.review_other_properties', $result->users->reviews->count() - $result->reviews->count(), ['count'=>$result->users->reviews->count() - $result->reviews->count()])
											</p>
											<a target="blank" class="btn btn-secondary mt-2" href="{{ route('show_profile',$result->user_id) }}">
												<span>
													@lang('messages.rooms.view_other_reviews')
												</span>
											</a>
										</div>
										@endif
									</div>
								</div>
							</div>
						</div>
						@endif
					</div>
					<div id="host-profile" class="host-profile-section scroll-section pt-4 mt-4">
						<h4 class="mb-4">
							{{ trans('messages.rooms.about_host') }}, {{ $result->users->first_name }}
						</h4>
						<div class="row align-items-center">
							<div class="col-12 col-md-3 text-center">
								<a href="{{ route('show_profile',$result->user_id) }}" class="profile-img">
									<img alt="{{ $result->users->first_name }}" data-pin-nopin="true" src="{{ $result->users->profile_picture->src }}" title="{{ $result->users->first_name }}">
								</a>
							</div>
							<div class="col-12 col-md-9 text-center text-md-left mt-3 mt-md-0">
								@if($result->users->live)
								<span>
									{{ $result->users->live }}
								</span>
								@endif
								<span>
									{{ trans('messages.profile.member_since') }}
									{{ $result->users->since }}
								</span>
								@auth
								@if(optional(auth()->user())->id != $result->user_id)
								<div id="contact_wrapper">
									<button id="host-profile-contact-btn" class="btn btn-primary mt-2" data-toggle="modal" data-target="#contact-modal">
										{{ trans('messages.rooms.contact_host') }}
									</button>
								</div>
								@endif
								@endauth
							</div>
						</div>
						<div class="trust-info mt-4 pt-4 d-flex align-items-center">
							<div class="col-md-3 p-0 d-none">
								<label>
									{{ trans('messages.rooms.trust') }}
								</label>
							</div>
							<div class="col-md-9">
								<div class="badge-pill d-inline-block text-center">
									<a rel="nofollow" href="{{ route('show_profile',$result->user_id) }}#reviews">
										<span class="badge-pill-count">
											{{ $result->users->reviews->count() }}
										</span>
										<h5>
											{{ trans_choice('messages.header.review',2) }}
										</h5>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 booking-form position-sticky" ng-init="booking_date_times = {{ json_encode($booking_date_times) }};msg_booking_date_times = {{ json_encode($booking_date_times) }};activities={{ $activities }}">
				{!! Form::open(['url' => route('payment.home',[$space_id]), 'method' => 'POST', 'id' => 'book_it_form', 'novalidate' => 'true']) !!}
				{!! Form::hidden('space_id',$result->id,['id' => 'space_id']) !!}
				{!! Form::hidden('booking_type',($result->booking_type ?? 'request_to_book'),['id' => 'booking_type']) !!}
				{!! Form::hidden('cancellation',$result->cancellation_policy,['id' => 'cancellation']) !!}
				{!! Form::hidden('event_type','',['ng-value' => 'hidden_event_type']) !!}
				{!! Form::hidden('booking_date_times','',['ng-value' => 'hidden_date_times']) !!}
				{!! Form::hidden('booking_period','',['ng-value' => 'booking_period']) !!}
				<h4 class="screen-reader-only">
					{{ trans('messages.rooms.request_to_book') }}
				</h4>
				<div id="pricing" class="price-label d-flex align-items-center" itemprop="offers">
					<div id="price_amount" class="book-it-price-amount">
						<span ng-bind-html="currency_symbol"></span>
						<span id="rooms_price_amount"> @{{ (base_hour_price > 0) ? base_hour_price : default_price }} </span>
						@if($result->booking_type == 'instant_book')
						<span aria-label="Book Instantly">
							<i class="icon icon-instant-book icon-flush-sides tool-amenity1"></i>
							<div class="tooltip-amenity" role="tooltip" data-sticky="true" aria-hidden="true">
								<ul class="panel-body">
									<li>
										<strong>
											Instant Book
										</strong>
									</li>
									<li>
										Book without waiting for the host to respond
									</li>
								</ul>
							</div>
						</span>
						@endif
					</div>
					<div id="payment-period-container" class="book-pay-type ml-auto">
						<span id="per_hour" class="per-hour">
							@lang('messages.space_detail.per_hour')
						</span>
					</div>
				</div>
				<div id="book_it" class="display-subtotal" ng-init="booking_period='{{ $booking_period}}';number_of_guests={{ ($guests > 0 ) ? $guests : '\'\'' }};activity_type_selected='{{ $activity_type_selected }}'">
					<div class="book-it-panel loading">
						<div class="form-group start_date-container">
							<label for="event_type"> @lang('messages.space_detail.event_type') </label>
							<select class="selectpicker form-control event_type" ng-model="activity_type" ng-change="validateDetails();" data-live-search="true" id="SelectAddress">
								<option value="" disabled> @lang('messages.lys.select') </option>
								<optgroup label="@{{ activity.name }}" data-subtext="in @{{ activity.activity_type_name }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length > 0">
									<option data-tokens="@{{activity.name}} @{{ activity.activity_type_name }}" value="@{{subactivity.id}}" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ subactivity.activity_id }}" ng-repeat="subactivity in activity.sub_activities"> @{{ subactivity.name }} </option>
								</optgroup>
								<option value="0" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ activity.id }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length == 0" data-subtext="in @{{ activity.activity_type_name }}" data-price="@{{ activity }}"> @{{ activity.name }}</option>
							</select>
						</div>
						<div class="booking-details">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="list_start_date"> @lang('messages.space_detail.checkin') </label>
									<input type="text" class="form-control start_date" id="list_start_date" ng-model="booking_date_times.start_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly>
								</div>
								<div class="form-group col-md-6" ng-show="booking_period == 'Single'">
								</div>
								<div class="form-group col-md-6">
									<label for="list_start_time"> @lang('messages.space_detail.start_time') </label>
									<select id="list_start_time" class="custom-select form-control" ng-model="booking_date_times.start_time" ng-change="validateDetails();">
										<option value="" disabled> @lang('messages.space_detail.start_time') </option>
										<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-if="key != '23:59:00'" ng-hide="not_available_times[booking_date_times.start_week_day].indexOf(key) >= 0" ng-disabled="blocked_times[booking_date_times.formatted_start_date].indexOf(key) >= 0" ng-checked="booking_date_times.start_time == key"> @{{value}} </option>
									</select>
								</div>
								<div class="form-group col-md-6" ng-hide="booking_period == 'Single'">
									<label for="list_end_date"> @lang('messages.space_detail.checkout') </label>
									<input type="text" class="form-control start_date" id="list_end_date" ng-model="booking_date_times.end_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly>
								</div>
								<div class="form-group col-md-6">
									<label for="list_end_time"> @lang('messages.space_detail.end_time') </label>
									<select id="list_end_time" class="custom-select form-control" ng-model="booking_date_times.end_time" ng-change="validateDetails();">
										<option value="" disabled> @lang('messages.space_detail.end_time') </option>
										<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-hide="not_available_times[booking_date_times.end_week_day].indexOf(key) >= 0 || (booking_period == 'Single' && booking_date_times.start_time >= key)" ng-disabled="blocked_times[booking_date_times.formatted_end_date].indexOf(key) >= 0"> @{{value}} </option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<a href="javascript:;" class="theme-link" ng-click="switchDayType();">
								<span ng-show="booking_period == 'Multiple'"> @lang('messages.space_detail.single_day') </span>
								<span ng-show="booking_period == 'Single'"> @lang('messages.space_detail.mulitple_day') </span>
							</a>
						</div>
						<div class="form-group">
							<label for="num_guests"> @lang('messages.space_detail.guests') </label>
							<input name="number_of_guests" type="number" class="form-control num_guests" id="list_guests" placeholder="@lang('messages.space_detail.guests')" ng-model="number_of_guests" ng-change="validateDetails();" min="0" max="$max_guest_limit">
						</div>
						<div class="panel-body" ng-show='price_calculated'>
							<div class="js-book-it-status" ng-class="is_calculate">
								<div class="js-book-it-enabled clearfix">
									<div class="js-subtotal-container book-it_subtotal panel-padding-fit mt-3" ng-show="not_available_reason == ''">
										<table class="table table-bordered price_table" >
											<tbody>


















												<tr ng-show='total_hour_price1>0'>
													<td class="pos-rel">
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="price_amount"> @{{ hour_amount }} </span>
														<span class="lang-chang-label">  x </span>
														<span id="total_hours_count"> @{{ count_total_hour }} </span>
														<span ng-if='count_total_hour<=1'>
														{{trans('messages.space_detail.hour')}}</span>
														<span ng-if='count_total_hour>1'> {{trans('messages.space_detail.hours')}}</span>
														<i id="service-fee-tooltip" rel="tooltip" class="icon icon-question" title="{{ trans('messages.rooms.avg_night_rate') }}"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="total_hour_price"> @{{ total_hour_price1 }} </span>
													</td>
												</tr>
												<tr ng-show='total_full_day_price>0'>
													<td class="pos-rel">
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="price_amount"> @{{ full_day_amount }} </span>
														<span class="lang-chang-label">  x </span>
														<span id="total_hours_count"> @{{ count_total_days }} </span>
														<span ng-if='count_total_days<=1'>
														{{trans('messages.space_detail.day')}}</span>
														<span ng-if='count_total_days>1'> {{trans('messages.space_detail.days')}}</span>
														<i id="service-fee-tooltip" rel="tooltip" class="icon icon-question" title="{{ trans('messages.rooms.avg_night_rate') }}"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="total_hour_price"> @{{ total_full_day_price }} </span>
													</td>
												</tr>
												<tr ng-show='total_week_price>0'>
													<td class="pos-rel">
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="price_amount"> @{{ weekly_amount }} </span>
														<span class="lang-chang-label">  x </span>
														<span id="total_hours_count"> @{{ count_total_week }} </span>
														<span ng-if='count_total_week<=1'>
														{{trans('messages.space_detail.week')}}</span>
														<span ng-if='count_total_week>1'> {{trans('messages.space_detail.weeks')}}</span>
														<i id="service-fee-tooltip" rel="tooltip" class="icon icon-question" title="{{ trans('messages.rooms.avg_night_rate') }}"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="total_hour_price"> @{{ total_week_price }} </span>
													</td>
												</tr>
												<tr ng-show='total_month_price>0'>
													<td class="pos-rel">
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="price_amount"> @{{ monthly_amount }} </span>
														<span class="lang-chang-label">  x </span>
														<span id="total_hours_count"> @{{ count_total_month }} </span>
														<span ng-if='count_total_month<=1'>
														{{trans('messages.space_detail.month')}}</span>
														<span ng-if='count_total_month>1'> {{trans('messages.space_detail.months')}}</span>
														<i id="service-fee-tooltip" rel="tooltip" class="icon icon-question" title="{{ trans('messages.rooms.avg_night_rate') }}"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="total_hour_price"> @{{ total_month_price }} </span>
													</td>
												</tr>



























												<tr ng-if="service_fee > 0">
													<td>
														@lang('messages.rooms.service_fee')
														<i id="service-fee-tooltip"  rel="tooltip" class="icon icon-question" title="@lang('messages.rooms.24_7_help')"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="service_fee"> @{{ service_fee }} </span>
													</td>
												</tr>
												<tr>
													<td> @lang('messages.rooms.total') </td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="total_price"> @{{ total_price }} </span>
													</td>
												</tr>
												<tr class="security_price" ng-if="security_fee > 0">
													<td>
														{{ trans('messages.rooms.security_fee') }} <i id="service-fee-tooltip" rel="tooltip" class="icon icon-question" title="{{ trans('messages.disputes.security_deposit_will_not_charge') }}"></i>
													</td>
													<td>
														<span class="lang-chang-label" ng-bind-html="currency_symbol"> </span>
														<span id="security_fee"> @{{ security_fee }} </span>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div id="book_it_disabled" class="text-center" ng-show="not_available_reason != ''">
										<p id="book_it_disabled_message" class="icon-rausch book_it_disabled_msg">
											@{{ not_available_reason }}
										</p>
										<a href="{{ route('search_page',['location' => $result->space_address->city]) }}" class="btn btn-large btn-block" id="view_other_listings_button">
											@lang('messages.rooms.view_other_listings')
										</a>
									</div>
									@if($result->user_id != optional(auth())->id())
									<div class="js-book-it-btn-container mt-3" ng-show="not_available_reason == ''" >
										<button type="submit" class="js-book-it-btn btn btn-block btn-primary">
											<span>
												@if($result->booking_type != 'instant_book')
												@lang('messages.rooms.request_to_book')
												@else
												<i class="icon icon-bolt book-instant-icon"></i>
												@lang('messages.lys.instant_book')
												@endif
											</span>
										</button>
									</div>
									<p class="text-muted mt-3 mb-0 text-center" ng-hide="{{($result->user_id == optional(auth()->user())->id)}}">
										<small>
											{{ trans('messages.rooms.review_before_paying') }}
										</small>
									</p>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card wishlist-panel mt-3">
						<div class="card-body">
							@if(Auth::check())
							<div class="wishlist-wrapper">
								<div class="rich-toggle wish_list_button not_saved" data-hosting_id="{{ $result->id }}">
									<input type="checkbox" name="wishlist-button" id="wishlist-button" @if(@$is_wishlist > 0 ) checked @endif >
									<label for="wishlist-button" class="btn btn-block" data-toggle="modal" data-target="#wishlist-modal">
										<span class="rich-toggle-checked">
											<i class="icon icon-heart mr-2"></i>
											Saved to Wish List
										</span>
										<span class="rich-toggle-unchecked">
											<i class="icon icon-heart-alt mr-2"></i>
											{{ trans('messages.wishlist.save_to_wishlist') }}
										</span>
									</label>
								</div>
							</div>
							@endif
							<div class="social-share-widget d-flex align-items-center justify-content-center mt-3 text-center">
								<span class="share-title mr-2">
									{{ trans('messages.rooms.share') }}:
								</span>
								<ul class="share-triggers">
									<li>
										<a class="share-btn link-icon" data-email-share-link="" data-network="email" rel="nofollow" title="{{ trans('messages.login.email') }}" href="mailto:?subject=I love this Space&body=Check out this {{ Request::url() }}">
											<span class="screen-reader-only">
												{{ trans('messages.login.email') }}
											</span>
											<i class="icon icon-envelope social-icon-size"></i>
										</a>
									</li>
									<li>
										<a class="share-btn link-icon" data-network="facebook" rel="nofollow" title="Facebook" href="http://www.facebook.com/sharer.php?u={{ Request::url() }}" target="_blank">
											<span class="screen-reader-only">Facebook</span>
											<i class="icon icon-facebook social-icon-size"></i>
										</a>
									</li>
									<li>
										<a class="share-btn link-icon" data-network="twitter" rel="nofollow" title="Twitter" href="http://twitter.com/intent/tweet?text=Love this! {{ $result->name }} - {{ $result->property_type_name }} for Rent - {{ "@".$site_name}} Travel&url={{ Request::url() }}" target="_blank">
											<span class="screen-reader-only">Twitter</span>
											<i class="icon icon-twitter social-icon-size"></i>
										</a>
									</li>
									<li>
										<a class="share-btn link-icon" data-network="pinterest" rel="nofollow" title="Pinterest" href="http://pinterest.com/pin/create/button/?url={{ Request::url() }}&media={{ $result->photo_name }}&description={{ $result->summary }}" target="_blank">
											<span class="screen-reader-only">Pinterest</span>
											<i class="icon icon-pinterest social-icon-size"></i>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
		<div id="detail-map" class="room-map scroll-section my-4 my-md-5" data-reactid=".2">
			<div id="map" data-lat="{{ $result->space_address->latitude }}" data-lng="{{ $result->space_address->longitude }}"></div>
			<div class="hover-card text-center d-none d-md-block">
				<h4>
					{{ trans('messages.rooms.listing_location') }}
				</h4>
				<a href="">
					<span>{{$result->space_address->state}},</span>
				</a>
				<a href="">
					<span>{{$result->space_address->country_name}}</span>
				</a>
			</div>
		</div>
		@if($similar->count())
		<div class="similar-listings my-4 my-md-5">
			<h4 class="title-sm mb-3">
				{{ trans('messages.rooms.similar_listings') }}
			</h4>
			<div id="similar-slider" class="owl-carousel" item-length="{{$similar->count()}}">
				@foreach($similar as $space)
				<div class="listing list_view">
					<div class="pro-img">
						<a href="{{ $space->link }}" target="listing_{{$space->id}}" class="media-photo media-cover">
							<div class="listing-img-container media-cover text-center">
								<img id="space_image_{{$space->id}}" ng-src="{{ $space->photo_name }}" class="img-responsive-height" alt="{{ $space->name }}">
							</div>
						</a>
					</div>
					<div class="pro-info">
						<h4 class="text-truncate">
							<span>{{ $space->space_type_name }}</span>
							<span>·</span>
							<span>{{ $space->number_of_guests }} @choice('messages.home.guest',$space->number_of_guests) </span>
						</h4>
						<a href="{{$space->link}}" target="listing_{{$space->id}}">
							<h5 class="text-truncate">
								{{$space->name}}
							</h5>
						</a>
						<p class="price">
							{{ $currency_symbol }} {{ @$space->activity_price->hourly }}
							@lang("messages.space_detail.per_hour")
							@if($space->booking_type == 'instant_book')
							<span>
								<i class="icon icon-instant-book">
								</i>
							</span>
							@endif
						</p>
						<div class="d-flex align-items-center">
							@if($space->overall_star_rating)
							{!!$space->overall_star_rating!!}
							@endif
							@if($space->reviews_count)
							<span class="review-count mx-2">
								· {{$space->reviews_count}}
							</span>
							<span class="review-label">
								{{ trans_choice('messages.header.review', $space->reviews_count) }}
							</span>
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		@endif
	</div>
</div>
<!--Contact Host Modal -->
<div class="modal fade" id="contact-modal" ng-cloak>
	<div class="modal-dialog" role="document">
		<div class="modal-content" ng-class="(contact_loading == 1) ? 'loading': ''">
			<div class="modal-header border-0 p-0">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body d-md-flex p-0">
				<div class="host-info col-md-4">
					<div class="profile-img mb-4">
						<a href="{{ route('show_profile',[$result->user_id]) }}" class="media-photo media-round">
							<img alt="shared.user_profile_image" data-pin-nopin="true" src="{{ $result->users->profile_picture->src }}" title="{{ $result->users->first_name }}">
						</a>
					</div>
					<h5>
						@lang('messages.rooms.send_a_message',['first_name'=>$result->users->first_name])
					</h5>
					<p>
						@lang('messages.rooms.share_following'):
					</p>
					<ul>
						<li>
							<span>
								@lang('messages.rooms.tell_about_yourself',['first_name'=>$result->users->first_name])
							</span>
						</li>
						<li>
							<span>
								@lang('messages.rooms.what_brings_you',['city'=>$result->space_address->city])?
							</span>
						</li>
						<li>
							<span>
								@lang('messages.rooms.love_about_listing')!
							</span>
						</li>
					</ul>
				</div>
				<div class="compose-info col-md-8">
					{!! Form::open(['url' => route('contact_request',['id' => $space_id,'src_url' => $result->link]), 'method' => 'POST', 'id' => 'message_form', 'class' => 'contact-host-panel m-0']) !!}
					{!! Form::hidden('event_type','',['ng-value' => 'msg_hidden_event_type']) !!}
					{!! Form::hidden('booking_date_times','',['ng-value' => 'msg_hidden_date_times']) !!}
					{!! Form::hidden('booking_period','',['ng-value' => 'booking_period']) !!}
					<h5>
						{{ trans('messages.rooms.when_you_traveling') }}?
					</h5>
					<div class="mt-3 clearfix">
						<div class="form-group start_date-container">
							<label for="event_type"> @lang('messages.space_detail.event_type') </label>
							<select class="selectpicker form-control msg_event_type" ng-model="msg_activity_type" ng-change="validateContactDetails();" data-live-search="true">
								<option value="" disabled> @lang('messages.lys.select') </option>
								<optgroup label="@{{ activity.name }}" data-subtext="in @{{ activity.activity_type_name }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length > 0">
									<option data-tokens="@{{activity.name}} @{{ activity.activity_type_name }}" value="@{{subactivity.id}}" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ subactivity.activity_id }}" ng-repeat="subactivity in activity.sub_activities"> @{{ subactivity.name }} </option>
								</optgroup>
								<option value="0" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ activity.id }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length == 0" data-subtext="in @{{ activity.activity_type_name }}"> @{{ activity.name }}</option>
							</select>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label for="msg_start_date"> @lang('messages.space_detail.checkin') </label>
								<input type="text" class="form-control start_date" id="msg_start_date" ng-model="msg_booking_date_times.start_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly>
							</div>
							<div class="form-group col-md-6" ng-show="booking_period == 'Single'">
							</div>
							<div class="form-group col-md-6">
								<label for="msg_start_time"> @lang('messages.space_detail.start_time') </label>
								<select id="msg_start_time" class="custom-select form-control" ng-model="msg_booking_date_times.start_time" ng-change="startTimeChanged();validateContactDetails();">
									<option value="" disabled> @lang('messages.space_detail.start_time') </option>
									<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-if="key != '23:59:00'" ng-hide="not_available_times[msg_booking_date_times.start_week_day].indexOf(key) >= 0" ng-disabled="blocked_times[msg_booking_date_times.formatted_start_date].indexOf(key) >= 0" ng-checked="msg_booking_date_times.start_time == key"> @{{value}} </option>
								</select>
							</div>
							<div class="form-group col-md-6" ng-hide="booking_period == 'Single'">
								<label for="msg_end_date"> @lang('messages.space_detail.checkout') </label>
								<input id="msg_end_date" type="text" class="form-control start_date" ng-model="msg_booking_date_times.end_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly>
							</div>
							<div class="form-group col-md-6">
								<label for="msg_end_time"> @lang('messages.space_detail.end_time') </label>
								<select id="msg_end_time" class="custom-select form-control" ng-model="msg_booking_date_times.end_time" ng-change="validateContactDetails();">
									<option value="" disabled> @lang('messages.space_detail.end_time') </option>
									<option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-hide="not_available_times[msg_booking_date_times.end_week_day].indexOf(key) >= 0 || (booking_period == 'Single' && msg_booking_date_times.start_time >= key)" ng-disabled="blocked_times[msg_booking_date_times.formatted_end_date].indexOf(key) >= 0"> @{{value}} </option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<a href="javascript:;" class="theme-link" ng-click="switchDayType();">
								<span ng-show="booking_period == 'Multiple'"> @lang('messages.space_detail.single_day') </span>
								<span ng-show="booking_period == 'Single'"> @lang('messages.space_detail.mulitple_day') </span>
							</a>
						</div>
						<div class="form-group">
							<label for="num_guests"> @lang('messages.space_detail.guests') </label>
							<input name="number_of_guests" type="number" class="form-control num_guests" id="msg_guests" placeholder="@lang('messages.space_detail.guests')" ng-model="msg_number_of_guests" ng-change="validateContactDetails();" min="0" max="$max_guest_limit">
						</div>
					</div>
					<div class="message-panel tooltip-fixed tooltip-bottom-left my-3">
						<div class="panel-body">
							<textarea class="focus-on-active" name="question" placeholder="{{ trans('messages.rooms.start_your_msg') }}..." ng-model="question"></textarea>
						</div>
					</div>
					<span class="text-danger mt-1" ng-show='contact_error'>
						@lang('messages.your_trips.please_fill_the_details')
					</span>
					<div class="send-user mt-4 d-flex align-items-center justify-content-between">
						<div class="profile-img">
							<a href="{{ route('show_profile',['id' => optional(auth()->user())->id]) }}" class="media-photo media-round">
								<img alt="user_profile_image" data-pin-nopin="true" src="{{ (Auth::check()) ? Auth::user()->profile_picture->src : '' }}" title="{{ (Auth::check()) ? Auth::user()->first_name : '' }}">
							</a>
						</div>
						<button type="button" id="contact_message_send" class="btn btn-primary" ng-click="sendContactRequest($event);">
							@lang('messages.your_reservations.send_message')
						</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
<!--Wishlist Modal -->
<div class="wishlist-popup modal fade" id="wishlist-modal" tabindex="-1" role="dialog" aria-labelledby="Wishlist-ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header border-0 p-0">
				<button type="button" class="close wl-modal-close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body p-0">
				<div class="d-md-flex">
					<div class="col-12 col-md-7 background-listing-img d-flex" style="background-image:url({{ $result->photo_name }});">
						<div class="mt-auto mb-3 d-flex align-items-center">
							<div class="profile-img mr-3">
								<img src="{{ $result->users->profile_picture->src }}">
							</div>
							<div class="profile-info">
								<h4>
									{{ $result->name }}
								</h4>
								<span>
									{{ $result->space_address->city }}
								</span>
							</div>
						</div>
					</div>
					<div class="add-wishlist d-flex flex-column col-12 col-md-5">
						<div class="wish-title pt-5 pb-3">
							<h3>
								{{ trans('messages.wishlist.save_to_wishlist') }}
							</h3>
						</div>
						<div class="wl-modal-wishlists d-flex flex-grow-1 flex-column">
							<ul class="mb-auto">
								<li class="d-flex align-items-center justify-content-between" ng-repeat="item in wishlist_list" ng-class="(item.saved_id) ? 'active' : ''" ng-click="wishlist_row_select($index)" id="wishlist_row_@{{ $index }}">
									<span class="d-inline-block text-truncate">@{{ item.name }}</span>
									<div class="wl-icons ml-2">
										<i class="icon icon-heart-alt icon-light-gray wl-modal-wishlist-row__icon-heart-alt" ng-hide="item.saved_id"></i>
										<i class="icon icon-heart icon-rausch wl-modal-wishlist-row__icon-heart" ng-show="item.saved_id"></i>
									</div>
								</li>
							</ul>
							<div class="wl-modal-footer my-3 pt-3">
								<form class="wl-modal-form d-none">
									<div class="d-flex align-items-center">
										<input type="text" class="wl-modal-input flex-grow-1 border-0" autocomplete="off" id="wish_list_text" value="{{ $result->space_address->city }}" placeholder="Name Your Wish List" required>
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
	$(document).ready(function() {
		function booking_form() {
			var header_height = $("header").outerHeight();
			var display_subtotal = $('.display-subtotal').width();
			var pricing = $('.price-label').outerHeight();

			
			var detail_banner = $('.detail-banner').position().top + $('.detail-banner').outerHeight();
			$('.booking-form').css({"top": header_height + "px"});
			$('.price-label').css({"width": display_subtotal + "px"});

			if ($(window).scrollTop() >= (detail_banner - header_height)) {
				$('.booking-form').addClass('active');
				$('.detail-sticky').addClass('active');
				$('.display-subtotal').css({"margin-top": pricing + "px"});
				$('.price-label').css({"top": header_height + "px"});
				// 
			}
			else {
				$('.booking-form').removeClass('active');
				$('.detail-sticky').removeClass('active');
				$('.display-subtotal').css({"margin-top": 0 + "px"});
        		// $('.booking-form').css('z-index', '9');

        	}
			// $(window).on('scroll', function() {
			//     $('#pricing').each(function() {
			//        if ($(window).scrollTop() >= (detail_banner - header_height)) {
			//             $('.booking-form').css('z-index', '9');
			//         }
			//     });
			// });

			// pricing
		}
		booking_form();
		$(window).scroll(function() {
			booking_form();
		});
		$(window).resize(function() {
			booking_form();
		});
	});
</script>
@endpush