@extends('template')
@section('main')
<main id="site-content" role="main">
  <div class="referrals-wrap py-4">
    <div class="container">
      <div class="profile-image">
        <a href="{{ route('show_profile',[$result->id]) }}" target="_blank" title="{{ $result->first_name }}">
          <img alt="{{ $result->first_name }}" src="{{ $result->profile_picture->src }}" title="{{ $result->first_name }}">
        </a>
      </div>
      <h1>
        {{ $result->first_name }} @lang('messages.referrals.gave_you') {{ html_string($referral->value(5)) }}{{ $referral->value(4) }} @lang('messages.referrals.to_travel').
      </h1>

      <p>
        {{ $site_name }} @lang('messages.referrals.best_way_to_rent_unique').
      </p>

      <a href="{{ url('/') }}/signup_login?referral={{ $result->id }}" class="btn btn-primary btn-large" data-signup-modal="" id="signup_login_button">
        @lang('messages.referrals.signup_to_claim')
      </a>
    </div>
  </div>

  <div class="referrals-work text-center py-4 py-md-5">
    <div class="container">
      <h1>
        @lang('messages.referrals.how_it_works')
      </h1>
      <p>
        @lang('messages.referrals.rent_unique').
      </p>

      <ul class="supporting-points d-md-flex">
        <li class="col-md-4 supporting-point explore">
          <a href="javascript:void();" class="icon"></a>
          <div class="point-text">
            <h3>
              @lang('messages.referrals.explore')
            </h3>
            <p>
              @lang('messages.referrals.find_perfect_place').
            </p>
          </div>
        </li>

        <li class="col-md-4 supporting-point contact">
          <a href="javascript:void();" class="icon"></a>
          <div class="point-text">
            <h3>
              @lang('messages.referrals.contact')
            </h3>
            <p>
              @lang('messages.referrals.message_hosts').
            </p>
          </div>
        </li>

        <li class="col-md-4 supporting-point book">
          <a href="javascript:void();" class="icon"></a>
          <div class="point-text">
            <h3>
              @lang('messages.referrals.book')
            </h3>
            <p>
              @lang('messages.referrals.view_your_itinerary').
            </p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</main>
@stop