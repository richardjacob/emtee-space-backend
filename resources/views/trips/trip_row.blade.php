<tr>
  <td class="status">
    <span class="label label-orange label-{{ $trip_row->status_color }}">
      @if($trip_row->status == 'Pre-Accepted' || $trip_row->status == 'Inquiry')
      @if($trip_row->checkin >= date("Y-m-d"))
      <span class="label label-{{ $trip_row->status_color }}">
        {{ trans('messages.dashboard.'.$trip_row->status) }}
      </span>
      @else
      <span class="label label-info">
        {{trans('messages.dashboard.Expired')}}
      </span>
      @endif
      @else
      <span class="label label-{{ $trip_row->status_color }}">
        {{ trans('messages.dashboard.'.$trip_row->status) }}
      </span>
      @endif
      
      @if($trip_row->status=='Pre-Accepted')
      <div class="mt-2">
        @if($trip_row->checkin >= date("Y-m-d"))
        @if( $trip_row->avablity!=1 || $trip_row->date_check!='No' )
        <a href="{{url('payments/book?reservation_id='.$trip_row->id)}}" class="btn btn-primary text-nowrap" id="{{ $trip_row->id }}" data-room="{{ $trip_row->room_id }}" data-checkin="{{ $trip_row->checkin }}" data-checkout="{{ $trip_row->checkout }}" >
          <p hidden="hidden" class="pending_id">
            <?php echo $trip_row->id;?>
          </p>
          <span>
            {{ trans('messages.inbox.book_now') }}
          </span>
        </a>
        @else
        <span class="theme-color" id="al_res{{ $trip_row->id }}">
          {{ trans('messages.inbox.already_booked') }}
        </span>
        @endif
        @endif
      </div>
      @endif
    </span>
  </td>
  <td class="location">
    <a href="{{$trip_row->space->link}}" class="{{$trip_row->title_class}} text-capitalize theme-link d-block">
      {{ $trip_row->space->name }}
    </a>
    @if(@$trip_row->space->space_address->city != '') 
    {{ $trip_row->space->space_address->city }}
    @else
    {{ $trip_row->space->space_address->state }}
    @endif
  </td>
  <td class="host">
    <a class="theme-link" href="{{ url('/') }}/users/show/{{ $trip_row->host_id }}">
      {{ $trip_row->space->users->full_name }}
    </a>
  </td>
  <td class="dates text-nowrap">
    {{ $trip_row->dates_subject }}
  </td>
  <td>
    <ul class="trip-options">
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/z/q/{{ $trip_row->id }}">
          {{ trans('messages.your_reservations.message_history') }}
        </a>
      </li>

      @if($trip_type == 'Current')
      @if($trip_row->status != "Cancelled" && $trip_row->status != "Declined" && $trip_row->status != "Expired" && $trip_row->status != " ")
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/reservation/itinerary?code={{ $trip_row->code }}">
          {{ trans('messages.your_trips.view_itinerary') }}
        </a>
      </li>
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/reservation/receipt?code={{ $trip_row->code }}">
          {{ trans('messages.your_trips.view_receipt') }}
        </a>
      </li>
      <li>
        <a class="theme-link text-nowrap" href="javascript:void(0)" id="{{$trip_row->id}}-trigger" data-toggle="modal" data-target="#cancel-modal">
          {{ trans('messages.your_reservations.cancel') }}
        </a>
      </li>
      @endif
      @endif

      @if($trip_type == 'Pending')
      <li>        
        @if($trip_row->date_check!='No')
        @if($trip_row->checkin >= date("Y-m-d"))
        <a class="theme-link text-nowrap" rel="nofollow" data-method="post" data-confirm="Are you sure that you want to cancel the request? Any money transacted will be refunded." id="{{ $trip_row->id }}-trigger-pending" class="button-steel" href="javascript:void(0)" data-toggle="modal" data-target="#cancel-modal">
          {{ trans('messages.your_trips.cancel_request') }}
        </a>
        @endif
        @endif
      </li>
      @endif

      @if($trip_type == 'Upcoming')
      @if($trip_row->status != "Cancelled"  && $trip_row->status != "Declined" && $trip_row->status != "Expired")
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/reservation/itinerary?code={{ $trip_row->code }}">
          {{ trans('messages.your_trips.view_itinerary') }}
        </a>
      </li>
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/reservation/receipt?code={{ $trip_row->code }}">
          {{ trans('messages.your_trips.view_receipt') }}
        </a>
      </li>
      <li>
        <a class="theme-link text-nowrap" href="javascript:void(0)" id="{{$trip_row->id}}-trigger" data-toggle="modal" data-target="#cancel-modal">
          {{ trans('messages.your_reservations.cancel') }}
        </a>
      </li>
      @endif
      @endif

      @if($trip_type == 'Previous')
      @if($trip_row->status != "Cancelled" && $trip_row->status != "Declined" && $trip_row->status != "Expired" && $trip_row->status != "Pre-Accepted" && $trip_row->status != "Inquiry" )
      <li>
        <a class="theme-link text-nowrap" href="{{ url('/') }}/reservation/itinerary?code={{ $trip_row->code }}">
          {{ trans('messages.your_trips.view_itinerary') }}
        </a>
      </li>
      <li>
        <a class="theme-link text-nowrap" href="{{ route('receipt',['code'=>$trip_row->code]) }}">
          {{ trans('messages.your_trips.view_receipt') }}
        </a>
      </li>
      @if($trip_row->review_days > 0 && $trip_row->review_days < 15)
        <li>
            <a class="theme-link text-nowrap" href="{{ route('reviews_edit',[$trip_row->id]) }}">
              @if($trip_row->guest_reviews->count() > 0)
                @lang('messages.reviews.edit') @lang('messages.email.review')
              @else
                @lang('messages.reviews.write_a_review')
              @endif
            </a>
        </li>
      @endif

      @endif
      @endif

      @if($trip_row->can_apply_for_dispute && $trip_row->paymode != '')
      <button class="btn btn-primary mt-2" type="button" id="js_dispute_btn" ng-click="trigger_create_dispute({{collect(['id' => $trip_row->id, 'currency_code' => $trip_row->currency_code, 'currency_symbol' => html_entity_decode($trip_row->currency->symbol)])->toJson()}})">
        {{trans('messages.disputes.dispute')}}
      </button>
      @endif
    </ul>
  </td>
</tr>