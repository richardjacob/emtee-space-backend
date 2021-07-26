<ul class="sidenav-list">
  @if (Route::currentRouteName() == 'space' || Route::currentRouteName() == 'reservation_request' || Route::currentRouteName() == 'my_bookings')
  <li>
    <a href="{{ route('space') }}" aria-selected="{{ (Route::currentRouteName() == 'space') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans_choice('messages.header.your_listing',2) }}
    </a>
  </li>
  <li>
    <a href="{{ route('my_bookings') }}" aria-selected="{{ (Route::currentRouteName() == 'my_bookings') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans('messages.header.your_reservations') }}
    </a>
  </li>
  @endif

  @if (Route::currentRouteName() == 'current_bookings' || Route::currentRouteName() == 'previous_bookings')
  <li>
    <a class="sidenav-item" aria-selected="{{ (Route::currentRouteName() == 'current_bookings') ? 'true' : 'false' }}" href="{{ route('current_bookings') }}">
      {{ trans('messages.new_space.your_bookings') }}
    </a>
  </li>
  <li>
    <a class="sidenav-item" aria-selected="{{ (Route::currentRouteName() == 'previous_bookings') ? 'true' : 'false' }}" href="{{ route('previous_bookings') }}">
      {{ trans('messages.new_space.prev_bookings') }}
    </a>
  </li>
  @endif

  @if (Route::currentRouteName() == 'edit_profile' || Route::currentRouteName() == 'user_reviews' || Route::currentRouteName() == 'edit_profile_media' || Route::currentRouteName() == 'edit_verification')
  <li>
    <a href="{{ route('edit_profile') }}" aria-selected="{{ (Route::currentRouteName() == 'edit_profile') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans('messages.header.edit_profile') }}
    </a>
  </li>
  <li>
    <a href="{{ route('edit_profile_media') }}" aria-selected="{{ (Route::currentRouteName() == 'edit_profile_media') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans_choice('messages.header.photo', 2) }}
    </a>
  </li>
  <li>
    <a href="{{ route('edit_verification') }}" aria-selected="{{ (Route::currentRouteName() == 'edit_verification') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans('messages.header.trust_verification') }}
    </a>
  </li>
  <li>
    <a href="{{ route('user_reviews') }}" aria-selected="{{ (Route::currentRouteName() == 'user_reviews') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans_choice('messages.header.review', 2) }}
    </a>
  </li>
  @endif

  @if (Route::currentRouteName() == 'security' || Route::currentRouteName() == 'payout_preferences' || Route::currentRouteName() == 'transaction_history')
  <li>
    <a href="{{ route('payout_preferences',[Auth::user()->id]) }}" aria-selected="{{ (Route::currentRouteName() == 'payout_preferences') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans('messages.header.payout_preferences') }}
    </a>
  </li>
  <li>
    <a href="{{ route('transaction_history') }}" aria-selected="{{ (Route::currentRouteName() == 'transaction_history') ? 'true' : 'false'}}" class="sidenav-item">
      {{ trans('messages.header.transaction_history') }}
    </a>
  </li>
  @if(Auth::user()->facebook_id == "" && Auth::user()->linkedin_id == "")
  <li>
    <a href="{{ route('security') }}" aria-selected="{{ (Route::currentRouteName() == 'security') ? 'true' : 'false' }}" class="sidenav-item">
      {{ trans('messages.header.security') }}
    </a>
  </li>
  @endif
  @endif
</ul>