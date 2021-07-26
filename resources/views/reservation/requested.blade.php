@extends('template')
@section('main') 
<main role="main" id="site-content">      
  <div class="share-itinerary py-4 py-md-5">
    <div class="container">
      <div class="share-info d-none d-md-block mb-md-5">
        @if($reservation_details->status == 'Pending')
        <h1>
          <span>
            @lang('messages.payments.your_request_sent')
          </span>
          <span>
            @lang('messages.payments.request_sent')!
          </span>
        </h1>
        @endif
        @if($reservation_details->status == 'Accepted')
        <h1>
          <span>
            @lang('messages.payments.get_ready_for')
            @if($reservation_details->space->space_address->city !='')
            {{ $reservation_details->space->space_address->city }}!
            @else
            {{ $reservation_details->space->space_address->state }}!
            @endif
          </span>
        </h1>
        @endif
        @if($reservation_details->status == 'Pending')
        <p>
          @lang('messages.payments.isnot_confirmed_reservation', ['first_name'=>$reservation_details->space->users->first_name])
        </p>
        @endif
        @if($reservation_details->status == 'Accepted')
        <p>
          @lang('messages.payments.confirmed_reservation', ['first_name'=>$reservation_details->space->users->first_name,'email'=>$reservation_details->users->email])
        </p>
        @endif
      </div>
      {!! Form::open(['url' => route('itinerary_friends'), 'method' => 'POST', 'id' => 'share-itinerary-form', 'autocomplete' => 'off']) !!}
        <input type="hidden" value="{{ $reservation_details->code }}" name="code">
        <input type="hidden" value="additional_guests" name="page5_action">
        <div class="row">
          <div class="col-12 col-md-6 col-lg-8 order-2 order-md-1">
            <div class="itinerary-mail col-12 p-0">
              <div class="share-info my-4 d-md-none">
                @if($reservation_details->status == 'Pending')
                <h1>
                  <span>
                    @lang('messages.payments.your_request_sent')
                  </span>
                  <span>
                    @lang('messages.payments.request_sent')
                  </span>
                </h1>
                @endif
                @if($reservation_details->status == 'Accepted')
                <h1>
                  <span>
                    @lang('messages.payments.get_ready_for')
                    @if($reservation_details->space->space_address->city !='')
                    {{ $reservation_details->space->space_address->city }}!
                    @else
                    {{ $reservation_details->space->space_address->state }}!
                    @endif
                  </span>
                </h1>
                @endif
                @if($reservation_details->status == 'Pending')
                <p>
                  @lang('messages.payments.isnot_confirmed_reservation', ['first_name'=>$reservation_details->space->users->first_name])
                </p>
                @endif
                @if($reservation_details->status == 'Accepted')
                <p>
                  @lang('messages.payments.confirmed_reservation', ['first_name'=>$reservation_details->space->users->first_name,'email'=>$reservation_details->users->email])
                </p>
                @endif
              </div>
              <h3>
                @lang('messages.payments.email_itinerary')
              </h3>
              @if($reservation_details->status == 'Pending')
              <p>
                @lang('messages.payments.send_trip_details_to_friends')
              </p>
              @endif
              <div class="add-friend">
                <div class="row add-text-email">
                  <div class="col-12 col-md-10">
                    <div class="friend-email">
                      <div data-email-tagging="false" data-typeahead-type="recent" class="email-input-typeahead-container">
                        <span class="twitter-typeahead">
                          <input type="email" placeholder="@lang('messages.dashboard.email_address')" name="friend_address[]" autocomplete="none" class="typeahead tt-input" spellcheck="false" dir="auto">
                          <pre aria-hidden="true"></pre>
                          <div class="tt-menu">
                            <div class="tt-dataset tt-dataset-email-typeahead">
                            </div>
                          </div>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <a data-prevent-default="true" id="add_another" href="javascript:void(0);" class="theme-link mt-2">
                @lang('messages.payments.add_another')
              </a>
            </div>
            <div class="my-4">
              <button type="submit" class="btn btn-primary">
                @lang('messages.lys.continue')
              </button>
            </div>
          </div>
          <div class="col-12 col-md-6 col-lg-4 listing-card order-1 order-md-2">
            <div class="reservation-img">
              <a target="_blank" href="{{ route('space_details',['id' => $reservation_details->space_id]) }}" class="d-block">
                <img src=" {{ $reservation_details->space->photo_name }}">
              </a>
            </div>
            <div class="reservation-info d-flex mt-4">
              <div class="col-5 text-center">
                <a class="profile-link" href="{{ route('show_profile',['id' => $reservation_details->host_id]) }}">
                  <div class="profile-image mb-2">
                    <img src="{{ $reservation_details->space->users->profile_picture->header_src }}" class="img-fluid" width="60" height="60">
                  </div>
                  <h5>
                    {{ $reservation_details->space->users->first_name }}
                  </h5>
                </a>
                <div class="my-2">
                  <small>
                    <div class="star-rating text-left">
                      <div class="foreground">
                      </div>
                      <div>
                      </div>
                    </div>
                  </small>
                </div>
              </div>
              <div class="col-7 p-0">
                <h3 class="reserve-user">
                  <a class="normal-link" target="_blank" href="{{ $reservation_details->space->link }}">
                    {{ $reservation_details->space->name }}
                  </a>
                </h3>
                <label class="reserve-date">
                  {{ $reservation_details->dates }}
                  </label>
                <p>
                  @if($reservation_details->status == 'Accepted')
                  <span class="d-block">
                    {{ $reservation_details->space->space_address->address_line_1 }}
                  </span>
                  <span class="d-block">
                    @if($reservation_details->space->space_address->city) {{ $reservation_details->space->space_address->city }}, @endif
                    {{ $reservation_details->space->space_address->state }}
                    {{ $reservation_details->space->space_address->postal_code }}
                  </span>
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
      {!! Form::close() !!}
    </div>
  </div>
</main>
@push('scripts')
<script>
  $(document).ready(function() {
    $('#add_another').click(function() {
      $(".add-text-email:first").clone().appendTo(".add-friend").find('input[type="email"]').val('');
    });

// browser back button redirect for accomendation page 
if (window.history && window.history.pushState) {
  $(window).on('popstate', function() {
    var hashLocation = location.hash;
    var hashSplit = hashLocation.split("#!/");
    var hashName = hashSplit[1];
    if (hashName !== '') {
      var hash = window.location.hash;
      if (hash === '') {
        window.location='#';
        return false;
      }
    }
  });
  window.history.pushState('forward', null, '#');
}
})
</script>
@endpush
@stop