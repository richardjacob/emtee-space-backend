@extends('template')
@section('main')
<main id="site-content" role="main" ng-controller="cancel_reservation">
  @include('common.subheader')
  <div class="your-trips my-4 my-md-5">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-3 side-nav">
          @include('common.sidenav')
        </div>
        <div class="col-12 col-md-9 your-trip-info mt-3 mt-md-0">
          @if($pending_trips->count() == 0 && $current_trips->count() == 0 && $upcoming_trips->count() == 0)
          <div class="card mb-5">
            <div class="card-header">
              <h3>
                @lang('messages.new_space.your_bookings')
              </h3>
            </div>
            <div class="card-body">
              <p>
                @lang('messages.new_space.no_current_bookings')
              </p>
              <form method="get" action="{{ route('search_page') }}" accept-charset="UTF-8">
                <div class="trip-search-bar d-flex">
                  <input type="text" placeholder="{{ trans('messages.header.where_are_you_going') }}" name="location" id="location" autocomplete="off" class="location">
                  <button id="submit_location" search_type="current" class="btn btn-primary ml-3" type="submit">
                    {{ trans('messages.home.search') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
          @endif
          @if($pending_trips->count() > 0)
          <div class="card mb-5">
            <div class="card-header">
              <h3>
                {{ trans('messages.new_space.pending_bookings') }}
              </h3>
            </div>
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>
                      {{ trans('messages.your_reservations.status') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.location') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.host') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.dates') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.options') }}
                    </th>
                  </tr>
                  @foreach($pending_trips as $pending_trip)
                  @include('trips/trip_row', ['trip_row' => $pending_trip, 'trip_type' => 'Pending'])
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif
          @if($current_trips->count() > 0)
          <div class="card mb-5">
            <div class="card-header">
              <h3>
                {{ trans('messages.new_space.current_bookings') }}
              </h3>
            </div>
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>
                      {{ trans('messages.your_reservations.status') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.location') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.host') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.dates') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.options') }}
                    </th>
                  </tr>
                  @foreach($current_trips as $current_trip)
                  @include('trips/trip_row', ['trip_row' => $current_trip, 'trip_type' => 'Current'])
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif
          @if($upcoming_trips->count() > 0)
          <div class="card mb-5">
            <div class="card-header">
              <h3>
                {{ trans('messages.new_space.upcoming_bookings') }}
              </h3>
            </div>
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <th>
                      {{ trans('messages.your_reservations.status') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.location') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.host') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.dates') }}
                    </th>
                    <th>
                      {{ trans('messages.your_trips.options') }}
                    </th>
                  </tr>
                  @foreach($upcoming_trips as $upcoming_trip)
                  @include('trips/trip_row', ['trip_row' => $upcoming_trip, 'trip_type' => 'Upcoming'])
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  @if($upcoming_trips->count() > 0 || $current_trips->count() > 0 || $pending_trips->count() > 0)
  <div class="modal" role="dialog" id="cancel-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form accept-charset="UTF-8" action="{{ route('guest_cancel_reservation') }}" id="cancel_reservation_form" method="post" name="cancel_reservation_form">
          {!! Form::token() !!}
          <div class="modal-header">
            <h5 class="modal-title">
              {{ trans('messages.your_reservations.cancel_this_reservation') }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="decline_reason_container" class="mb-2"> 
              <p>
                {{ trans('messages.your_reservations.reason_cancel_reservation') }}
              </p>
              <p>
                <strong>
                  {{ trans('messages.your_trips.response_not_shared_host') }}
                </strong>
              </p>
              <div class="select">
                <select id="cancel_reason" name="cancel_reason">
                  <option value="">
                    {{ trans('messages.your_reservations.why_declining') }}
                  </option>
                  <option value="no_longer_need_accommodations">
                    {{ trans('messages.your_reservations.I_no_longer_need_accommodations') }}
                  </option>
                  <option value="travel_dates_changed">
                    {{ trans('messages.your_reservations.My_travel_dates_changed_successfully') }}
                  </option>
                  <option value="made_the_reservation_by_accident">
                    {{ trans('messages.your_reservations.i_made_the_reservation_by_accident') }}
                  </option>
                  <option value="I_have_an_extenuating_circumstance">
                    {{ trans('messages.your_reservations.i_have_an_extenuating_circumstance') }}
                  </option>
                  <option value="my_host_needs_to_cancel">
                    {{ trans('messages.your_reservations.my_host_need_to_cancel') }}
                  </option>
                  <option value="uncomfortable_with_the_host">
                    {{ trans('messages.your_reservations.i_m_uncomfortable_with_the_host') }}
                  </option>
                  <option value="place_not_okay">
                    {{ trans('messages.your_reservations.the_place_is_not_what_was_expecting') }}
                  </option>
                  <option value="other">
                    {{ trans('messages.your_reservations.other') }}
                  </option>
                </select>
              </div>
              <div id="cancel_reason_other_div" class="d-none mt-2">
                <label for="cancel_reason_other">
                  {{ trans('messages.your_reservations.why_cancel') }}
                </label>
                <textarea id="decline_reason_other" name="decline_reason_other" rows="4"></textarea>
              </div>
            </div>
            <label for="cancel_message" class="mt-2">
              {{ trans('messages.your_trips.type_msg_host') }}...
            </label>
            <textarea cols="40" id="cancel_message" name="cancel_message" rows="10"></textarea>
            <input type="hidden" name="id" id="reserve_code" value="">
          </div>
          <div class="modal-footer">
            <input type="hidden" name="decision" value="decline">
            <input class="btn btn-primary w-auto" id="cancel_submit" name="commit" type="submit" value="{{ trans('messages.your_reservations.cancel_this_reservation') }}">
            <button class="btn ml-2" data-dismiss="modal" aria-label="Close">
              {{ trans('messages.home.close') }}
            </button>
          </div>
        </form>      
      </div>
    </div>
  </div>
  @endif
 


@include('trips/dispute_modal')
</main>
@stop
@push('scripts')
<script type="text/javascript">
 var show_popup  = {!! session('popup_reservation') ? session('popup_reservation') : 0  !!};
 console.log(show_popup);
 if(show_popup==true)
  $('.reservation_complete').modal('show');  
</script>
@endpush