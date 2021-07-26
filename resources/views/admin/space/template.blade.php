@section('edit_page_submit')
<div class="box-footer">
    <a href="{{route('admin.space')}}" class="btn btn-default cancel">
      Cancel
  </a>
  <button class="btn btn-info pull-right" type="submit" id="space_submit_button" name="submit" ng-value="submit_step" ng-hide="submit_step=='availability'" ng-disabled="submit_disable==1">
      Submit
  </button>
</div>
@endsection
@section('space_type')
<fieldset class="box-body">
    <div class="form-group">
        <label for="space_type" class="col-sm-3 control-label">
            What type of space do you have?
            <em class="text-danger">*</em>
        </label>
        <div class="col-sm-6">
            {{ Form::select('space_type', $space_types, @$space->space_type, ['class' => 'form-control', 'id' => 'space_type', 'placeholder' => 'Select...']) }}
        </div>
    </div>
</fieldset>
@endsection
@section('basic')
<fieldset class="box-body">
  {{-- <div class="form-group">
    <label for="number_of_rooms" class="col-sm-3 control-label">Number of Rooms
    </label>
    <div class="col-sm-6">
        {{ Form::number('number_of_rooms', @$space->number_of_rooms, ['class' => 'form-control', 'id' => 'number_of_rooms', 'placeholder' => '','min' => '0']) }}
    </div>
</div>
<div class="form-group">
    <label for="number_of_restrooms" class="col-sm-3 control-label">Number of Restrooms
    </label>
    <div class="col-sm-6">
        {{ Form::number('number_of_restrooms', @$space->number_of_restrooms, ['class' => 'form-control', 'id' => 'number_of_restrooms', 'placeholder' => '','min' => '0']) }}
    </div> 
</div>--}}
<div class="form-group">
    <label for="number_of_restrooms" class="col-sm-3 control-label"> @lang('messages.basics.is_your_space_fully_furnished') <sub class="text-danger"> * </sub>
    </label>
    <div class="col-sm-6">
        <input type="radio" name="fully_furnished" class="form-control"  value='Yes' {{@$space->fully_furnished=='Yes'?'checked':''}}> @lang('messages.reviews.yes')
        <input type="radio" name="fully_furnished" class="form-control" value='No' {{@$space->fully_furnished=='No'?'checked':''}}>@lang('messages.reviews.no')
    </div> 
</div> 
<div class="form-group">
    <label for="number_of_restrooms" class="col-sm-3 control-label" min ='0'> @lang('messages.basics.how_many_workstations') <sub class="text-danger"> * </sub>
    </label>
    <div class="col-sm-6">
     <input type="number" name="no_of_workstations" value='{{@$space->no_of_workstations}}'class="form-control" >
 </div> 
</div>
<div class="form-group">
    <label for="number_of_restrooms" class="col-sm-3 control-label"> @lang('messages.basics.are_they_shared_or_private') <sub class="text-danger"> * </sub>
    </label>
    <div class="col-sm-6">
        <input type="radio" name="shared_or_private" class="form-control"  value="Yes" {{@$space->shared_or_private=='Yes'?'checked':''}}>@lang('messages.reviews.yes')
        <input type="radio" name="shared_or_private" class="form-control" value="No"  {{@$space->shared_or_private=='No'?'checked':''}}>@lang('messages.reviews.no')

    </div> 
</div>
<div class="form-group">
    <label for="number_of_restrooms" class="col-sm-3 control-label"> @lang('messages.basics.is_this_your_first_time_of_renting_space',['site_name' => $site_name])<sub class="text-danger"> * </sub>
    </label>
    <div class="col-sm-6">
        <input type="radio" name="renting_space_firsttime" class="form-control"   value="Yes"{{@$space->renting_space_firsttime=='Yes'?'checked':''}}>@lang('messages.reviews.yes')
        <input type="radio" name="renting_space_firsttime" class="form-control"  value="No" {{@$space->renting_space_firsttime=='No'?'checked':''}}>@lang('messages.reviews.no')
    </div> 
</div>


<!-- <div class="form-group">
    <label for="floor_number" class="col-sm-3 control-label">Floor number (if applicable)
    </label>
    <div class="col-sm-6">
        {{ Form::number('floor_number', @$space->floor_number, ['class' => 'form-control', 'id' => 'floor_number', 'placeholder' => '','min' => '0']) }}
    </div>
</div> -->
<div class="form-group">
    <label for="sq_ft" class="col-sm-3 control-label">Estimated square footage of the available Space
        <em class="text-danger">*
        </em>
    </label>
    <div class="col-sm-6">
        {{ Form::number('sq_ft', @$space->sq_ft, ['class' => 'form-control', 'id' => 'sq_ft','required','min' => '0']) }}
    </div>
</div>
</fieldset>
@endsection
@section('guest_access')
<fieldset class="box-body">
    <label class="col-sm-3 control-label" style="text-align: left;">
        How can guests access your space
        <em class="text-danger">*</em>
    </label>
    <span id="guest_access_error"></span>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($guest_accesses as $guest_access)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline guest-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $guest_access->id }}" name="guest_accesses[]" data-error-placement="container" data-error-container="#guest_access_error" ng-checked="{{ in_array($guest_access->id, $prev_guest_access) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $guest_access->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
</fieldset>
@endsection
@section('guests')
<fieldset class="box-body">
    <div class="form-group">
        <label for="number_of_guests" class="col-sm-3 control-label">Maximum number of guests
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {{ Form::number('number_of_guests', @$space->number_of_guests, ['class' => 'form-control', 'id' => 'number_of_guests','required','min' => '1']) }}
        </div>
    </div>
</fieldset>
@endsection
@section('amenities')
<fieldset class="box-body">
    <label class="col-sm-3 control-label" style="text-align: left;">
        Amenities
    </label>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($amenitieses as $amenities)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline amenities-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $amenities->id }}" name="amenities[]" ng-checked="{{ in_array($amenities->id, $prev_amenities) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $amenities->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
</fieldset>
@endsection
@section('services')
<fieldset class="box-body">
    <label class="col-sm-3 control-label" style="text-align: left;">
        What services and extras do you offer
    </label>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($services as $service)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline service-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $service->id }}" name="services[]" ng-checked="{{ in_array($service->id, $prev_services) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $service->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
    <div class="form-group">
        <label for="services_extra" class="col-sm-3 control-label">
            Additional information about services, packages and rates:
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('services_extra', @$space->services_extra, ['class' => 'form-control', 'id' => 'services_extra', 'maxlength' => 500]) }}
        </div>
    </div>
</fieldset>
@endsection

@section('location')
<fieldset class="box-body">
    <div class="form-group">
        <label for="country" class="col-sm-3 control-label">Country
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {!! Form::select('country', $country, @$space->space_address->country, ['class' => 'form-control', 'id' => 'country', 'placeholder' => 'Select...']) !!}
        </div>
    </div> 
    <div class="form-group">
        <label for="address_line_1" class="col-sm-3 control-label">Address Line 1
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {!! Form::text('address_line_1', @$space->space_address->address_line_1, ['class' => 'form-control', 'id' => 'address_line_1', 'placeholder' => 'House name/number + street/road', 'autocomplete' => 'off']) !!}
        </div>
    </div>  
    <div class="form-group">
        <label for="address_line_2" class="col-sm-3 control-label">Address Line 2
        </label>
        <div class="col-sm-6">
            {!! Form::text('address_line_2', @$space->space_address->address_line_2, ['class' => 'form-control', 'id' => 'address_line_2', 'placeholder' => 'Apt., suite, building access code']) !!}
        </div>
    </div>    
    <div class="form-group">
        <label for="city" class="col-sm-3 control-label">City / Town / District
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {!! Form::text('city', @$space->space_address->city, ['class' => 'form-control', 'id' => 'city', 'placeholder' => '']) !!}
        </div>
    </div>     
    <div class="form-group">
        <label for="state" class="col-sm-3 control-label">State / Province / County / Region
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {!! Form::text('state', @$space->space_address->state, ['class' => 'form-control', 'id' => 'state', 'placeholder' => '']) !!}
        </div>
    </div>     
    <div class="form-group">
        <label for="postal_code" class="col-sm-3 control-label">ZIP / Postal Code
        </label>
        <div class="col-sm-6">
            {!! Form::text('postal_code', @$space->space_address->postal_code, ['class' => 'form-control', 'id' => 'postal_code', 'placeholder' => '']) !!}
        </div>
    </div>  
    <input type="hidden" name="latitude" value="{{@$space->space_address->latitude}}" id="latitude" class="do-not-ignore">         
    <input type="hidden" name="longitude" value="{{@$space->space_address->longitude}}" id="longitude">   
    <div class="form-group">
        <label for="guidance" class="col-sm-3 control-label">Check-In Guidance
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('guidance', @$space->space_address->guidance, ['class' => 'form-control', 'id' => 'guidance', 'placeholder' => '']) }}
        </div>
    </div>           
</fieldset>
@endsection

@section('photos')
<fieldset class="box-body">
    <div class="form-group">
        <label for="night" class="col-sm-3" style="text-align: right;">Photos
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            <input type="file" name="photos[]" multiple="true" id="upload_photos" >
        </div>
    </div>
</fieldset>
@endsection
@section('style')
<fieldset class="box-body">
    <label class="col-sm-12 control-label" style="text-align: left;">
        The style of your space can be described as
    </label>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($space_styles as $space_style)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline space_style-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $space_style->id }}" name="space_styles[]" ng-checked="{{ in_array($space_style->id, $prev_space_style) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $space_style->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
</fieldset>
@endsection
@section('special_features')
<fieldset class="box-body">
    <label class="col-sm-12 control-label" style="text-align: left;">
        What special features does your space have?
    </label>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($special_featureses as $special_features)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline special_features-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $special_features->id }}" name="special_featureses[]" ng-checked="{{ in_array($special_features->id, $prev_special_feature) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $special_features->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
</fieldset>
@endsection
@section('space_rules')
<fieldset class="box-body">
    <label class="col-sm-12 control-label" style="text-align: left;">
        Set your Space Rules
    </label>
    <ul class="list-unstyled col-xs-12 col-sm-12 col-md-12" id="triple" style="width:100% !important;margin: 20px 0;">
        @foreach($space_rules as $space_rule)
        <li class="col-xs-12 col-sm-4 col-md-4">
            <label class="label-large label-inline space_rule-accesse-label pull-left" style="width:100% !important;">
                <input class="pull-left" type="checkbox" value="{{ $space_rule->id }}" name="space_rules[]" ng-checked="{{ in_array($space_rule->id, $prev_space_rule) }}">
                <span class="pull-left" style="margin-left:8px;width:85%;white-space:normal;"> {{ $space_rule->name }}
                </span>
            </label>
        </li>
        @endforeach
    </ul>
</fieldset>
@endsection
@section('description')
<fieldset class="box-body">
    <div class="form-group" >
        <label for="language" class="col-sm-3 control-label">Language
        </label>
        <div class="col-sm-6">
            {{ Form::select('language[]', $language, 'en', ['class' => 'form-control check go', 'id' => 'language','disabled']) }}
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-sm-3 control-label">Listing Name
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {{ Form::text('name[]', @$space->name, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Be clear and descriptive']) }}
        </div>
    </div>
    <div class="form-group">
        <label for="summary" class="col-sm-3 control-label">Summary
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('summary[]', @$space->summary, ['class' => 'form-control', 'id' => 'summary', 'placeholder' => 'Tell travelers what you love about the space. You can include details about the decor, the amenities it includes, and the neighborhood.', 'rows' => 5]) }}
        </div>
    </div>
    <div class="form-group">
        <label for="space" class="col-sm-3 control-label">Space
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('space[]', @$space->space_description->space, ['class' => 'form-control', 'id' => 'space', 'rows' => 5]) }}
        </div>
    </div>
    <div class="form-group">
        <label for="access" class="col-sm-3 control-label">Guest Access
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('access[]', @$space->space_description->access, ['class' => 'form-control', 'id' => 'access', 'rows' => 5]) }}
        </div>
    </div>
    <div class="form-group">
        <label for="interaction" class="col-sm-3 control-label">Interaction with Guests
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('interaction[]', @$space->space_description->interaction, ['class' => 'form-control', 'id' => 'interaction', 'rows' => 5]) }}
        </div>
    </div>
    <div class="form-group">
        <label for="notes" class="col-sm-3 control-label">Other Things to Note
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('notes[]', @$space->space_description->notes, ['class' => 'form-control', 'id' => 'notes', 'rows' => 5]) }}
        </div>
    </div>
    <div class="form-group">
        <label for="house_rules" class="col-sm-3 control-label">Space Rules
        </label>
        <div class="col-sm-6">
            {{ Form::textarea('house_rules[]', @$space->space_description->house_rules, ['class' => 'form-control', 'id' => 'house_rules', 'rows' => 5]) }}
        </div>
    </div>
    @if(@$space==null)
    <div ng-repeat="choice in rows">
        <div class="form-group" data-index="@{{ $index }}">
            <label for="language" class="col-sm-3 control-label">Language
            </label>
            <div class="col-sm-6">
                {!! Form::select('language[]', $language, '', ['class' => 'form-control go', 'id' => 'language@{{ $index }}','placeholder' => 'Select...']) !!}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="name" class="col-sm-3 control-label">Listing Name
                <em class="text-danger">*
                </em>
            </label>
            <div class="col-sm-6">
                {{ Form::text('name[]', '', ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Be clear and descriptive']) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="summary" class="col-sm-3 control-label">Summary
                <em class="text-danger">*
                </em>
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('summary[]', '', ['class' => 'form-control', 'id' => 'summary', 'placeholder' => 'Tell travelers what you love about the space. You can include details about the decor, the amenities it includes, and the neighborhood.', 'rows' => 5]) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="space" class="col-sm-3 control-label">Space
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('space[]', '', ['class' => 'form-control', 'id' => 'space', 'rows' => 5]) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="access" class="col-sm-3 control-label">Guest Access
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('access[]', '', ['class' => 'form-control', 'id' => 'access', 'rows' => 5]) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="interaction" class="col-sm-3 control-label">Interaction with Guests
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('interaction[]', '', ['class' => 'form-control', 'id' => 'interaction', 'rows' => 5]) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="notes" class="col-sm-3 control-label">Other Things to Note
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('notes[]', '', ['class' => 'form-control', 'id' => 'notes', 'rows' => 5]) }}
            </div>
        </div>
        <div class="form-group" data-index="@{{ $index }}">
            <label for="house_rules" class="col-sm-3 control-label">Space Rules
            </label>
            <div class="col-sm-6">
                {{ Form::textarea('house_rules[]', '', ['class' => 'form-control', 'id' => 'house_rules', 'rows' => 5]) }}
            </div>
        </div>
        <a class="pull-right" href="javascript:void(0);" ng-click="removeRow($index)">
            <span style="color:red;"> Remove
            </span>
        </a>
        <br>
    </div>
    <a class="pull-right"  href="javascript:void(0);" ng-click="addNewRow()"> Add
    </a>
    @else
    <div ng-repeat="choice_check in rows">
        <div class="form-group" >
            <label for="language" class="col-sm-3 control-label">
                Language
            </label>
            <div class="col-sm-6">
                <select class="go" ng-model="choice_check.language" name="language[]" id="language@{{ $index }}" data-index="@{{ $index }}">
                    <option value="">
                        Select
                    </option>
                    <option ng-repeat="item in lang_list" value="@{{ item.value }}" ng-selected="item.value == choice_check.lang_code" >
                        @{{ item.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">
                Listing Name
                <em class="text-danger">
                    *
                </em>
            </label>
            <div class="col-sm-6">
                <input type="text"  class="form-control" id="name" name="name[]" ng-model="choice_check.name" placeholder="Be clear and descriptive" data-index="@{{ $index }}" >
            </div>
        </div>
        <div class="form-group">
            <label for="summary" class="col-sm-3 control-label">
                Summary
                <em class="text-danger">
                    *
                </em>
            </label>
            <div class="col-sm-6">
                <textarea name="summary[]"  class="form-control" id="summary" placeholder="Tell travelers what you love about the space. You can include details about the decor, the amenities it includes, and the neighborhood." rows="5" ng-model="choice_check.summary" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="space" class="col-sm-3 control-label">
                Space
            </label>
            <div class="col-sm-6">
                <textarea name="space[]"  class="form-control" id="space" rows="5" ng-model="choice_check.space" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="access" class="col-sm-3 control-label">
                Guest Access
            </label>
            <div class="col-sm-6">
                <textarea name="access[]"  class="form-control" id="space" rows="5" ng-model="choice_check.access" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="interaction" class="col-sm-3 control-label">
                Interaction with Guests
            </label>
            <div class="col-sm-6">
                <textarea name="interaction[]"  class="form-control" id="interaction" rows="5" ng-model="choice_check.interaction" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="notes" class="col-sm-3 control-label">
                Other Things to Note
            </label>
            <div class="col-sm-6">
                <textarea name="notes[]"  class="form-control" id="notes" rows="5" ng-model="choice_check.notes" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="house_rules" class="col-sm-3 control-label">
                Space Rules
            </label>
            <div class="col-sm-6">
                <textarea name="house_rules[]"  class="form-control" id="house_rules" rows="5" ng-model="choice_check.house_rules" data-index="@{{ $index }}">
                </textarea>
            </div>
        </div>
        <a class="pull-right" href="javascript:void(0);" ng-click="removeRow($index)">
            <span class="text-danger">
                Remove
            </span>
        </a>
        <br>
    </div>
    <a class="pull-right"  href="javascript:void(0);" ng-click="addNewRow()">
        Add
    </a>
    @endif
</fieldset>
@endsection
@section('activity')
<input type="hidden" name="space_activities" id="space_activities" value=@{{getActivitiesData()}}>
<fieldset class="box-body" ng-init="space_activities='{}';">
    <label class="control-label">
        Which activities are welcome in your space?
        <em class="text-danger">*</em>
    </label>
    <p id="activity_error" class="text-danger"></p>
    <div class="col-md-12">
        @foreach($activity_types as $activity_type)
        <div class="panel panel-default cls_activitis mt-2">
            <a class=" w-100 activity-header {{ in_array($activity_type->id, $prev_activity_type) ? '':'collapsed'}}" data-toggle="collapse" href="#activity_type_{{ $activity_type->id }}"> <i class="fa fa-plus"></i> <i class="fa fa-minus"></i> {{ $activity_type->name }} </a>
            <div id="activity_type_{{ $activity_type->id }}" class="panel-collapse collapse {{ in_array($activity_type->id, $prev_activity_type) ? 'in':''}}">

                <ul class="h5 list-group">
                    @foreach($activities as $activity)
                    <li class="mt-2" ng-if="{{ $activity_type->id == $activity->activity_type_id}}">
                        <div class="form-check">
                            <input type="checkbox" data-error-container="#activity_error" data-error-placement="container" name="activity[]" class="mt-1 form-check-input activities do-not-ignore" id="activity_{{$activity->id}}" value="{{$activity->id}}" data-activity_type="{{ $activity_type->id }}" ng-checked="{{ in_array($activity->id, $prev_activities) }}">
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
</fieldset>
@endsection
@section('edit_space_price')
<fieldset class="box-body" ng-init='space={{@$space==null?"{}":$space}};minimum_amount={{$minimum_amount}};min_price_symbol="{{html_entity_decode($currency_symbol)}}"'>
    <input type="hidden" name="activity_price" value=@{{getActivitiesPriceData()}}>
    <label for="hourly_rate" class="control-label mb-2">
        Set your price (Minimum Hourly Rate is <span ng-bind-html="min_price_symbol"></span><span ng-bind="minimum_amount"></span>)
    </label>
    <div class="mb-3 pb-3">
        <div class="panel-group">
            <div class="mt-2" ng-repeat="activities in space.space_activities">

                <div class="col-md-12 mb-3 align-items-center d-flex">
                    <img class="img event_image-icon" ng-src="@{{ activities.activity_type.image_url }}" style="height: 30px;width: 30px;vertical-align: bottom;">
                    <span class="h5"> @{{ activities.activity_type.name }} </span>
                </div>  
                <div class="col-md-4">
                    <div class=" mt-2">
                        <label> @lang('messages.ready_to_host.hourly_rate') </label>
                        <input type="number"  name="hourly_rate" id="hourly_rate_@{{ activities.activity_price.id }}" class="form-control hourly_rate" ng-model="activities.activity_price.original_hourly" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="@{{minimum_amount}}" required>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="mt-2">
                        <label> @lang('messages.ready_to_host.full_day_rate')  (@lang('messages.new_space.optional')) </label>
                        <input type="number" name="full_day_rate" id="full_day_rate_@{{ activities.activity_price.id }}" class="form-control full_day_rate" ng-model="activities.activity_price.original_full_day" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="@{{minimum_amount}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mt-2">
                        <label> @lang('messages.ready_to_host.weekly_rate')  (@lang('messages.new_space.optional')) </label>
                        <input type="number" name="weekly" id="weekly_@{{ activities.activity_price.id }}" class="form-control weekly" ng-model="activities.activity_price.original_weekly" min="@{{minimum_amount}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mt-2">
                        <label> @lang('messages.ready_to_host.monthly_rate')  (@lang('messages.new_space.optional')) </label>
                        <input type="number" name="monthly" id="monthly_@{{ activities.activity_price.id }}" class="form-control monthly" ng-model="activities.activity_price.original_monthly" min="@{{minimum_amount}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class=" mt-2">
                        <label>
                            @lang('messages.ready_to_host.min_hours')
                            <i class="fa fa-info-circle" data-html="true" data-toggle="tooltip" title="@lang('messages.ready_to_host.min_hours_tooltip')"></i>
                        </label>
                        <div id="min_hours_error" class="input-group mb-3">
                            <input type="number" number="min_hours" id="min_hours_@{{ activities.activity_price.id }}" class="form-control input-number min_hours" ng-model="activities.activity_price.min_hours" ng-change="updateFormStatus();validateStepData('ready_to_host', 'activity_price');" min="0" max="4" style="height: 35px;" data-error-placement="container" data-error-container="#min_hours_error">
                            <div class="input-group-append">
                                <button type="button" style="height: 35px;" class="btn input-group-text fa fa-minus" ng-click="updateActivityHours('decrease',$index,activities.activity_price)" ng-disabled="activities.activity_price.min_hours < 2" ></button>
                            </div>
                            <div class="input-group-append">
                                <button type="button" style="height: 35px;" class="btn input-group-text fa fa-plus" ng-click="updateActivityHours('increase',$index,activities.id)" ng-disabled="activities.activity_price.min_hours > 3"></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="pricing_field my-3 col-md-12">
                <label>
                    {{ trans('messages.account.currency') }}
                </label>
                <div id="currency-picker" class="form-select " ng-init="activity_currency='{{(@$space->space_activities[0]->activity_price->original_currency_code)?$space->space_activities[0]->activity_price->original_currency_code:DEFAULT_CURRENCY}}'">
                    {!! Form::select('currency_code',$currency,'', ['id' => 'price_currency_code','class' => 'form-control','ng-model' => 'activity_currency', 'ng-change' => 'updateFormStatus();currency_change();']) !!}
                </div>
            </div>
        </div>
    </div>
</fieldset>
@endsection
@section('add_space_price')
<fieldset class="box-body" ng-init="all_activity_types={{$activity_types}};minimum_amount={{$minimum_amount}};min_price_symbol='{{$currency_symbol}}'">
    <input type="hidden" name="activity_price" value="@{{selected_activity_type}}">
    <label for="hourly_rate" class="control-label">Set your price (Minimum Hourly Rate is <span ng-bind-html="min_price_symbol"></span><span ng-bind="minimum_amount"></span>) </label>
    <div class="mb-3 pb-3">
        <div class="panel-group">
            <div class="mt-2" ng-repeat="activities in selected_activity_type">
                <div class="col-md-12 mb-3 align-items-center d-flex">
                    <img class="img event_image-icon" ng-src="@{{ activities.image_url }}" style="height: 30px;width: 30px;vertical-align: bottom;">
                    <span class="h5"> @{{ activities.name }} </span>
                </div>  
                <div class="col-md-4">
                    <div class="mt-2">
                        <label> @lang('messages.ready_to_host.hourly_rate') </label>
                        <input type="number" name="hourly_rate" class="form-control hourly_rate" ng-model="activities.activity_price.hourly" min="@{{minimum_amount}}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mt-2">
                        <label> @lang('messages.ready_to_host.full_day_rate')  </label>
                        <input type="number" name="full_day_rate" class="form-control full_day_rate" ng-model="activities.activity_price.full_day" min="@{{minimum_amount}}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mt-2" ng-init="activities.activity_price.weekly=0">
                        <label> @lang('messages.ready_to_host.weekly_rate') </label>
                        <input type="number" name="weekly" id="weekly_@{{ activities.activity_price.id }}" class="form-control weekly" ng-model="activities.activity_price.weekly" min="@{{minimum_amount}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mt-2" ng-init="activities.activity_price.monthly=0">
                        <label> @lang('messages.ready_to_host.monthly_rate')  </label>
                        <input type="number" name="monthly" id="monthly_@{{ activities.activity_price.id }}" class="form-control monthly" ng-model="activities.activity_price.monthly" min="@{{minimum_amount}}" required>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class=" mt-2">
                        <label>
                            @lang('messages.ready_to_host.min_hours')
                            <i class="fa fa-info-circle" data-html="true" data-toggle="tooltip" title="@lang('messages.ready_to_host.min_hours_tooltip')"></i>
                        </label>
                        <div id="min_hours_error" class="input-group mb-3">
                            <input type="number" number="min_hours" class="form-control input-number min_hours" ng-model="activities.activity_price.min_hours" min="0" max="4" style="height: 35px;" data-error-placement="container" data-error-container="#min_hours_error">
                            <div class="input-group-append">
                                <button type="button" class="btn input-group-text fa fa-minus" ng-disabled="activities.activity_price.min_hours < 2" ng-click="activities.activity_price.min_hours=activities.activity_price.min_hours-1" style="height: 35px;"></button>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn input-group-text fa fa-plus" ng-click="activities.activity_price.min_hours=activities.activity_price.min_hours+1" ng-disabled="activities.activity_price.min_hours > 3" style="height: 35px;"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pricing_field my-3 col-md-12 p-0">
                <label>
                    {{ trans('messages.account.currency') }}
                </label>
                <div id="currency-picker" class="form-select" ng-init="activity_currency='{{(@$space==null || !@$space->activity_price->currency_code)?DEFAULT_CURRENCY:$space->activity_price->currency_code}}'">
                    {!! Form::select('currency_code',$currency,'', ['id' => 'price_currency_code','class' => 'form-control','ng-model' => 'activity_currency', 'ng-change' => 'updateFormStatus();currency_change();']) !!}
                </div>
            </div>
        </div>
    </div>
</fieldset>
@endsection
@section('availability')
<fieldset class="box-body">
    <input type="hidden" name="availabilities" value="@{{availabilities}}">
    <label class="control-label">
        Set your space availability
    </label>
    <div class="col-12 manage-listing-container d-flex" id="js-manage-listing-content-container">
        <div class="manage-listing-content col-12 px-0" id="js-manage-listing-content">
            <div class="mb-3 pb-3 d-flex align-items-center justify-content-between" ng-init="times_array={{ json_encode($times_array) }};days_array={{json_encode($days_array)}};availabilities={{$space_availabilities}};">
                <div class="availability-form space-availability">
                    <div ng-repeat="(avail_key,availability) in availabilities">
                        <div class="mt-4 border-bottom pb-3" ng-init="availability.removed_availability = []">
                            <div class="row">
                                <div class="col-md-3">
                                    @{{ availability.day_name }}
                                </div>
                                <div class="col-md-3" ng-init="availability.available = getAvailabeStatus(availability.status);availability.status = getAvailabilityStatus(availability.status);">
                                    <label class="switch">
                                        <input type="checkbox" id="availability_opt_@{{ $index }}" class="toggle_switch" ng-model="availability.status" ng-true-value="'Open'" ng-false-value="'Closed'" ng-change="availabilityChanged()">
                                        <span class="slider round">
                                        </span>
                                    </label>
                                    <label for="availability_opt_@{{ $index }}" ng-show="availability.status == 'Closed'"> @lang('messages.ready_to_host.closed') 
                                    </label>
                                    <label for="availability_opt_@{{ $index }}" ng-show="availability.status != 'Closed'"> @lang('messages.ready_to_host.open') 
                                    </label>
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
                        </div>
                        <div class="mt-2" ng-hide="availability.status == 'Closed'">
                            <div class="row mt-4" ng-repeat="avail_hours in availability.availability_times">
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
                                <div class="col-md-2 p-0 text-left">
                                    <a class="btn btn-default mr-2" ng-hide="availability.availability_times.length == 1" ng-click="removeAvailabilityHours(avail_key,$index)"> <span class="icon icon-trash"></span> </a>
                                    <a class="btn btn-default" ng-show="(availability.availability_times.length - 1) == $index" ng-click="addAvailabilityHours(avail_key)"> <span class="icon icon-add"></span> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</fieldset>
@endsection
@section('rules')
<fieldset class="box-body">
    <div class="form-group">
        <label for="cancellation_policy" class="col-sm-3 control-label">Set your cancellation policy
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {{ Form::select('cancellation_policy', ['Flexible'=>'Flexible', 'Moderate'=>'Moderate','Strict'=>'Strict'], @$space->cancellation_policy, ['class' => 'form-control', 'id' => 'cancellation_policy', 'placeholder' => 'Select...']) }}
        </div>
    </div>
    <div class="form-group">
        <label for="booking_type" class="col-sm-3 control-label">Booking Type
            <em class="text-danger">*
            </em>
        </label>
        <div class="col-sm-6">
            {{ Form::select('booking_type', ['request_to_book'=>'Request To Book', 'instant_book'=>'Instant Book'], (@$space==null)?'instant_book':$space->booking_type, ['class' => 'form-control', 'id' => 'booking_type']) }}
        </div>
    </div>
    <div class="form-group">
        <label for="security_deposit" class="col-sm-3 control-label">Security Deposit
        </label>
        <div class="col-sm-6">
            {{ Form::number('security_deposit', @$space->space_price->security, ['class' => 'form-control', 'id' => 'security_deposit','min'=>1, 'autocomplete' => 'off']) }}
        </div>
    </div>
</fieldset>
@endsection