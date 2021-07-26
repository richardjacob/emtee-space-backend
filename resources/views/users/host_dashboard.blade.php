﻿@extends('template')
@section('main')   
<main id="site-content" role="main">
  @include('common.subheader')
  <!-- <div class="host-dashboard" id="host-dashboard-container">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-7 col-lg-8 d-md-flex align-items-center dashboard-left py-4 text-center text-md-left">
          <div class="col-md-3 col-lg-2 dashboard-profile p-0 mb-3 mb-md-0">
            <a href="{{ route('show_profile',[$user->id]) }}" data-tracking="{&quot;section&quot;:&quot;header_profile_photo&quot;}">
              <img src="{{$user->profile_picture->src}}">
            </a>
          </div>
          <div class="col-md-9 col-lg-10 dashboard-content">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <strong>
                    {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                  </strong>                           
                  {{ trans('messages.host_dashboard.title') }}
                </div>
                <div class="carousel-item">
                  <strong>
                    {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                  </strong>
                  {{trans('messages.host_dashboard.welcome_message')}}
                </div>
                <div class="carousel-item">
                  <strong>
                    {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                  </strong>                         
                  {{ trans('messages.host_dashboard.title') }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-5 col-lg-4 dashboard-right p-4">
          <div class="text-center">
            <h2>
              <sup>
                {{ $currency_symbol }}
              </sup>
              <strong>
                {{ $completed_payout  + $future_payouts }}
              </strong>
            </h2>
            <p>
              {{trans('messages.host_dashboard.for_nights_in_month', ['count' => ($completed_nights  +  $future_nights), 'month' => trans('messages.lys.'.date('F')) ])}}
            </p>
          </div>
          <div class="table-responsive">          
            <table class="table borderless">
              <thead>
                <tr>
                  <th class="text-center border-0" colspan="2">
                    {{trans('messages.lys.'.date('F'))}} {{ trans('messages.host_dashboard.breakdown') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left">
                    {{ trans('messages.host_dashboard.already_paid_out') }}
                  </td>
                  <td class="text-right">
                    <strong>
                      <sup>
                        {{ $currency_symbol }}
                      </sup>
                      {{ $completed_payout }}
                    </strong>
                  </td>
                </tr>
                <tr>
                  <td class="text-left">
                    {{ trans('messages.host_dashboard.expected_earnings') }}
                  </td>
                  <td class="text-right">
                    <strong>
                      <sup>
                        {{$currency_symbol}}
                      </sup>
                      {{ $future_payouts }}
                    </strong>
                  </td>
                </tr>
                <tr class="total">
                  <td class="text-left">
                    {{ trans('messages.rooms.total') }}
                    <sup>
                      <i class="fa fa-question-circle" title="{{trans('messages.host_dashboard.total_details')}}" rel="tooltip"></i>
                    </sup>
                  </td>
                  <td class="text-right"><strong><sup>{{$currency_symbol}}</sup>{{ $completed_payout  + $future_payouts}} </strong></td>
                </tr>
                <tr class="total_paid">
                  <td class="text-left"> {{ trans('messages.host_dashboard.total_paid_out_in') }} {{date('Y')}}</td>
                  <td class="text-right"><strong><sup>{{$currency_symbol}}</sup>{{ $total_payout}}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="transaction_history">
            <a href="{{ url('users/transaction_history') }}" class="btn w-100">
              {{ trans('messages.host_dashboard.transaction_history') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <div class="cls_dashboard pt-5 pb-5">
    <div class="container">
      <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-12  ">
            <div class="dash_mnttitle mb-3 p-2 d-flex flex-lg-row text-center  flex-column align-items-between justify-content-between shadow-sm">
              <span>{{trans('messages.lys.'.date('F'))}} {{ trans('messages.host_dashboard.breakdown') }} <strong>
                {{ $currency_symbol }}{{ $completed_payout  + $future_payouts }}
              </strong></span>
              <div>
              <a href="{{ url('users/transaction_history') }}" class="btn border border-primary btn-sm">{{ trans('messages.host_dashboard.transaction_history') }}</a></div>
            </div>
            <div class="d-flex flex-row mb-3 align-items-center cls_dash shadow-sm">
                <div class="p-2 cls_dash_img ">
                  <h1><img src="{{ asset('images/amenities/alredypayout.png') }}" class="img-fluid">
                 </h1>
                </div>
                <div class="p-2 cls_dash_txt flex-grow-1 ">
                  <h2>{{ trans('messages.host_dashboard.already_paid_out') }}</h2>
                  <h5>{{ $currency_symbol }}<span class="cls_count">{{ $completed_payout }}</span></h5>
                </div>
            </div>
            <div class="d-flex flex-row mb-3 align-items-center cls_dash shadow-sm">
                <div class="p-2 cls_dash_img ">
                  <h1><img src="{{ asset('images/amenities/expectearn.png') }}" class="img-fluid">
                 </h1>
                </div>
                <div class="p-2 cls_dash_txt flex-grow-1 ">
                  <h2>{{ trans('messages.host_dashboard.expected_earnings') }}</h2>
                  <h5>{{ $currency_symbol }}<span class="cls_count">{{ $future_payouts }}</span></h5>
                </div>
            </div>
            <div class="d-flex flex-row mb-3 align-items-center cls_dash shadow-sm">
                <div class="p-2 cls_dash_img ">
                  <h1><img src="{{ asset('images/amenities/totalpaidyear.png') }}" class="img-fluid">
                 </h1>
                </div>
                <div class="p-2 cls_dash_txt flex-grow-1 ">
                  <h2>{{ trans('messages.host_dashboard.total_paid_out_in') }} {{date('Y')}}</h2>
                  <h5>{{ $currency_symbol }}<span class="cls_count">{{ $total_payout}}</span></h5>
                </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12 ">
            <div class="d-flex flex-lg-row flex-sm-column flex-column mb-3 align-items-center cls_dashprofile">
              <div class="p-2 cls_dashprofile_img ">
                <a href="{{ route('show_profile',[$user->id]) }}" data-tracking="{&quot;section&quot;:&quot;header_profile_photo&quot;}">
                <img src="{{$user->profile_picture->src}}">
                </a>
              </div>
              <div class="p-2 pl-3 cls_dashprofile_text text-center text-lg-left">
                <h4>Welcome Back</h4>
                <h3>{{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}</h3>
                <div class="mt-3">
                  <!-- <a href="#" class="btn btn-primary btn-sm mr-3">View Profile</a> -->
                  <a href="{{ url('users/edit') }}" class="btn border border-primary btn-sm">Edit Profile</a>
                </div>
            </div>
          </div>
          <div class="mt-3 shadow-sm p-3 text-center d-flex align-items-center justify-content-center cls_dashslider">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <strong>
                         {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                      </strong>                           
                      {{ trans('messages.host_dashboard.title') }}
                    </div>
                    <!-- <div class="carousel-item">
                      <strong>
                        {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                      </strong>
                      {{trans('messages.host_dashboard.welcome_message')}}
                    </div>
                    <div class="carousel-item">
                      <strong>
                        {{trans('messages.host_dashboard.hi_first_name', ['first_name' => $user->first_name])}}
                      </strong>                         
                      {{ trans('messages.host_dashboard.title') }}
                    </div> -->
                  </div>
            </div>
          </div>

          <!-- <div class="d-flex mt-3">
            <a href="{{ url('users/transaction_history') }}" class="btn btn-primary btn-sm">{{ trans('messages.host_dashboard.transaction_history') }}</a>
          </div> -->
        </div>        
    
    </div>
  </div>
</div>
  <div class="notify-wrap py-4 py-md-5">
    <div class="container">
      <div ng-controller="Tabsh" class="notify-tab">
        <ul role="tablist" class="d-flex tabs align-items-end">
          <li>
            <a href="javascript:void(0);" ng-click="show= 1;tab1=true;tab2=false" class="tab-item" role="tab" aria-controls="hdb-tab-standalone-first" aria-selected="@{{tab1}}" data-tracking="{&quot;section&quot;:&quot;inbox_pending_requests_tab&quot;}">
              ({{@$pending_count}} {{ trans('messages.dashboard.new') }}) {{ trans('messages.host_dashboard.Pending_requests_and_inquiries') }}
            </a>
          </li>
          <li>
            <a href="javascript:void(0);" ng-click="show= 2;tab2=true;tab1=false" class="tab-item" role="tab" aria-controls="hdb-tab-standalone-second" aria-selected="@{{tab2}}" data-tracking="{&quot;section&quot;:&quot;inbox_alerts_tab&quot;}">
              {{ trans('messages.host_dashboard.Notifications') }}
              <i class="alert-count text-center" ng-hide="{{ $notification_count == 0 }}">
                {{ $notification_count }}
              </i>
            </a>
          </li>
        </ul>

        <div class="notify-list" ng-show="show == 1">
          <ul class="col-12 list-layout">
            @foreach($pending as $all)
              @if($all->host_check == 1 && ($all->reservation->status == 'Pending' || $all->reservation->status == 'Inquiry'))
                <li class="d-flex" id="thread_{{ $all->id }}">
                  <div class="col-3 pl-0 col-md-2 list-img text-center">      
                    <a data-popup="true" href="{{ route('show_profile',[$all->user_details->id])}}">
                      <img title="{{ $all->user_details->first_name }}" src="{{ $all->user_details->profile_picture->src }}" class="media-round media-photo" alt="{{ $all->user_details->first_name }}">
                    </a>
                  </div>

                  <div class="col-9 col-md-10 p-0 d-md-flex mt-2 mt-lg-4 text-md-center">
                    <div class="list-name col-12 col-md-3">
                      <h3 class="text-truncate">
                        {{ $all->user_details->first_name }}
                      </h3>
                      <span class="list-date">
                        {{ $all->created_time }}
                      </span>
                    </div>

                    <div class="reserve-link col-12 col-md-6">
                        <a href="{{ ($all->reservation->status == 'Pending') ? route('reservation_request',[$all->reservation_id]) : route('host_conversation',[$all->reservation_id]) }}">
                          <span class="list-subject unread-message font-weight-bold"> {{ @$all->message }} </span>
                          <span class="street-address">
                            {{ optional($all->space_address)->address_line_1 }} {{ optional($all->space_address)->address_line_2 }},
                          </span>
                          <span class="locality">
                            {{ $all->space_address->city }},
                          </span> 
                          <span class="region">
                            {{ $all->space_address->state }}
                          </span>
                          @if($all->reservation->status == 'Pending')
                            <span class="check-date d-inline-block"> ({{ $all->reservation->dates_subject }}) </span>
                          @endif
                          @if($all->inbox_thread_count > 1)
                            <span>
                              <i class="alert-count1 text-center inbox_message_count"> {{ $all->inbox_thread_count }} </i>
                            </span>
                          @endif
                      </a>
                    </div>
                    <div class="list-status col-12 col-md-3">
                      <span class="d-block label label-{{ $all->reservation->status_color }}">
                        <strong> {{ $all->reservation->status_language }} </strong>
                      </span>
                      @if($all->reservation->type != 'contact')
                        <span>
                          {{ $currency_symbol }} {{ $all->reservation->subtotal - $all->reservation->host_fee }} 
                        </span>
                      @endif
                    </div>
                  </div>
                </li>
              @endif
            @endforeach
          </ul>
          <div class="col-12 text-center">
            <a class="theme-link" href="{{ route('inbox') }}"> {{ trans('messages.dashboard.all_messages') }} </a>
          </div>
        </div>

        <!-- notification -->
        <div class="notify-list" ng-show="show==2" id="{{ count($unread) }}">
          <ul class="col-12 list-layout">
            @foreach($unread as $all)
              <!-- Start Admin Message -->
              @if($all->user_to == $all->user_from)
              <li class="d-flex" id="thread_{{ $all->id }}">
                <div class="col-3 pl-0 col-md-2 list-img text-center">
                  <a data-popup="true" href="{{ route('show_profile',[$all->user_details->id])}}">
                    <img title="{{ $all->admin_name }}" src="{{ asset('admin_assets/dist/img/avatar04.png') }}" class="media-round media-photo" alt="{{ $all->admin_name }}">
                  </a>
                </div>
                <div class="col-9 col-md-10 p-0 d-md-flex mt-2 mt-lg-4 text-md-center">
                  <div class="list-name col-12 col-md-3">
                    <h3 class="text-truncate">
                    {{ $all->admin_name }}
                    </h3>
                    <span class="list-date">
                      {{ $all->created_time }}
                    </span>
                  </div>
                  <div class="reserve-link col-12 col-md-6">
                    <a href="{{ ($all->room_id == 0) ? route('admin_messages',['id' => $all->user_to]) : route('admin_resubmit_message',['id' => $all->reservation_id]) }}">
                      <span class="list-subject unread_message font-weight-bold">
                        {{ $all->message }}
                      </span>
                      @if(@$all->inbox_thread_count > 1)
                      <span>
                        <i class="alert-count1 text-center inbox_message_count">
                        {{ $all->inbox_thread_count }}
                        </i>
                      </span>
                      @endif
                    </a>
                  </div>
                </div>
              </li>
              <!-- End Admin Message -->
              @else
              <li class="d-flex" id="thread_{{ $all->id }}">
                <div class="col-3 pl-0 col-md-2 list-img text-center">
                  <a data-popup="true" href="{{ route('show_profile',[$all->user_details->id])}}">
                    <img title="{{ $all->user_details->first_name }}" src="{{ $all->user_details->profile_picture->src }}" class="media-round media-photo" alt="{{ $all->user_details->first_name }}">
                  </a>
                </div>
                <div class="col-9 col-md-10 p-0 d-md-flex mt-2 mt-lg-4 text-md-center">
                  <div class="list-name col-12 col-md-3">
                    <h3 class="text-truncate">
                    {{ $all->user_details->first_name }}
                    </h3>
                    <span class="list-date">
                      {{ $all->created_time }}
                    </span>
                  </div>
                  <div class="reserve-link col-12 col-md-6">
                    @if($all->host_check == 1)
                    <a class="link-reset text-muted1" href="{{ (@$all->reservation->status == 'Pending') ? url('reservation') : url('messaging/qt_with') }}/{{ $all->reservation_id}}">
                      @else
                      <a href="{{ url('z/q')}}/{{ $all->reservation_id }}">
                        @endif
                        <span class="list-subject unread_message font-weight-bold">
                          {{ @$all->message }}
                        </span>
                        <span class="street-address">
                          {{ @$all->reservation->space->space_address->address_line_1 }} {{ @$all->reservation->space->space_address->address_line_2 }},
                        </span>
                        <span class="locality">
                          {{ @$all->reservation->space->space_address->city }},
                        </span>
                        <span class="region">
                          {{ @$all->reservation->space->space_address->state }}
                        </span>
                        @if(@$all->inbox_thread_count > 1)
                        <span>
                          <i class="alert-count1 text-center inbox_message_count">
                          {{ $all->inbox_thread_count }}
                          </i>
                        </span>
                        @endif
                        @if(@$all->reservation->type != 'contact' )
                        <span class="check-date d-inline-block"> ({{ @$all->reservation->dates_subject }}) </span>
                        @endif
                      </a>
                    </div>
                    @if(@$all->reservation->list_type != 'Experiences' || @$all->reservation->type != 'contact')
                    <div class="list-status col-12 col-md-3">
                      <span class="d-block label label-{{ @$all->reservation->status_color }}">
                        <strong> {{ @$all->reservation->status_language }} </strong>
                      </span>
                      {{ $currency_symbol }}
                      <span ng-show="{{ $all->host_check == 1}}">
                        {{ @$all->reservation->subtotal - @$all->reservation->host_fee }}
                      </span>
                      <span ng-show="{{ $all->guest_check == 1 }}">
                        {{ @$all->reservation->total }}
                      </span>
                    </div>
                    @endif
                  </div>
                </li>
                @endif
                @endforeach
              </ul>
              <div class="col-12 text-center">
                <a class="theme-link" href="{{ url('inbox') }}">
                  {{ trans('messages.dashboard.all_messages') }}
                </a>
              </div>
            </div>
            <div class="invite-wrap text-center mt-4 mt-md-5 py-4">
              <h3>
              <strong>
              {{trans('messages.host_dashboard.earn_Travel')}}
              </strong>
              </h3>
              <p>
                {{ trans('messages.referrals.earn_up_to') }} {{ html_string($result->value(5)) }}{{ $result->value(2) + $result->value(3) }} {{ trans('messages.referrals.everyone_invite') }}.
              </p>
              <a data-tracking="{&quot;section&quot;:&quot;promo_invite_friends&quot;}" href="{{ url('invite') }}" class="btn btn-large btn-primary">
                {{trans('messages.host_dashboard.invite_friends')}}
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
    @stop