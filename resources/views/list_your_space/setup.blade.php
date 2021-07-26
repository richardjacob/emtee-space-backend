@include('list_your_space.setup_step_template')
<div class="progress">
	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" ng-style="progress_style">
	</div>
</div>
<div class="manage-listing-container d-flex cls_flexd" id="js-manage-listing-content-container">
	<div class="manage-listing-content col-md-7 cls_mlist" id="js-manage-listing-content">
		<div class="cls_listblock">
			<div ng-init="current_step={{$step_num}};current_step_name='setup';next_step_name='ready_to_host';">
				<div data-step='1' ng-show="current_step == 1" class="image_step_view">
					@yield('photos')
				</div>
				<div data-step='2' ng-show="current_step == 2">
					@yield('style')
				</div>
				<div data-step='3' ng-show="current_step == 3">
					@yield('special_features')
				</div>
				<div data-step='4' ng-show="current_step == 4">
					@yield('space_rules')
				</div>
				<div data-step='5' ng-show="current_step == 5">
					@yield('description')
				</div>
			</div>
		</div>
	</div>
	<div class="cls_fixlsit" ng-style="bottom_style">
		<div class="d-flex align-items-center justify-content-between progress-buttons">
			<div class="prevStep">
				<button class="back-section-button" ng-click="prevStep('setup')" ng-hide="step_error != ''" ng-disabled="is_loading || main_loading">
					@lang('messages.new_space.back')
				</button>
			</div>
			
			<div class="next_step d-flex align-items-center justify-content-between">
				<span class="warning_message text-danger pl-2 pr-2" ng-show="step_error != ''">
					@{{ step_error }}
				</span>
				<button type="button" class="btn btn-primary next-section-button" ng-click="nextStep('setup');" ng-disabled="is_loading || main_loading || step_error != ''">
					@lang('messages.new_space.continue')
				</button>
			</div>
		</div>
	</div>
	<div class="col-md-5 cls_mlistright">
		<div data-step='1' ng-show="current_step == 1">
			@yield('photos_desc')
		</div>
		<div data-step='2' ng-show="current_step == 2">
			@yield('style_desc')
		</div>
		<div data-step='3' ng-show="current_step == 3">
			@yield('special_features_desc')
		</div>
		<div data-step='4' ng-show="current_step == 4">
			@yield('space_rules_desc')
		</div>
		<div data-step='5' ng-show="current_step == 5">
			@yield('description_desc')
		</div>
	</div>
</div>