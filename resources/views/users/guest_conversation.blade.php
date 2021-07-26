@extends('template') 
@section('main')
<main id="site-content" role="main" ng-controller="conversation" class="notranslate">
    @include('common.subheader')  
    <div class="guest-conversation my-4 my-md-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-7 messaging-thread-main order-md-2" ng-init="reservation_id='{{ @$messages[0]->reservation_id }}'">
                    <input type="hidden" value="{{ @$messages[0]->reservation_id }}" id="reservation_id">
                    @if(@$messages[0]->message_type == 1)
                    <div class="banner-status mb-3 text-center">
                        <h4>
                            {{ trans('messages.payments.request_sent') }}
                        </h4>
                        <p>
                            {{ trans('messages.inbox.reservation_isnot_confirmed') }}
                        </p>
                    </div>
                    @endif
                    @if(@$messages[0]->message_type == 2)
                    <div class="banner-status mb-3 text-center">
                        <h4>
                            @lang('messages.inbox.reservation_confirmed')
                        </h4>
                        <a href="{{ url('/') }}/reservation/itinerary?code={{ @$messages[0]->reservation->code }}" class="btn mt-2">
                            <span>
                                {{ trans('messages.your_trips.view_itinerary') }}
                            </span>
                        </a>
                    </div>
                    @endif
                    @if(@$messages[0]->message_type == 3 || @$messages[0]->message_type == 8)
                    <div class="banner-status mb-3 text-center">
                        <h4>
                            {{ trans('messages.inbox.request_declined') }}
                        </h4>
                        <p>
                            {{ trans('messages.inbox.more_places_available') }}
                        </p>
                        <a class="btn mt-2" href="{{ route('search_page',['location' => @$messages[0]->reservation->space->space_address->city]) }}">
                            <span>
                                {{ trans('messages.inbox.keep_searching') }}
                            </span>
                        </a>
                    </div>
                    @endif
                    @if(@$messages[0]->reservation->special_offer)
                    <div class="card action-status mb-3">
                        <div class="card-body text-center">
                            <h5>
                                {{ ucfirst(@$messages[0]->reservation->space->users->first_name) }} @lang('messages.inbox.pre_approved_trip')
                            </h5>
                            <div class="my-3">
                                @if(@$messages[0]->message_type != 8 )
                                    @if(@$messages[0]->reservation->avablity == 0 || @$messages[0]->reservation->special_offer->avablity == 0)
                                        @if(@$messages[0]->reservation->special_offer->checkin >= date("Y-m-d"))       
                                            @if(@$messages[0]->reservation->special_offer->is_booked)
                                            <a href="{{ $messages[0]->reservation->special_offer->booking_link }}" class="btn btn-primary">
                                                <span>
                                                    @lang('messages.inbox.book_now')
                                                </span>
                                            </a>
                                            @endif
                                            @if(@$messages[0]->reservation->special_offer->id != '' && $messages[0]->reservation->special_offer->type == 'special_offer')
                                            <div class="my-3">
                                                <div class="special-offer-info">
                                                    <div class="my-1">
                                                        <span>
                                                            @lang('messages.lys.listing_name')
                                                        </span>
                                                    </div>                                       
                                                    <a class="theme-color" href="{{$messages[0]->reservation->special_offer->space->link }}">
                                                        {{ $messages[0]->reservation->special_offer->space->name }}
                                                    </a>
                                                </div>
                                                <div class="special-offer-info">
                                                    <div class="my-1">
                                                        <span>
                                                            @lang('messages.your_reservations.checkin')
                                                        </span>
                                                    </div>
                                                    <h5>
                                                        {{@$messages[0]->reservation->special_offer->checkin_with_time}} 
                                                    </h5>
                                                </div>
                                                <div class="special-offer-info">
                                                    <div class="my-1">
                                                        <span>
                                                            @lang('messages.your_reservations.checkout')
                                                        </span>
                                                    </div>
                                                    <h5> 
                                                        {{@$messages[0]->reservation->special_offer->checkout_with_time}} 
                                                    </h5>
                                                </div>
                                                <div class="special-offer-info">
                                                    <div class="my-1">
                                                        <span>
                                                            @choice('messages.home.guest',@$messages[0]->reservation->special_offer->number_of_guests )
                                                        </span>
                                                    </div>
                                                    <h5> 
                                                        {{@$messages[0]->reservation->special_offer->number_of_guests }} 
                                                    </h5>
                                                </div>
                                                <div class="reservation-info-section">
                                                    <div class="my-1">
                                                        <span> 
                                                            @lang('messages.inbox.special_offer')
                                                        </span>
                                                    </div>
                                                    <h5>
                                                        {{ html_string($messages[0]->reservation->currency->symbol)}}{{@$messages[0]->reservation->special_offer->price }} 
                                                    </h5>
                                                </div>
                                            </div>
                                            @endif
                                        @else
                                            <span class="label label-info">
                                                @lang('messages.dashboard.Expired')
                                            </span>
                                        @endif
                                    @else
                                        @if($messages[0]->reservation->special_offer->checkin >= date("Y-m-d"))
                                            @if(@$messages[0]->reservation->special_offer->is_booked)
                                            <span class="error-msg" id="al_res{{ $messages[0]->reservation->id }}">
                                                {{ trans('messages.inbox.already_booked') }}
                                            </span> 
                                            @endif
                                        @else
                                            <span class="label label-info">
                                                {{trans('messages.dashboard.Expired')}}
                                            </span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif(@$messages[0]->reservation->status=='Pre-Accepted')
                    <div class="card action-status mb-3">
                        <div class="card-body text-center">
                            <h5>
                                {{ ucfirst(@$messages[0]->reservation->space->users->first_name) }} {{ trans('messages.inbox.preaccept_booking') }}
                            </h5>
                            <div class="mt-3">
                                @if(@$messages[0]->message_type != 8)
                                    @if(@$messages[0]->reservation->avablity == 0)
                                        @if(@$messages[0]->reservation->checkin >= date("Y-m-d"))
                                        <a href="{{ route('payment.home',['id' => @$messages[0]->reservation->space_id,'reservation_id' => @$messages[0]->reservation->id]) }}" class="btn btn-primary text-nowrap">
                                            <p hidden="hidden" class="pending_id"> {{ @$messages[0]->reservation->id }}
                                            </p>
                                            <span>
                                                @lang('messages.inbox.book_now')
                                            </span>
                                        </a>
                                        @else
                                        <span class="label label-info">
                                            {{trans('messages.dashboard.Expired')}}
                                        </span>
                                        @endif
                                    @else
                                        @if(@$messages[0]->reservation->checkin >= date("Y-m-d"))
                                        <span class="error-msg" id="al_res{{ @$messages[0]->reservation->id }}">
                                            {{ trans('messages.inbox.already_booked') }}
                                        </span>
                                        @else
                                        <span class="label label-info">
                                            {{trans('messages.dashboard.Expired')}}
                                        </span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div id="post_message_box" data-key="guest_conversation" class="row mb-3 send-message-box">
                        <div class="col-10">
                            <div class="card">
                                <div class="cars-body">
                                    <textarea rows="3" placeholder="" class="send-message-textarea border-0" id="message_text" name="message"></textarea>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary" id="reply_message" ng-click="reply_message('guest_conversation')">
                                        {{ trans('messages.your_reservations.send_message') }}
                                    </button>
                                </div>
                            </div>
                            <div class="page_translate" onclick="google_translate()"><a href=''>{{trans('messages.your_reservations.google_translate')}}</a></div>
                        </div>
                        <div class="col-2 profile-img pl-0">
                            <img class="img-fluid w-100" src="{{ Auth::user()->profile_picture->src }}">
                        </div>
                    </div>
                    <div id="thread-list" class="translate">
                        @for($i=0; $i < count($messages); $i++)
                            @if($messages[$i]->message_type=='12')
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.preaccept_booking') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 9)
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.contact_request_sent') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 2)
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.reservation_confirmed') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 3)
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.reservation_declined') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 4)
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.reservation_expired') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 6)
                            <div class="inline-status py-4">
                                <span>
                                    {{ $messages[$i]->reservation->space->users->first_name }} {{ trans('messages.inbox.pre_approved_you') }} 
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 7)
                            <div class="inline-status py-4">
                                <span>
                                    {{ $messages[$i]->reservation->space->users->first_name }} {{ trans('messages.inbox.sent_special_offer') }} 
                                </span>
                                <span>
                                    ( {{ html_string($messages[$i]->special_offer->currency->symbol) }}{{ $messages[$i]->special_offer->price }} / {{ $messages[$i]->special_offer->special_offer_times->diff_hours }} @choice('messages.booking.hour',2))
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->message_type == 11 || $messages[$i]->message_type == 8)
                            <div class="inline-status py-4">
                                <span>
                                    {{ trans('messages.inbox.reservation_declined') }}
                                </span>
                                <span>
                                    {{ $messages[$i]->created_time }}
                                </span>
                            </div>
                            @endif

                            @if($messages[$i]->user_from != Auth::user()->id && $messages[$i]->message != '')
                            <div class="row my-3">
                                <div class="col-2 profile-img pr-0">
                                    <img src="{{ $messages[$i]->user_details->profile_picture->src }}">
                                </div>
                                <div class="col-10">
                                    <div class="card">
                                        <div class="card-body custom-arrow left">
                                            <a data-prevent-default="true" title="Report this message" class="flag-trigger" href="#"></a>
                                            <span class="message-text">
                                                {{ $messages[$i]->message }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="my-2 time text-right">
                                        <span>
                                            {{ $messages[$i]->created_time }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($messages[$i]->user_from == Auth::user()->id)
                            <div class="row my-4">
                                <div class="col-10">
                                    <div class="card">
                                        <div class="card-body custom-arrow right">
                                            <span class="message-text">
                                                {{ $messages[$i]->message }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="my-2 time text-right">
                                        <span>
                                            {{ $messages[$i]->created_time }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-2 pl-0 profile-img">
                                    <img src="{{ Auth::user()->profile_picture->src }}" class="user-profile-photo">
                                </div>
                            </div>
                            @endif
                        @endfor
                    </div>
                </div>

                <div class="col-12 col-md-5 mt-4 mt-md-0 order-md-1">
                    <form accept-charset="UTF-8" action="{{ url('/') }}/messaging/qt_reply/{{ @$messages[0]->reservation_id }}" method="post">
                        <div class="mini-profile card p-4">
                            <div class="profile-image text-center">
                                <a href="{{ route('show_profile',[@$messages[0]->reservation->space->users->id]) }}">
                                    <img src="{{ @$messages[0]->reservation->space->users->profile_picture->src }}" alt="{{ @$messages[0]->reservation->space->users->first_name }}">
                                </a>
                            </div>

                            <div class="mt-3 text-center">
                                <a class="theme-link" href="{{ route('show_profile',[@$messages[0]->reservation->space->users->id]) }}">
                                    {{ @$messages[0]->reservation->space->users->first_name }}
                                </a>
                                <div class="mt-1">
                                    {{ @$messages[0]->reservation->space->users->live }}
                                </div>
                            </div>

                            @if(@$messages[0]->reservation->space->users->about)
                            <div class="mt-1 text-center">
                                <div class="expandable expandable-trigger-more expanded">
                                    <div class="expandable-content">
                                        <p>
                                            {{ @$messages[0]->reservation->space->users->about }}
                                        </p>
                                        <div class="expandable-indicator expandable-indicator-light"></div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(@$messages[0]->reservation->status == 'Accepted')
                            <div class="mt-3 pt-3 border-top text-left">
                                <h5>
                                    {{ trans('messages.login.email') }}
                                </h5>
                                <span>
                                    {{ @$messages[0]->reservation->space->users->email }}
                                </span>
                            </div>
                            @endif

                            @if(@$messages[0]->reservation->status == 'Accepted' && @$messages[0]->reservation->host_users->primary_phone_number != '' )
                            <div class="mt-3 text-left">
                                <div class="text-medium-gray">
                                    {{ trans('messages.profile.phone_number') }}
                                </div>
                                <div class="mt-1">
                                    {{ @$messages[0]->reservation->host_users->primary_phone_number }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mt-5 mb-4 pb-4 border-bottom reservation-info">
                            <a class="normal-link" href="{{@$messages[0]->reservation->space->link }}">
                                <h4>
                                    {{ @$messages[0]->reservation->space->name }}
                                </h4>
                            </a>

                            <div class="reservation-info-section d-flex mt-3 row flex-wrap">
                                <div class="col-12 col-md-6 p-0 d-flex align-items-center d-md-block">
                                    <div class="col-6 col-md-12">
                                        <span>
                                            {{ trans('messages.your_reservations.checkin') }}
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-12">
                                        <h5>
                                            {{@$messages[0]->reservation->checkin_with_time}}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 p-0 d-flex align-items-center d-md-block">
                                    <div class="col-6 col-md-12">
                                        <span>
                                            {{ trans('messages.your_reservations.checkout') }}
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-12">
                                        <h5>
                                            {{ @$messages[0]->reservation->checkout_with_time}}
                                        </h5>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 mt-2 p-0 d-flex align-items-center d-md-block">
                                    <div class="col-6 col-md-12">
                                        <span>
                                            @lang('messages.space_detail.event_type')
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-12">
                                        <h5>
                                            {{ @$messages[0]->reservation->event_type_name }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mt-2 p-0 d-flex align-items-center d-md-block">
                                    <div class="col-6 col-md-12">
                                        <span>
                                            {{ trans_choice('messages.home.guest',@$messages[0]->reservation->number_of_guests ) }}
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-12">
                                        <h5>
                                            {{ @$messages[0]->reservation->number_of_guests }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="guest-payment-info my-4">
                            <h4>
                                @lang('messages.payments.payment')
                            </h4>
                            <div class="mt-4">
                                @if(@$messages[0]->reservation->hours>0)
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_hour }}
                                        </span> 
                                        <span> 
                                            x {{ @$messages[0]->reservation->hours }}
                                            @if(@$messages[0]->reservation->hours<=1) 
                                            {{trans('messages.space_detail.hour')}}
                                            @else
                                            {{trans('messages.space_detail.hours')}}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>         
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_hour*@$messages[0]->reservation->hours }}
                                        
                                        </span>
                                    </div>
                                </div>
                                @endif
                                 @if(@$messages[0]->reservation->days>0)
                                 <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_day }}
                                        </span> 
                                        <span> 
                                            x {{ @$messages[0]->reservation->days }}
                                         @if(@$messages[0]->reservation->hours<=1) 
                                            {{trans('messages.space_detail.day')}}
                                            @else
                                            {{trans('messages.space_detail.days')}}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>         
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_day*@$messages[0]->reservation->days }}
                                        
                                        </span>
                                    </div>
                                </div>
                                @endif
                                 @if(@$messages[0]->reservation->weeks>0)
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_week }}
                                        </span> 
                                        <span> 
                                            x {{ @$messages[0]->reservation->weeks }}
                                         @if(@$messages[0]->reservation->weeks<=1) 
                                            {{trans('messages.space_detail.week')}}
                                            @else
                                            {{trans('messages.space_detail.weeks')}}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>         
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_week*@$messages[0]->reservation->weeks }}
                                        
                                        </span>
                                    </div>
                                </div>
                                @endif
                                @if(@$messages[0]->reservation->months>0)
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_month }}
                                        </span> 
                                        <span> 
                                            x {{ @$messages[0]->reservation->months }}
                                         @if(@$messages[0]->reservation->months<=1) 
                                            {{trans('messages.space_detail.month')}}
                                            @else
                                            {{trans('messages.space_detail.months')}}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>         
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->per_month*@$messages[0]->reservation->months }}
                                        
                                        </span>
                                    </div>
                                </div>
                                @endif
                                
                                @if(@$messages[0]->reservation->cleaning != 0 )
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>{{ trans('messages.rooms.cleaning_fee') }}</span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->cleaning }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                @if(@$messages[0]->reservation->service != 0)
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            {{ trans('messages.rooms.service_fee') }}
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>
                                            {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->service }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                @if(@$messages[0]->reservation->coupon_amount != 0)
                                <div class="d-flex my-2 row">
                                    <div class="col-8 text-left">
                                        <span>
                                            @if(@$messages[0]->reservation->coupon_code == 'Travel_Credit')
                                            {{ trans('messages.referrals.travel_credit') }}
                                            @else
                                            {{ trans('messages.payments.coupon_amount') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>
                                            -{{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->coupon_amount }}
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <div class="d-flex mt-3 pt-3 border-top row">
                                    <div class="col-8 text-left">
                                        <span class="font-weight-bold">
                                            @lang('messages.rooms.total')
                                        </span>
                                    </div>
                                    <div class="col-4 text-right">
                                        <strong>
                                            <span>
                                                {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->total }}
                                            </span>
                                        </strong>
                                    </div>
                                </div>
                                @if(@$messages[0]->reservation->security > 0)
                                    <div class="d-flex my-2 row">
                                        <div class="col-8 text-left">
                                            <span>
                                                @lang('messages.rooms.security_fee')
                                            </span>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>
                                                {{ html_string(@$messages[0]->reservation->currency->symbol) }}{{ @$messages[0]->reservation->security }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="my-4">
                                <span>
                                    {{ trans('messages.inbox.protect_your_payments') }}
                                </span>
                                <span>
                                    {{ trans('messages.inbox.never_pay_outside',['site_name'=>$site_name]) }}
                                </span>
                                <span class="custom-tooltip d-block d-md-inline-block">
                                    <i class="icon icon-question tns-payment-tooltip-trigger tool-amenity2"></i>
                                    <div class="tooltip-wrap tooltip-amenity2 mt-3" data-sticky="true" aria-hidden="true">
                                        <div class="tooltip-info custom-arrow top">
                                        <span>
                                                {{ trans('messages.inbox.never_pay_outside',['site_name'=>$site_name]) }}
                                                </span>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<div id='google_translate_element' style="display: none;"></div>
<input type="hidden" id="page_lang" value="{{ $page_language }}">
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script type="text/javascript">
function googleTranslateElementInit() {
new google.translate.TranslateElement('google_translate_element');
}
function google_translate() {
  var language = $('#page_lang').val();
  console.log(language);
  var selectField = document.querySelector("#google_translate_element select");
  for(var i=0; i < selectField.children.length; i++){
    var option = selectField.children[i];
    // find desired langauge and change the former language of the hidden selection-field 
    if(option.value==language){
       selectField.selectedIndex = i;
       // trigger change event afterwards to make google-lib translate this side
       selectField.dispatchEvent(new Event('change'));
       break;
    }
  }
}

</script>
@stop
