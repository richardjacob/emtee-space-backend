@section('what_kind_of_space')
<div class="form-group">
  <label> @lang('messages.new_space.what_kind_of_space') <sub class="text-danger"> * </sub> </label>
  <select name="space_type" class="form-control cls_arrow_select" ng-model="space.space_type" ng-change="validateStepData('basics','space_type')">
    <option value="0" disabled> @lang('messages.lys.select') </option>
    @foreach($space_types as $type)
    <option value="{{ $type->id }}"> {{ $type->name }} </option>
    @endforeach
  </select>
</div>
@endsection

@section('number_of_rooms')
<div class="form-group">
  <div class="form-group">
  <label>  @lang('messages.basics.is_your_space_fully_furnished') <sub class="text-danger"> * </sub> </label>
  <br>
    <input type="radio" name="fully_furnished" class="form-control" ng-model='space.fully_furnished' value='Yes' ng-click="validateStepData('basics','sq_ft')"> 
    <span class="mr-3">@lang('messages.reviews.yes')</span>
    <input type="radio" name="fully_furnished" class="form-control" ng-model='space.fully_furnished' value='No' ng-click="validateStepData('basics','sq_ft')">
    <span>@lang('messages.reviews.no')</span>
</div> 
<div class="form-group">
  <label>   @lang('messages.basics.how_many_workstations') <sub class="text-danger"> * </sub> </label>
    <input type="number" name="workstations_result" class="form-control" ng-model="space.no_of_workstations" ng-change="validateStepData('basics','sq_ft')"  min="0">

</div> 
<div class="form-group">
  <label>  @lang('messages.basics.are_they_shared_or_private') <sub class="text-danger"> * </sub> </label>
  <br>
     <input type="radio" name="shared_or_private" class="form-control" ng-model="space.shared_or_private" value="Yes" ng-click="validateStepData('basics','sq_ft')">
     <span class="mr-3">@lang('messages.reviews.shared')</span>
    <input type="radio" name="shared_or_private" class="form-control" ng-model="space.shared_or_private" value="No" ng-click="validateStepData('basics','sq_ft')">
    <span>@lang('messages.reviews.private')</span>
</div> 
<div class="form-group">
  <label>  @lang('messages.basics.is_this_your_first_time_of_renting_space',['site_name' => $site_name]) <sub class="text-danger"> * </sub> </label>
  <br>
   <input type="radio" name="renting_space_firsttime" class="form-control"  ng-model="space.renting_space_firsttime" value="Yes" ng-click="validateStepData('basics','sq_ft')">
   <span class="mr-3">@lang('messages.new_trans.Im_new_to_this')</span>
    <input type="radio" name="renting_space_firsttime" class="form-control"  ng-model="space.renting_space_firsttime" value="No" ng-click="validateStepData('basics','sq_ft')">
    <span>@lang('messages.new_trans.i_have')</span>
</div>
 <!--  <label> @lang('messages.basics.floor_number') @lang('messages.basics.if_applicable') </label>
  <input type="number" name="floor_number" class="form-control" ng-model='space.floor_number' min="0"> -->
</div>
<div class="form-group">
  <label> @lang('messages.basics.estimated_sqft') <sub class="text-danger"> * </sub> </label>
  <div class="input-group">
    <input type="number" name="sq_ft" class="form-control" ng-model='space.sq_ft' ng-change="validateStepData('basics','sq_ft')" min="0" step="any">
    <div class="input-group-append">
      <select name="size_type" class="form-control custom-select cls_custom_select" ng-model="space.size_type" ng-change="validateStepData('basics','space_type')">
        <option value="sq_ft"> @lang('messages.space_detail.sq_ft') </option>
        <option value="acre"> @lang('messages.space_detail.acre') </option>
      </select>
    </div>
  </div>
</div>
@endsection

@section('guest_access')
<div class="form-group">
 <!--  <label>
    @lang('messages.basics.how_guest_can_access') <sub class="text-danger"> * </sub>
  </label> -->
  <input type="hidden" name="guest_access" ng-model="space.guest_access">
  <ul class="mt-3">
    @foreach($guest_accesses as $guest_access)
    <li>
      <input type="checkbox" id="guest_access_{{$guest_access->id}}" class="guest_access" value="{{ $guest_access->id }}" ng-checked="{{ in_array($guest_access->id, $prev_guest_access) }}">
      <label for="guest_access_{{$guest_access->id}}"> {{ $guest_access->name }} </label>
    </li>
    @endforeach
  </ul>
</div>
@endsection

@section('maximum_guests')
<div class="form-group">
  <label> @lang('messages.new_space.maximum_guests') <sub class="text-danger"> * </sub> </label>
  <div class="input-group mb-3">
    <input type="number" name="number_of_guests" class="form-control" ng-model='space.number_of_guests' min="1" max="{{$max_guest_limit}}" ng-change="validateStepData('basics','number_of_guests')">
    <div class="input-group-append">
      <button class="btn input-group-text fa fa-minus" ng-click="decrement(space.number_of_guests);validateStepData('basics','number_of_guests')" ng-disabled="space.number_of_guests < 2"></button>
    </div>
    <div class="input-group-append">
      <button class="btn input-group-text fa fa-plus" ng-click="space.number_of_guests = space.number_of_guests+1;validateStepData('basics','number_of_guests')"></button>
    </div>
  </div>
</div>
@endsection

@section('amenities')
<div class="form-group">
  <h3 class="ameniti_head"> @lang('messages.basics.what_amenities_offer') </h3>
  <ul class="mt-3">
    @foreach($amenities as $amenity)
    <li>
      <input type="checkbox" id="amenity_{{$amenity->id}}" class="amenities" value="{{ $amenity->id }}" ng-checked="{{ in_array($amenity->id, $prev_amenities) }}">
      <label for="amenity_{{$amenity->id}}"> {{ $amenity->name }} </label>
    </li>
    @endforeach
  </ul>
</div>
@endsection

@section('services')
<div class="form-group">
  <label style="font-size: 22px;font-weight: bold;color: #000;"> @lang('messages.basics.what_services_offer') </label>
  <ul class="mt-3">
    @foreach($services as $service)
    <li>
      <input type="checkbox" id="service_{{$service->id}}" class="services" value="{{ $service->id }}" ng-checked="{{ in_array($service->id, $prev_services) }}">
      <label for="service_{{$service->id}}"> {{ $service->name }} </label>
    </li>
    @endforeach
    <li>
      <label for="services_extra" class="font-weight-bold"> @lang('messages.basics.additional_services') </label>
      <textarea name="services_extra" id="services_extra" class="form-control" ng-model="space.services_extra" maxlength="500"></textarea>
    </li>
  </ul>
</div>
@endsection

@section('location')
<!-- <div class="content-heading my-4">
  <h3> @lang('messages.location.address_of_space') </h3>
</div> -->
<div class="mb-3 pb-3">
  {!! Form::open(['name' => 'enter_address', 'id' => 'js-address-fields-form']) !!}
  {!! Form::hidden('address_line1', '', ['id' => 'address_line1', 'ng-value' => 'space.space_address.address_line_1']) !!}
  {!! Form::hidden('address_line2', '', ['id' => 'address_line2', 'ng-value' => 'space.space_address.address_line_2']) !!}
  {!! Form::hidden('city', '', ['id' => 'city', 'ng-value' => 'space.space_address.city']) !!}
  {!! Form::hidden('state', '', ['id' => 'state', 'ng-value' => 'space.space_address.state']) !!}
  {!! Form::hidden('country', '', ['id' => 'country', 'ng-value' => 'space.space_address.country']) !!}
  {!! Form::hidden('postal_code', '', ['id' => 'postal_code', 'ng-value' => 'space.space_address.postal_code']) !!}
  {!! Form::hidden('latitude', '', ['id' => 'latitude', 'ng-value' => 'space.space_address.latitude']) !!}
  {!! Form::hidden('longitude', '', ['id' => 'longitude', 'ng-value' => 'space.space_address.longitude']) !!}

  <div class="form-group mt-2">
    <label> @lang('messages.location.address1') <sub class="text-danger"> * </sub> </label>
    <input type="text" name="address_line" id="address_line" class="form-control" ng-model="space.space_address.address_line" placeholder="{{ trans('messages.lys.address1_placeholder') }}" autocomplete="off" ng-keyup="resetAutoComplete()">
    <span class="small"> @lang('messages.location.address_desc1') </span>
  </div>

<!--   <div class="form-group mt-2">
    <label> @lang('messages.location.address2')</label>
    <input type="text" name="address_line_2" id="address_line_2" class="form-control" ng-model="space.space_address.address_line_2" autocomplete="off">
    <span class="small"> @lang('messages.location.address_desc2') </span>
  </div> -->

  <div class="form-group mt-2">
    <label> @lang('messages.location.checkin_guidance') </label>
    <textarea name="guidance" class="form-control" ng-model="space.space_address.guidance" autocomplete="none"></textarea>
  </div>
  <div class="form-group mt-2">
    <span class="small">
      @lang('messages.location.checkin_guidance_desc')
      <span class="font-weight-bold d-block"> @lang('messages.location.checkin_guidance_desc1') </span>
    </span>
  </div>

  <div class="form-group mt-2 border rounded border-dark">
    <div id="location_map_static" class="w-100 h-100" style="width: 100%; height: 300px;" ng-show="space.space_address.latitude == null || space.space_address.longitude == null"> 
      <img class="w-100 img-fluid" src="{{ asset('images/empty-map.png') }}">
    </div>
    <div id="location_map" class="form-control" style="width: 100%; height: 300px;" ng-show="space.space_address.latitude != null || space.space_address.longitude != null"></div>
  </div>
 <span class="map-guide"><span class="star-list"> **  </span>@lang('messages.location.address_desc2-map')</span>
  {!! Form::close() !!}
</div>
@endsection

@section('what_kind_of_space_desc')
<h1>
  <img src="@asset(images/list_space/space_type.png)" alt="">
</h1>
<h3>
  <strong>
    @lang('messages.steps.space_type_desc1')
  </strong>
</h3>
<h3>
    @lang('messages.steps.space_type_desc2')  
</h3>
@endsection

@section('number_of_rooms_desc')
<h1>
  <img src="@asset(images/list_space/maximum_guests.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold">@lang('messages.steps.num_rooms_desc1') </span>
  <br>
  @lang('messages.steps.num_rooms_desc2')
</h3>
@endsection

@section('guest_access_desc')
<h1>
  <img src="@asset(images/list_space/space_accessibility.png)" alt="">
</h1>
<span class="font-weight-bold">@lang('messages.steps.guest_acces_desc1') </span>
@lang('messages.steps.guest_acces_desc2')
@endsection

@section('maximum_guests_desc')
<h1>
  <img src="@asset(images/list_space/maximum_guests.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.max_guests_desc1') </span>
  @lang('messages.steps.max_guests_desc2')
</h3>
@endsection

@section('amenities_desc')
<h1>
  <img src="@asset(images/list_space/amenity.png)" alt="">
</h1>
<h3>
  <ol>
    <li>
      <span class="font-weight-bold"> @lang('messages.steps.amenities_desc1') </span>
      @lang('messages.steps.amenities_desc2')
    </li>
    <li>
      <span class="font-weight-bold"> @lang('messages.steps.amenities_desc3') </span>
      @lang('messages.steps.amenities_desc4')
    </li>
  </ol>
  
</h3>
@endsection

@section('services_desc')
<h1>
  <img src="@asset(images/list_space/services.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.services_desc1') </span> @lang('messages.steps.services_desc2')
</h3>
@endsection

@section('location_desc')
<h1>
  <img src="@asset(images/list_space/location.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold">  @lang('messages.steps.location_desc1') </span>  @lang('messages.steps.location_desc2')
</h3>
@endsection