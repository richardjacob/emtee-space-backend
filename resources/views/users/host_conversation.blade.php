@extends('template')
@section('main')
<main id="site-content" role="main" ng-controller="conversation" class="notranslate">
  @include('common.subheader')
  <div class="conversation-content" ng-cloak>
    <div class="container">
      <div class="pt-4 mb-3 conversation-head">
        <h1>
          {{ trans('messages.inbox.conversation_with') }} {{ $messages[0]->reservation->users->first_name }}
        </h1>
      </div>
      @if($messages[0]->reservation->status == 'Accepted')
      <div class="col-12 accepted-alert text-left alert alert-success alert-block p-3 mb-4">
        <div class="d-flex">
          <i class="icon icon-star-circled mr-3"></i>
          <p>
            <strong>
              {{ trans('messages.inbox.accepted') }}
            </strong>
            {{ trans('messages.inbox.you_have_accepted_reservation',['site_name'=>$site_name, 'first_name'=>$messages[0]->reservation->users->first_name]) }} 
            <a class="theme-link" href="mailto:{{ $messages[0]->reservation->users->email }}">
              {{ trans('messages.inbox.email') }}
            </a>
            @if($messages[0]->reservation->users->primary_phone_number != ''){{trans('messages.login.or')}} {{strtolower(trans('messages.profile.phone_number'))}} ({{$messages[0]->reservation->users->primary_phone_number}}) @endif 
          </p>
        </div>
        <div class="mt-2">
          <a class="theme-link" href="{{ url('/') }}/reservation/itinerary?code={{ $messages[0]->reservation->code }}">
            {{ trans('messages.your_trips.view_itinerary') }}
          </a>
        </div>
      </div>
      @endif

      <div class="conversation-wrap d-md-flex row" ng-init="space_id={{ $messages[0]->reservation->space_id }};reservation_id={{ $messages[0]->reservation_id }};">
        <div class="col-12 col-md-7 col-lg-8 conversation-left">
          <ul>
            <li id="message_friction_react"></li>
            <li id="post_message_box">
              <form id="non_special_offer_form" data-key="non_special_offer_form" class="message_form">
                <input type="hidden" value="{{ $messages[0]->reservation_id }}" name="inquiry_post_id" id="reservation_id">
                <input type="hidden" value="{{ $messages[0]->reservation->space_id }}" name="space_id" id="space_id">
                <input type="hidden" value="" name="template">
                <textarea placeholder="{{ trans('messages.inbox.add_personal_msg') }}" name="message" id="message_text"></textarea>
                <div>
                <div class="page_translate" onclick="google_translate()"><a href=''>{{trans('messages.your_reservations.google_translate')}}</a></div>
                </div>
                <div class="my-4 text-right">
                  @if($status != 'Expired')
                    @if($messages[0]->reservation->type != 'contact')
                      <a class="btn attach-offer" href="javascript:void(0);">
                        @lang('messages.inbox.attach_special_offer')
                      </a>
                      @endif
                      @if($messages[0]->reservation->type == 'contact')
                      <a id="pre_approve_button" class="btn pre-approve" href="javascript:void(0);">
                        @lang('messages.inbox.pre_approve') / @lang('messages.your_reservations.decline')
                      </a>
                      @endif
                  @endif
                  <button type="button" class="btn btn-primary w-auto ml-2" ng-click="reply_message('non_special_offer_form')">
                    @lang('messages.your_reservations.send_message')
                  </button>
                </div>
              </form>

              <div class="card inquiry-form-fields d-none">
                <div class="card-header">
                  <div class="row">
                    <div class="col-12 col-md-8 text-center text-md-left">
                      <h4>
                        {{ $messages[0]->reservation->space->name }}
                      </h4>
                      <p>
                        {{ $messages[0]->reservation->dates_subject }} ({{ $messages[0]->reservation->hours }} {{ trans_choice('messages.rooms.night',1) }}{{ ($messages[0]->reservation->hours > 1) ? 's' : '' }})
                        ·
                        {{ $messages[0]->reservation->number_of_guests }} {{ trans_choice('messages.home.guest',$messages[0]->reservation->number_of_guests) }}
                      </p>
                    </div>
                    <div class="price-info col-12 col-md-4 mt-3 mt-md-0 text-center text-md-right">
                      <h2>
                        <sup class="h5">
                          {{ html_string($messages[0]->reservation->currency->symbol) }}
                        </sup>
                        {{ $messages[0]->reservation->subtotal - $messages[0]->reservation->host_fee }}
                      </h2>
                    </div>
                  </div>
                </div>

                <div class="card-body host-decide">
                  <ul class="option-list" ng-init="last_message_id='{{$messages[0]->id}}'">
                    <li data-tracking-section="accept" class="positive">
                      <a class="option-link theme-link" href="javascript:void(0);">
                        {{ trans('messages.inbox.allow_guest_book') }}
                      </a>
                      <form class="message_form positive" id="allow_guest">
                        <input type="hidden" value="{{ $messages[0]->reservation_id }}" name="inquiry_post_id">
                        <ul class="mb-4 d-none">
                          @if(@$messages[0]->reservation->booked_reservation)
                          <li data-key="pre-approve" class="mb-2">
                            <hr>
                            <label class="d-flex align-items-center">
                              <input type="radio" value="1" name="template">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.pre_approve_book',['first_name'=>$messages[0]->reservation->users->first_name]) }}
                              </strong>
                            </label>
                            <div class="textarea-field mt-2">
                              <div class="drawer">
                                <p class="description mb-3">
                                  {{ trans('messages.inbox.pre_approve_desc',['first_name'=>$messages[0]->reservation->users->first_name]) }}
                                </p>
                                <textarea placeholder="{{ trans('messages.inbox.include_msg',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                                <div class="mt-2 text-right">
                                  <input type="submit" value="{{ trans('messages.inbox.pre_approve') }}" class="btn btn-primary w-auto" ng-click="reply_message('pre-approve')">
                                </div>
                              </div>
                            </div>
                          </li>
                          @endif

                          <li data-key="special_offer" class="active">
                            <hr>
                            <label>
                              <input type="radio" value="2" name="template">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.send_a_special_offer',['first_name'=>$messages[0]->reservation->users->first_name]) }}
                              </strong>
                            </label>

                            <div class="textarea-field">
                              <div class="drawer d-none">
                                <p class="description mb-3">
                                  {{ trans('messages.inbox.special_offer_desc',['first_name'=>$messages[0]->reservation->users->first_name]) }}
                                </p>
                                <fieldset class="available-special-offer my-3" ng-init="booking_period='{{ $booking_period}}';number_of_guests={{ $guests }};activity_type_selected='{{ $activity_type_selected }}';times_array={{ json_encode($times_array) }};booking_date_times = {{ json_encode($booking_date_times) }};msg_booking_date_times = {{ json_encode($booking_date_times) }};activities={{ $activities }};minimum_amount={{ $minimum_amount }}">
                                  {!! Form::hidden('event_type','',['ng-value' => 'hidden_event_type']) !!}
                                  {!! Form::hidden('booking_date_times','',['ng-value' => 'hidden_date_times']) !!}
                                  {!! Form::hidden('booking_period','',['ng-value' => 'booking_period']) !!}
                                  {!! Form::hidden('currency',session('currency')) !!}

                                  <label for="pricing_space_id">
                                    {{ trans('messages.lys.listing') }}
                                  </label>
                                  <div class="select mt-2">
                                    {!! Form::select('pricing[hosting_id]', $space_unlist, $messages[0]->reservation->space_id, ['id'=>'pricing_space_id', 'ng-model' => 'space_id', 'ng-change' => "changeSpace();"]); !!}
                                  </div>

                                  <div class="special-offer-date-fields my-3">

                                    <div class="form-group start_date-container">
                                      <label for="event_type"> @lang('messages.space_detail.event_type') </label>
                                      <select class="select-picker form-control event_type" ng-model="activity_type" data-live-search="true" ng-change="calculateSpecialOffer();">
                                        <option value="" disabled> @lang('messages.lys.select') </option>
                                        
                                        <optgroup label="@{{ activity.name }}" data-subtext="in @{{ activity.activity_type_name }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length > 0">
                                          <option value="@{{subactivity.id}}" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ subactivity.activity_id }}" ng-repeat="subactivity in activity.sub_activities"> @{{ subactivity.name }} </option>
                                        </optgroup>

                                        <option value="0" data-activity_type ="@{{ activity.activity_type_id }}" data-activity ="@{{ activity.id }}" ng-repeat="activity in activities" ng-if="activity.sub_activities.length == 0" data-subtext="in @{{ activity.activity_type_name }}"> @{{ activity.name }}</option>
                                      </select>
                                    </div>
                                    <div class="booking-details">
                                      <div class="form-row">
                                        <div class="form-group col-md-6">
                                          <label for="pricing_start_date"> @lang('messages.space_detail.checkin') </label>
                                          <input type="text" class="form-control start_date" id="pricing_start_date" ng-model="booking_date_times.start_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly ng-change="calculateSpecialOffer();">
                                        </div>
                                        <div class="form-group col-md-6" ng-show="booking_period == 'Single'">
                                        </div>
                                        <div class="form-group col-md-6">
                                          <label for="pricing_start_time"> @lang('messages.space_detail.start_time') </label>
                                          <select class="custom-select form-control" ng-model="booking_date_times.start_time" ng-change="calculateSpecialOffer();">
                                            <option value="" disabled> @lang('messages.space_detail.start_time') </option>
                                            <option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-if="key != '23:59:00'" ng-hide="not_available_times[booking_date_times.start_week_day].indexOf(key) >= 0" ng-disabled="blocked_times[formatted_date].indexOf(key) >= 0"> @{{value}} </option>
                                          </select>
                                        </div>
                                        <div class="form-group col-md-6" ng-hide="booking_period == 'Single'">
                                          <label for="pricing_end_date"> @lang('messages.space_detail.checkout') </label>
                                          <input type="text" class="form-control start_date" id="pricing_end_date" ng-model="booking_date_times.end_date" placeholder="{{ strtoupper(DISPLAY_DATE_FORMAT) }}" readonly ng-change="calculateSpecialOffer();">
                                        </div>
                                        <div class="form-group col-md-6">
                                          <label for="pricing_end_time"> @lang('messages.space_detail.end_time') </label>
                                          <select class="custom-select form-control" ng-model="booking_date_times.end_time" ng-change="calculateSpecialOffer();">
                                            <option value="" disabled> @lang('messages.space_detail.end_time') </option>
                                            <option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-hide="not_available_times[booking_date_times.end_week_day].indexOf(key) >= 0 || (booking_period == 'Single' && booking_date_times.start_time >= key)" ng-disabled="blocked_times[formatted_date].indexOf(key) >= 0"> @{{value}} </option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <a href="javascript:;" class="theme-link" ng-click="switchDayType($event);"> 
                                        <span ng-show="booking_period == 'Multiple'"> @lang('messages.space_detail.single_day') </span>
                                        <span ng-show="booking_period == 'Single'"> @lang('messages.space_detail.mulitple_day') </span>
                                      </a>
                                    </div>

                                    <div class="form-group">
                                      <label for="num_guests"> @lang('messages.space_detail.guests') </label>
                                      <input name="number_of_guests" type="number" class="form-control num_guests" id="list_guests" placeholder="@lang('messages.space_detail.guests')" ng-model="number_of_guests" min="0" max="$max_guest_limit" ng-change="calculateSpecialOffer();">
                                    </div>

                                    <div class="form-group">
                                      <label for="num_guests"> @lang('messages.inbox.price') </label>
                                      <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text" id="basic-addon1">{{ html_string($messages[0]->reservation->currency->symbol) }}</span>
                                        </div>
                                        <input type="number" name="pricing[price]" id="pricing_price" class="form-control" min="0" ng-model="price">
                                      </div>
                                      <span class="my-2"> @lang('messages.inbox.price_include_additional_fees') </span>
                                    </div>
                                  </div>

                                </fieldset>

                                <textarea class="form-group" placeholder="{{ trans('messages.inbox.include_msg',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message" ng-model="message"></textarea>

                                <div id="availability_warning" class="alert alert-info" ng-show='availability_error'>
                                  <i class="icon alert-icon icon-comment"></i>
                                  <span ng-show="not_available">
                                    @lang('messages.inbox.already_marked_dates')
                                  </span>
                                  <span ng-hide="not_available">
                                    @{{ availability_error_message }}
                                  </span>
                                </div>
                                <span class="text-danger mt-1" ng-show='contact_error'>
                                  @lang('messages.your_trips.please_fill_the_details')
                                </span>

                                <div class="mt-2 text-right">
                                  <input type="submit" value="{{ trans('messages.inbox.send_offer') }}" class="btn btn-primary w-auto" ng-click="calculateSpecialOffer(true)">
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </form>       
                    </li>

                    @if($messages[0]->reservation->status != 'Accepted' && $messages[0]->reservation->status != 'Declined' && $messages[0]->reservation->status != 'Cancelled')
                    <li data-tracking-section="decline" class="negative">
                      <a class="option-link theme-link" href="javascript:void(0);">
                        {{ trans('messages.inbox.tell_listing_unavailable') }}
                      </a>
                      <form class="message_form negative" id="decline">
                        <input type="hidden" value="" name="inquiry_post_id">
                        <ul class="d-none">
                          <li>
                            <br>
                            <p class="font-weight-bold green-color">
                              {{trans('messages.inbox.host_msg_note',['site_name'=>SITE_NAME])}}
                            </p>
                          </li>
                          <li data-key="dates_not_available">
                            <hr>
                            <label>
                              <input type="radio" value="NOT_AVAILABLE" name="template" data-message="Dates are not available">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.dates_not_available_block',['dates'=>$messages[0]->reservation->dates_subject]) }}
                              </strong>
                            </label>
                            <div class="textarea-field">
                              <div class="drawer d-none">
                                <p class="description mb-3">
                                  {{ trans('messages.inbox.calc_marked_unavailable',['dates'=>$messages[0]->reservation->dates_subject]) }}
                                </p>
                                <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                                <p class="text-danger message_error_box d-none">
                                {{trans('messages.reviews.this_field_is_required')}}</p>
                                <div class="mt-2 text-right">
                                  <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('dates_not_available')">
                                </div>
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="not_comfortable">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="I do not feel comfortable with this guest">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.donot_feel_comfortable') }}
                              </strong>
                            </label>
                            <div class="textarea-field">
                              <div class="drawer d-none">
                                <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                                <p class="text-danger message_error_box d-none">
                                {{trans('messages.reviews.this_field_is_required')}}</p>
                                <div class="mt-2 text-right">
                                  <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('not_comfortable')">
                                </div>
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="not_a_good_fit" class="template_9">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="My listing is not a good fit for the guest’s needs (children, pets, etc.)">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.listing_not_good_fit') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                              <p class="text-danger message_error_box d-none">
                                {{trans('messages.reviews.this_field_is_required')}}
                              </p>
                              <div class="mt-2 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('not_a_good_fit')">
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="waiting_for_better_reservation" class="template_9">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="I’m waiting for a more attractive reservation">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.waiting_attractive_reservation') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                              <p class="text-danger message_error_box d-none">
                              {{trans('messages.reviews.this_field_is_required')}}</p>
                              <div class="mt-2 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('waiting_for_better_reservation')">
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="different_dates_than_selected" class="template_9">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="The guest is asking for different dates than the ones selected in this request">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.guest_asking_different_dates') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                              <p class="text-danger message_error_box d-none">
                              {{trans('messages.reviews.this_field_is_required')}}</p>
                              <div class="mt-2 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('different_dates_than_selected')">
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="spam" class="template_9">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="This message is Spam">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.msg_is_spam') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                              <p class="text-danger message_error_box d-none">
                              {{trans('messages.reviews.this_field_is_required')}}</p>
                              <div class="mt-3 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('spam')">
                              </div>
                            </div>
                          </li>
                          <!-- 9 -->
                          <li data-key="other" class="template_9">
                            <hr>
                            <label>
                              <input type="radio" value="9" name="template" data-message="Other">                                    
                              <strong class="d-inline-block">
                                {{ trans('messages.profile.other') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea placeholder="{{ trans('messages.inbox.send_msg_user',['first_name'=>$messages[0]->reservation->users->first_name]) }}" name="message"></textarea>
                              <p class="text-danger message_error_box d-none">
                              {{trans('messages.reviews.this_field_is_required')}}</p>
                              <div class="mt-3 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('other')">
                              </div>
                            </div>
                          </li>
                        </ul>
                      </form>        
                    </li>
                    @endif
                    <li data-tracking-section="discussion" class="neutral d-none">
                      <a class="option-link theme-link" href="javascript:void(0);">
                        {{ trans('messages.inbox.write_back_to_learn') }}
                      </a>
                      <form class="message_form neutral" id="discussion">
                        <input type="hidden" value="" name="inquiry_post_id">
                        <ul class="d-none">
                          <!-- 7 -->
                          <li data-key="discussion" class="template_7" data-message="Dates are not available">
                            <hr>
                            <label>
                              <input type="radio" value="7" name="template">
                              <strong class="d-inline-block">
                                {{ trans('messages.inbox.need_answer_question') }}
                              </strong>
                            </label>
                            <div class="textarea-field drawer d-none">
                              <textarea class="required" placeholder="{{ trans('messages.inbox.only_guest_see_msg') }}" name="message"></textarea>
                              <div class="mt-3 text-right">
                                <input type="submit" value="{{ trans('messages.inbox.send') }}" class="btn btn-primary w-auto" ng-click="reply_message('discussion')">
                              </div>
                            </div>
                          </li>
                        </ul>
                      </form>       
                    </li>
                  </ul>
                </div>
              </div>
            </li>

            @for($i=0; $i < count($messages); $i++)
            @if($messages[$i]->user_from == Auth::user()->id)
            <li id="question2_post_{{ $messages[$i]->id }}" class="translate">
              @if($messages[$i]->message_type == 7)
              <div class="card my-4">
                <div class="card-header">
                  <span class="label label-info">
                    {{ trans('messages.inbox.special_offer') }}
                  </span>
                  <h5>
                    {{ $messages[$i]->reservation->users->first_name }} {{ trans('messages.inbox.pre_approved_stay_at') }} 
                    <a href="{{ route('space_details',[$messages[$i]->special_offer->space_id]) }}">
                      {{ $messages[$i]->special_offer->space->name }}
                    </a>
                  </h5>
                  <p class="m-0">
                    {{ $messages[$i]->special_offer->dates_subject }}
                    <span class="ml-2">
                      ·
                      {{ $messages[$i]->special_offer->number_of_guests }} {{ trans_choice('messages.home.guest',$messages[$i]->special_offer->number_of_guests) }}
                    </span>
                    <br>
                    <strong>
                      {{ trans('messages.inbox.you_could_earn') }} {{ html_entity_decode($messages[$i]->special_offer->currency->symbol).$messages[$i]->special_offer->price }} {{ $messages[$i]->special_offer->currency->session_code }}
                    </strong> 
                    ({{ trans('messages.inbox.once_reservation_made') }})
                  </p>
                </div>
                @if(@$messages[$i]->special_offer->is_booked)
                <div class="card-body">
                  <a href="{{ route('remove_special_offer',['id' => $messages[$i]->special_offer_id]) }}" class="btn" data-confirm="Are you sure?" data-method="post" rel="nofollow">
                    {{ trans('messages.inbox.remove_special_offer') }}
                  </a>
                </div>
                @endif
              </div>
              @endif

              @if($messages[$i]->message_type == 6)
              <div class="card my-4">
                <div class="card-header">
                  <h5>
                    {{ $messages[$i]->reservation->users->first_name }} {{ trans('messages.inbox.pre_approved_stay_at') }} 
                    <a href="{{ route('space_details',[$messages[$i]->reservation->space_id]) }}">
                      {{ $messages[$i]->special_offer->space->name }}
                    </a>
                  </h5>
                  <p class="m-0">
                    {{ $messages[$i]->special_offer->dates_subject }}
                    <span class="mx-2">
                      ·
                      {{ $messages[$i]->special_offer->number_of_guests }} {{ trans_choice('messages.home.guest',$messages[$i]->special_offer->number_of_guests) }}
                      ·
                    </span>
                    {{ html_entity_decode($messages[$i]->special_offer->currency->symbol).($messages[$i]->special_offer->price - $messages[$i]->reservation->host_fee) }} {{ $messages[$i]->special_offer->currency->session_code }}
                  </p>
                </div>
                @if(@$messages[$i]->special_offer->is_booked)
                <div class="card-body">
                  <a href="{{ route('remove_special_offer',['id' => $messages[$i]->special_offer_id]) }}" class="btn" data-confirm="Are you sure?" data-method="post" rel="nofollow">
                    {{ trans('messages.inbox.remove_pre_approval') }}
                  </a>
                </div>
                @endif
              </div>
              @endif

              <div class="row my-4">
                <div class="col-3 col-md-2 pr-0 text-center">
                  <a aria-label="{{ $messages[$i]->reservation->space->users->first_name }}" data-behavior="tooltip" href="{{ route('show_profile',[$messages[$i]->reservation->host_id]) }}">
                    <img title="{{ $messages[$i]->reservation->space->users->first_name }}" src="{{ $messages[$i]->reservation->space->users->profile_picture->src }}" alt="{{ $messages[$i]->reservation->space->users->first_name }}">
                  </a>
                </div>
                <div class="col-9 col-md-10">
                  <div class="card custom-arrow left">
                    <div class="card-body p-3">
                      <p>
                        {{ $messages[$i]->message }}
                      </p>
                    </div>
                  </div>
                  <div class="time-container">
                    <small title="{{ $messages[$i]->created_at }}" class="time">
                      {{ $messages[$i]->created_time }}
                    </small>
                    <small class="exact-time d-none">
                      {{ $messages[$i]->created_at }}
                    </small>
                  </div>
                </div>
              </div>
            </li>
            @endif

            @if($messages[$i]->user_from != Auth::user()->id)
            <li id="question2_post_{{ $messages[$i]->id }}" class="translate">
              @if(($messages[$i]->message_type == 1 || $messages[$i]->message_type == 9) && $messages[$i]->reservation->list_type != 'Experiences')
              <div class="card">
                <div class="card-header">
                  <h5>
                    {{ trans('messages.inbox.inquiry_about') }} 
                    <a locale="en" data-popup="true" href="{{ route('space_details',[$messages[$i]->reservation->space_id]) }}" class="theme-link">
                      {{ $messages[$i]->reservation->space->name }}
                    </a>
                  </h5>
                  <p class="m-0">
                    {{ $messages[$i]->reservation->dates_subject }}
                    <span class="ml-2">
                      ·
                      {{ $messages[$i]->reservation->number_of_guests }} {{ trans_choice('messages.home.guest',$messages[$i]->reservation->number_of_guests) }}
                    </span>
                    <br>
                    {{ trans('messages.inbox.you_will_earn') }} {{ html_entity_decode($messages[$i]->reservation->currency->symbol).$messages[$i]->reservation->host_payout }} {{ $messages[$i]->reservation->currency->code }}
                  </p>
                </div>
              </div>
              @endif
              @if($messages[$i]->message_type == 10)
              <div class="inline-status">
                <div class="horizontal-rule-text">
                  <span class="horizontal-rule-wrapper">
                    <span>
                      {{ trans('messages.inbox.reservation_declined') }} 
                    </span>
                    <span>
                      {{ $messages[$i]->created_time }}
                    </span>
                  </span>
                </div>
              </div>
              @endif

              <div class="row my-4">
                <div class="col-9 col-md-10">
                  <div class="card custom-arrow right">
                    <div class="card-body p-3">
                      <p>
                        {{ $messages[$i]->message }}
                      </p>
                    </div>
                  </div>
                  <div class="time-container text-right">
                    <small title="{{ $messages[$i]->created_at }}" class="time">
                      {{ $messages[$i]->created_time }}
                    </small>
                    <small class="exact-time d-none">
                      {{ $messages[$i]->created_at }}
                    </small>
                  </div>
                </div>

                <div class="col-3 col-md-2 pl-0 text-center">
                  <a aria-label="{{ $messages[$i]->reservation->users->first_name }}" data-behavior="tooltip" href="{{ route('show_profile',[$messages[$i]->reservation->user_id]) }}">
                    <img title="{{ $messages[$i]->reservation->users->first_name }}" src="{{ $messages[$i]->reservation->users->profile_picture->src }}" alt="{{ $messages[$i]->reservation->users->first_name }}">
                  </a>
                </div>
              </div>
            </li>
            @endif
            @endfor
          </ul>
        </div>

        <div class="col-12 col-md-5 col-lg-4 coversation-right">
          <div class="card">
            <div class="mini-profile d-flex">
              <div class="profile-img col-4 p-0">
                <a href="{{ route('show_profile',[$messages[0]->reservation->user_id]) }}">
                  <img alt="{{ $messages[0]->reservation->users->first_name }}" src="{{ $messages[0]->reservation->users->profile_picture->src }}">
                </a>
              </div>

              <div class="mini-profile-info col-8 my-2">
                <h4 class="text-truncate">
                  <a href="{{ route('show_profile',[$messages[0]->reservation->user_id]) }}">
                    {{ $messages[0]->reservation->users->first_name }}
                  </a>
                </h4>
                <span>
                  {{ $messages[0]->reservation->users->live }}
                </span>
                <span>
                  {{ trans('messages.profile.member_since') }} {{ @$messages[0]->reservation->users->since }}
                </span>
              </div>
            </div>

            @if($messages[0]->reservation->users->users_verification->show() || $messages[0]->reservation->users->verification_status == 'Verified')
            <div class="verification-panel">
              <div class="card-header">
                {{ trans('messages.dashboard.verifications') }}
              </div>
              <div class="card-body">
                <ul>
                  @if($messages[0]->reservation->users->verification_status == 'Verified')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        {{ trans('messages.dashboard.id_verification') }}
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.verified') }}
                      </p>
                    </div>
                  </li>
                  @endif 
                  @if($messages[0]->reservation->users->users_verification->email == 'yes')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        {{ trans('messages.dashboard.email_address') }}
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.verified') }}
                      </p>
                    </div>
                  </li>
                  @endif
                  @if($messages[0]->reservation->users->users_verification->phone_number == 'yes')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        {{ trans('messages.profile.phone_number') }}
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.verified') }}
                      </p>
                    </div>
                  </li>
                  @endif
                  @if($messages[0]->reservation->users->users_verification->facebook == 'yes')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        Facebook
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.validated') }}
                      </p>
                    </div>
                  </li>
                  @endif
                  @if($messages[0]->reservation->users->users_verification->google == 'yes')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        Google
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.validated') }}
                      </p>
                    </div>
                  </li>
                  @endif
                  @if($messages[0]->reservation->users->users_verification->linkedin == 'yes')
                  <li>
                    <i class="icon icon-ok mr-2"></i>
                    <div class="media-body">
                      <h5>
                        LinkedIn
                      </h5>
                      <p>
                        {{ trans('messages.dashboard.validated') }}
                      </p>
                    </div>
                  </li>
                  @endif
                </ul>
              </div>
            </div>
            @endif
          </div>

          <div class="select my-3" ng-init="calendar_data={{ json_encode($calendar_data) }}">
            {!! Form::select('hosting', $space, '', ['ng-model'=>'space_id']); !!}
          </div>

          <div id="calendar" class="small-calendar my-2"></div>

          <a class="theme-link" href="{{ $edit_calendar_link }}" id="edit_calendar_url" data-type="{{$messages[0]->reservation->list_type}}">
            {{ trans('messages.inbox.full_calc_edit') }}
          </a>

          <div class="payment-info card my-4">
            <div class="card-header">
              <h5>
                {{ trans('messages.inbox.protect_your_payments') }}
              </h5>
            </div>
            <div class="card-body">
              <p>
                {{ trans('messages.inbox.never_pay_outside',['site_name'=>$site_name]) }}
              </p>
              <p>
                {{ trans('messages.inbox.protect_your_payments_desc',['site_name'=>$site_name]) }}
              </p>
            </div>
          </div>

          <div class="contact-info card my-4">
            <div class="card-header">
              <h5>
                {{ trans('messages.inbox.contact_info') }}
              </h5>
            </div>
            <div class="card-body">
              <p>
                {{ trans('messages.inbox.contact_info_desc') }}
              </p>
            </div>
          </div>
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
//new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
new google.translate.TranslateElement('google_translate_element');
}
function google_translate() {
  var language = $('#page_lang').val();
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