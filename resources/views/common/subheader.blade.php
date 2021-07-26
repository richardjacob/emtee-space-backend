<div class="subnav hide-print" ng-cloak>
  <div class="container">
    <ul class="subnav-list d-flex text-nowrap">
      <li>
        <a href="{{ route('dashboard') }}" aria-selected="{{(isActiveRoute('dashboard')) ? 'true' : 'false'}}" class="subnav-item">
          {{ trans('messages.header.dashboard') }}
        </a>
      </li>
      <li>
        <a href="{{ route('inbox') }}" aria-selected="{{ (isActiveRoute('inbox', 'guest_conversation', 'host_conversation', 'admin_messages', 'admin_resubmit_message')) ? 'true' : 'false'}}" class="subnav-item">
          {{ trans('messages.header.inbox') }}
        </a>
      </li>
      <li>
        <a href="{{ route('space') }}" aria-selected="{{ isActiveRoute('space','my_bookings') ? 'true' : 'false'}}" class="subnav-item">
          @choice('messages.header.your_listing',2)
        </a>
      </li>
      <li>
        <a href="{{ route('current_bookings') }}" aria-selected="{{ isActiveRoute('current_bookings', 'previous_bookings') ? 'true' : 'false'}}" class="subnav-item">
          @lang('messages.new_space.your_bookings')
        </a>
      </li>
      <li>
        <a href="{{ route('edit_profile') }}" aria-selected="{{ isActiveRoute('edit_profile', 'user_reviews', 'edit_profile_media', 'edit_verification') ? 'true' : 'false'}}" class="subnav-item">
          {{ trans('messages.header.profile') }}
        </a>
      </li>
      <li>
        <a href="{{ route('payout_preferences',[auth()->id()]) }}" aria-selected="{{ isActiveRoute('security', 'payout_preferences', 'transaction_history') ? 'true' : 'false'}}" class="subnav-item">
          {{ trans('messages.header.account') }}
        </a>
      </li>
      <li>
        <a href="{{ route('invite') }}" class="subnav-item">
          {{ trans('messages.referrals.travel_credit') }}
        </a>
      </li>
      <li>
        <a href="{{ route('disputes') }}" class="subnav-item" aria-selected="{{ isActiveRoute('disputes') ? 'true' : 'false'}}">
          {{ trans('messages.disputes.disputes') }}
          @if(auth()->user()->dispute_messages_count > 0)
          <i class="alert-count text-center ">
            {{auth()->user()->dispute_messages_count}}
          </i>
          @endif
        </a>
      </li>
    </ul>
  </div>
</div>

@if(Session::has('message') && Auth::check())
<div class="alert {{ Session::get('alert-class') }} text-center" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
  {{ Session::get('message') }}
</div>
@endif