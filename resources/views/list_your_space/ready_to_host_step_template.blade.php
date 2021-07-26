@section('activities')
<div class="content-heading my-4" ng-init="space_activities = {{ ($result->space_activities) ? $result->space_activities : '{}' }};">
  <h3> @lang('messages.ready_to_host.activities_in_your_space') </h3>
  <p> @lang('messages.ready_to_host.activities_desc') </p>
</div>
<hr>
<div class="mb-3 pb-3">
  <div class="panel-group">
    <input type="hidden" name="activity_type" ng-model="space_activities.activity_type_id" ng-value="space_activities.activity_type_id">
    <input type="hidden" name="activity" ng-model="space_activities.activity_id" ng-value="space_activities.activity_id">
    <input type="hidden" name="sub_activity" ng-model="space_activities.sub_activity_id" ng-value="space_activities.sub_activity_id">
    @foreach($activity_types as $activity_type)
    <div class="panel panel-default mt-2">
      <a class=" w-100 activity-header" data-toggle="collapse" href="#activity_type_{{ $activity_type->id }}"> <i class="fa fa-{{ in_array($activity_type->id, $prev_activity_type) ? 'minus':'plus'}}"></i> {{ $activity_type->name }} </a>
      <div id="activity_type_{{ $activity_type->id }}" class="panel-collapse collapse {{ in_array($activity_type->id, $prev_activity_type) ? 'show':''}}">
        <ul class="h5 list-group">
          @foreach($activities as $activity)
          <li class="mt-2" ng-if="{{ $activity_type->id == $activity->activity_type_id}}">
            <div class="form-check">
              <input type="checkbox" class="mt-1 form-check-input activities" id="activity_{{$activity->id}}" value="{{$activity->id}}" data-activity_type="{{ $activity_type->id }}" ng-checked="{{ in_array($activity->id, $prev_activities) }}">
              <label class="form-check-label" for="activity_{{$activity->id}}" style="font-size: 15px;"> {{ $activity->name }} </label>
              <ul class="mt-2 h5 list-group">
                @foreach($sub_activities as $subactivity)
                <li class="mt-2 h6" ng-if="{{ $activity->id == $subactivity->activity_id }}">
                  <div class="form-check">
                    <input type="checkbox" class="mt-1 form-check-input sub_activities" id="subactivity_{{ $subactivity->id}}" value="{{ $subactivity->id}}" data-activity_id="{{ $activity->id }}" ng-checked="{{ in_array($subactivity->id, $prev_sub_activities) }}">
                    <label class="form-check-label" for="subactivity_{{ $subactivity->id}}"> {{ $subactivity->name }} </label>
                  </div>
                </li>
                @endforeach
              </ul>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
@section('activity_price')
<div class="content-heading my-4" ng-init="minimum_amount={{$minimum_amount}}">
  <span class="h4"> @lang('messages.ready_to_host.set_your_price') </span>
  <p class="text-muted">
    @lang('messages.ready_to_host.minimum_hourly_price')
    <span ng-bind-html="currency_symbol"></span>@{{ minimum_amount }}
  </p>
</div>
<div class="mb-3 pb-3">
  <div class="panel-group">
    <div class="row panel panel-default mt-2" ng-repeat="activities in space.space_activities">
      <div class="col-md-12">
        <img class="img event_image-icon" ng-src="@{{ activities.activity_type.image_url }}" style="height: 30px;width: 30px;vertical-align: bottom;">
        <span class="h5"> @{{ activities.activity_type.name }} </span>
      </div>
      <div class="col-md-12">
        <div class="form-group mt-2">
          <label> @lang('messages.ready_to_host.hourly_rate') </label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" ng-bind-html="currency_symbol"></span>
            </div>
            <input type="number" name="hourly_rate" id="hourly_rate_@{{ activities.activity_price.id }}" class="form-control hourly_rate price_input" ng-model="activities.activity_price.hourly" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" maxlength = "5">
          </div>
        </div>
      </div>
 
      <div class="col-md-12">
        <div class="form-group mt-2">
          <label> @lang('messages.ready_to_host.full_day_rate')</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" ng-bind-html="currency_symbol"></span>
            </div>
            <input type="number" name="full_day_rate" id="full_day_rate_@{{ activities.activity_price.id }}" class="form-control full_day_rate price_input" ng-model="activities.activity_price.full_day" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" maxlength = "5">
          </div>
        </div>
      </div> 
        <div class="col-md-12">
        <div class="form-group mt-2">
          <label> @lang('messages.ready_to_host.weekly_rate')  </label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" ng-bind-html="currency_symbol"></span>
            </div>
            <input type="number" name="weekly_rate" id="weekly_rate_@{{ activities.activity_price.id }}" class="form-control weekly_rate price_input"  ng-model="activities.activity_price.weekly" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" maxlength = "5">
          </div>
        </div>
      </div>
        <div class="col-md-12">
        <div class="form-group mt-2">
          <label> @lang('messages.ready_to_host.monthly_rate') </label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" ng-bind-html="currency_symbol"></span>
            </div>
            <input type="number" name="monthly_rate" id="full_day_rate_@{{ activities.activity_price.id }}" class="form-control monthly_rate price_input"  ng-model="activities.activity_price.monthly" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" maxlength = "5">
          </div>
        </div>
      </div>
           <div class="col-md-4">
        <div class="form-group mt-2">
          <label>
            @lang('messages.ready_to_host.min_hours')
            <i class="fa fa-info-circle" data-html="true" data-toggle="tooltip" title="@lang('messages.ready_to_host.min_hours_tooltip')"></i>
          </label>
          <div class="input-group mb-3">
            <input type="number" number="min_hours" id="min_hours_@{{ activities.activity_price.id }}" class="form-control input-number min_hours price_input" ng-model="activities.activity_price.min_hours" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" max="4" maxlength = "5">
            <div class="input-group-append">
              <button class="btn input-group-text fa fa-minus" ng-click="updateActivityHours('decrease',$index,activities.activity_price)" ng-disabled="activities.activity_price.min_hours < 2"></button>
            </div>
            <div class="input-group-append">
              <button class="btn input-group-text fa fa-plus" ng-click="updateActivityHours('increase',$index,activities.id)" ng-disabled="activities.activity_price.min_hours > 3"></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="pricing_field my-3">
      <label>
        {{ trans('messages.account.currency') }}
      </label>
      <div id="currency-picker" class="form-select">
        {!! Form::select('currency_code',$currency, '', ['id' => 'price_currency_code','class' => 'form-control','ng-model' => 'activity_currency', 'ng-change' => 'updateFormStatus();validateActivityPriceData();']) !!}
      </div>
    </div>
  </div>
</div>
@endsection
@section('availability')
<div class="col-12 manage-listing-container d-flex" id="js-manage-listing-content-container">
  <div class="manage-listing-content col-12" id="js-manage-listing-content">
    <div class="content-heading my-4">
      <h3> @lang('messages.ready_to_host.set_availability') </h3>
    </div>
    <div class="mb-3 pb-3 d-flex align-items-center justify-content-between" ng-init="times_array={{ json_encode($times_array) }};days_array={{json_encode($days_array)}};availabilities={{$result->space_availabilities}};">
      <div class="availability-form container">
        <div ng-repeat="(avail_key,availability) in availabilities">
          <div class="mt-4 row border-bottom" ng-init="availability.removed_availability = []">
            <div class="col-md-3">
              @{{ availability.day_name }}
            </div>
            <div class="col-md-3" ng-init="availability.available = getAvailabeStatus(availability.original_status);availability.status = getAvailabilityStatus(availability.original_status);">
              <label class="switch">
                <input type="checkbox" id="availability_opt_@{{ $index }}" class="toggle_switch" ng-model="availability.status" ng-true-value="'Open'" ng-false-value="'Closed'" ng-change="availabilityChanged()">
                <span class="slider round">
                </span>
              </label>
              <label for="availability_opt_@{{ $index }}" ng-show="availability.status == 'Closed'"> @lang('messages.ready_to_host.closed') </label>
              <label for="availability_opt_@{{ $index }}" ng-show="availability.status != 'Closed'"> @lang('messages.ready_to_host.open') </label>
            </div>
            <div class="col-md-5" ng-hide="availability.status == 'Closed'">
              <label class="radio-inline">
                <input type="radio" name="available[@{{$index}}]" ng-model="availability.available" value="all" ng-change="availabilityTypeChanged(avail_key,'all')"> @lang('messages.ready_to_host.all_hours')
              </label>
              <label class="radio-inline">
                <input type="radio" name="available[@{{$index}}]" ng-model="availability.available" value="set_hours" ng-change="availabilityTypeChanged(avail_key,'set_hours')"> @lang('messages.ready_to_host.set_hours')
              </label>
            </div>
          </div>
          <div class="container mt-2" ng-hide="availability.status == 'Closed'">
            <div class="row mt-4 " ng-repeat="avail_hours in availability.availability_times">
              <div class="col-md-5">
                <select name="avail_hours[start_time]" class="select form-control" ng-model="avail_hours.start_time" ng-change="availabilityChanged();startTimeChanged(avail_key,$index)">
                  <option value="" disabled> @lang('messages.lys.select') </option>
                  <option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-selected="key == avail_hours.start_time"> @{{value}} </option>
                </select>
              </div>
              <div class="col-md-5">
                <select name="avail_hours[end_time]" class="select form-control" ng-model="avail_hours.end_time" ng-change="availabilityChanged()">
                  <option value="" disabled> @lang('messages.lys.select') </option>
                  <option ng-repeat="(key, value) in times_array" value="@{{ key }}" ng-selected="key == avail_hours.end_time" ng-show="key > avail_hours.start_time"> @{{value}} </option>
                  <option value="23:59:00" ng-selected="'23:59:00' == avail_hours.end_time" ng-show="avail_hours.start_time != null"> 11:59 PM </option>
                </select>
              </div>
              <div class="col-md-2">
                <a class="btn btn-default" ng-hide="availability.availability_times.length == 1" ng-click="removeAvailabilityHours(avail_key,$index)"> <span class="icon icon-trash"></span> </a>
                <a class="btn btn-default mt-2" ng-show="(availability.availability_times.length - 1) == $index" ng-click="addAvailabilityHours(avail_key)"> <span class="icon icon-add"></span> </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('cancellation')
<div class="content-heading my-4">
  <h3> @lang('messages.ready_to_host.set_cancellation_policy') </h3>
</div>
<div class="mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="form-group w-100">
    <select name="cancellation_policy" ng-model="space.cancellation_policy">
      <option value="Flexible"> {{ trans('messages.lys.flexible_desc') }} </option>
      <option value="Moderate"> {{ trans('messages.lys.moderate_desc') }} </option>
      <option value="Strict"> {{ trans('messages.lys.strict_desc') }} </option>
    </select>
  </div>
</div>
@endsection
@section('booking_type')
<div class="content-heading my-4">
  <h3> @lang('messages.ready_to_host.booking_type') </h3>
</div>
<div class="mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="form-group w-100" >
    <select name="cancellation_policy" ng-model="space.booking_type">
      <option value="instant_book"> {{ trans('messages.ready_to_host.instant_book')}}</option>
      <option value="request_to_book"> {{ trans('messages.ready_to_host.request_to_book') }} </option>
    </select>
  </div>
</div>
@endsection
@section('security')
<div class="content-heading my-4">
  <h3> @lang('messages.ready_to_host.security_deposit') </h3>
</div>
<div class="mb-3 pb-3">
  <div class="panel-group">
    <div class="panel panel-default mt-2">
      <label> @lang('messages.ready_to_host.security_deposit') </label>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <button class="btn input-group-text"> @{{ activity_currency }} </button>
        </div>
        <input type="number" name="security_deposit" class="form-control input-number security_deposit" min="0" ng-model="space.space_price.security" ng-change="updateFormStatus();">
      </div>
    </div>
  </div>
</div>
@endsection
@section('calendar')
@include('list_your_space.calendar')
@endsection
@section('activities_desc')
<h1>
<img src="@asset(images/list_space/cancellation_policy.png)" alt="">
</h1>
<h3>
<span class="font-weight-bold"> @lang('messages.steps.activities_desc1') </span> @lang('messages.steps.activities_desc2')
</h3>
@endsection
@section('activity_price_desc')
<h1>
<img src="@asset(images/list_space/security_deposit.png)" alt="">
</h1>
<h3>
<span class="font-weight-bold"> @lang('messages.steps.activity_price_desc1') </span>
@lang('messages.steps.activity_price_desc2')
</h3>
@endsection
@section('availability_desc')
<h1>
<img src="@asset(images/list_space/activities_price.png)" alt="">
</h1>
<h3>
<span class="font-weight-bold"> @lang('messages.steps.availability_desc1') </span>
</h3>
@endsection
@section('cancellation_desc')
<h1>
<img src="@asset(images/list_space/security_deposit.png)" alt="">
</h1>
<h3>
<span class="font-weight-bold"> @lang('messages.steps.cancellation_desc1') </span> @lang('messages.steps.cancellation_desc2',['site_name' => $site_name])
</h3>
@endsection