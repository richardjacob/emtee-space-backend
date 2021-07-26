@include('list_your_space.basics_step_template')
<div class="progress">
  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" ng-style="progress_style">
  </div>
</div>
<div class="manage-listing-container d-flex cls_flexd" id="js-manage-listing-content-container">
  <div class="manage-listing-content col-md-7 cls_mlist" id="js-manage-listing-content" ng-init="current_step={{$step_num}};current_step_name='basics';next_step_name='setup';">
    <div class="cls_listblock">
    <div data-step='1' ng-show="current_step == 1">
      <div class="content-heading my-4">
        <h3>
          {{ trans('messages.lys.basics_title1') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc1',['site_name'=>$site_name]) }}
        </p>
      </div>
    </div>
    <div data-step='2' ng-show="current_step == 2">
      <div class="content-heading my-4">
        <h3>
          {{ trans('messages.lys.basics_title2') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc2',['site_name'=>$site_name]) }}
        </p>
      </div>
    </div>
     <div data-step='3' ng-show="current_step == 3">
      <div class="content-heading my-4">
        <h3>
          {{ trans('messages.lys.basics_title') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc',['site_name'=>$site_name]) }}
        </p>
      </div>
    </div>
    <div data-step='4' ng-show="current_step == 4">
      <div class="content-heading my-4">
        <h3>
          {{ trans('messages.lys.basics_title') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc',['site_name'=>$site_name]) }}
        </p>
      </div>
    </div>
    <div data-step='5' ng-show="current_step == 5">
      <div class="content-heading my-4">
        <!-- <h3>
          {{ trans('messages.lys.basics_title') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc',['site_name'=>$site_name]) }}
        </p> -->
      </div>
    </div>
    <div data-step='6' ng-show="current_step == 6">
      <div class="content-heading my-4">
       <!--  <h3>
          {{ trans('messages.lys.basics_title') }}
        </h3>
        <p>
          {{ trans('messages.lys.basics_desc',['site_name'=>$site_name]) }}
        </p> -->
        <h3>
           {{ trans('messages.location.address_of_space') }}
        </h3>
      </div>
    </div>
    <div data-step='7' ng-show="current_step == 7">
      <div class="content-heading my-4">
        <h3>
          {{ trans('messages.basics.how_guest_can_access') }} <sub class="text-danger" style="vertical-align: sub;"> * </sub>
        </h3>
       <!--  <p>
          {{ trans('messages.lys.basics_desc',['site_name'=>$site_name]) }}
        </p> -->
      </div>
    </div>
      <div class="js-section">
       <!--  <h4>
          {{ trans('messages.lys.listing') }}
        </h4> -->
        <div class="my-3 option-row">
          <div data-step='1' ng-show="current_step == 1">
            @yield('what_kind_of_space')
          </div>

          <div data-step='2' ng-show="current_step == 2">
            @yield('number_of_rooms')
          </div>

          <div data-step='3' ng-show="current_step == 3">
            @yield('maximum_guests')
          </div>

          <div data-step='4' ng-show="current_step == 4">
            @yield('amenities')
          </div>

          <div data-step='5' ng-show="current_step == 5">
            @yield('services')
          </div>

          <div data-step='6' ng-show="current_step == 6">
            @yield('location')
          </div>

          <div data-step='7' ng-show="current_step == 7">
            @yield('guest_access')
          </div>
        </div>
      </div>
  </div>
  </div>

  <div class="cls_fixlsit" ng-style="bottom_style">

    <div class="d-flex align-items-center justify-content-between progress-buttons">
      <div class="prevStep">
        <button class="back-section-button" ng-click="prevStep('basics')" ng-hide="(step_error != '') || (space_id == '' && current_step == 1)" ng-disabled="is_loading || main_loading">
          @lang('messages.new_space.back')
        </button>
      </div>
       
      <div class="next_step d-flex align-items-center justify-content-between">
        <span class="warning_message text-danger pr-2 pl-2" ng-show="step_error != ''">
          @{{ step_error }}
        </span>
        <button type="button" class="btn btn-primary next-section-button" ng-click="nextStep('basics');" ng-disabled="is_loading || main_loading || step_error != ''">
          @lang('messages.new_space.continue')
        </button>
      </div>
    </div>
  </div>
  
  <div class="col-md-5 cls_mlistright">
    <div data-step='1' ng-show="current_step == 1">
      @yield('what_kind_of_space_desc')
    </div>

    <div data-step='2' ng-show="current_step == 2">
      @yield('number_of_rooms_desc')
    </div>

    <div data-step='7' ng-show="current_step == 7">
      @yield('guest_access_desc')
    </div>

    <div data-step='3' ng-show="current_step == 3">
      @yield('maximum_guests_desc')
    </div>

    <div data-step='4' ng-show="current_step == 4">
      @yield('amenities_desc')
    </div>

    <div data-step='5' ng-show="current_step == 5">
      @yield('services_desc')
    </div>

    <div data-step='6' ng-show="current_step == 6">
      @yield('location_desc')
    </div>
  </div>

</div>
