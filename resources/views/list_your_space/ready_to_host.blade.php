@include('list_your_space.ready_to_host_step_template')
<div class="progress">
      <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" ng-style="progress_style">
      </div>
    </div>
<div class="manage-listing-container d-flex cls_flexd" id="js-manage-listing-content-container" ng-init="current_step={{ $step_num }};current_step_name='ready_to_host';next_step_name='';" ng-cloak>
  <div class="manage-listing-content cls_mlist" id="js-manage-listing-content" ng-class="(current_step == '5' || current_step == '3') ? 'col-md-12' : 'col-md-7'">
    <div class="cls_listblock">
      <div>
        <div data-step='1' ng-show="current_step == 1">
          @yield('activities')
        </div>
        <div data-step='2' ng-show="current_step == 2">
          @yield('activity_price')
        </div>
        <div class="cls-full_screen" data-step='3' ng-show="current_step == 3">
          @yield('availability')
        </div>
        <div data-step='4' ng-show="current_step == 4">
          @yield('cancellation')
          @yield('booking_type')
          @yield('security')
        </div>
        <div class="cls-full_screen" data-step='5' ng-show="current_step == 5">
          @yield('calendar')
        </div>
      </div>
    </div>
  </div>
  <div class="cls_fixlsit" ng-class="(current_step == 5 || current_step == 3) ? 'calendar_step':''" ng-style="bottom_style">
    
    <div class="d-flex align-items-center justify-content-between progress-buttons">
      <div class="prevStep">
        <button class="back-section-button" ng-click="prevStep('ready_to_host')" ng-hide="step_error != ''" ng-disabled="is_loading || main_loading">
          @lang('messages.new_space.back')
        </button>
      </div>
     
      <div class="next_step d-flex align-items-center justify-content-between">
         <span class="warning_message text-danger pl-2 pr-2" ng-show="step_error != ''">
          @{{ step_error }}
        </span>
        <button type="button" class="btn btn-primary next-section-button" ng-click="nextStep('ready_to_host');" ng-disabled="is_loading || main_loading || step_error != ''">
          @lang('messages.new_space.continue')
        </button>
      </div>
    </div>
  </div>

  <div class="col-md-5 cls_mlistright" ng-hide="(current_step == 5 || current_step == 3)">

    <div data-step='1' ng-show="current_step == 1">
      @yield('activities_desc')
    </div>
    <div data-step='2' ng-show="current_step == 2">
      @yield('activity_price_desc')
    </div>
    <div class="cls-full_screen" data-step='3' ng-show="current_step == 3">
      @yield('availability_desc')
    </div>
    <div data-step='4' ng-show="current_step == 4">
      @yield('cancellation_desc')
    </div>
  </div>

</div>