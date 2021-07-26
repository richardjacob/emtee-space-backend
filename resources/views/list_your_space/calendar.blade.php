<!-- Center Part Starting  -->
<div class="col-12 manage-listing-container d-flex" id="js-manage-listing-content-container">
	<div class="manage-listing-content col-12" id="js-manage-listing-content">
		<div class="content-heading my-4">
			<h3>
				@lang('messages.lys.calendar_title') 
			</h3>
			<p>
				@lang('messages.lys.calendar_desc',['site_name'=>$site_name]) 
			</p>
		</div>
		<div class="row">
			<div class="calendar-setting col-12">
				<ul class="d-flex d-md-none d-lg-none">
					<li class="">
						<span>@lang('messages.lys.mobile_select_desc') </span>
					</li>
				</ul>
				<ul class="d-flex flex-sm-wrap justify-content-between justify-content-lg-start">
					<li class="">
						<div class="d-inline-flex" style="background-color: #46A4A7;height: 10px; width: 10px;"></div>
						<span> @lang('messages.lys.today') </span>
					</li>
					<li class="ml-lg-2">
						<div class="d-inline-flex" style="background-color: #8592FF;height: 10px; width: 10px;">
						</div>
						<span> @lang('messages.lys.Blocked') </span>
					</li>
					<li class="ml-lg-2">
						<div class="d-inline-flex" style="background-color: #E2B4B6;height: 10px; width: 10px;">
						</div>
						<span> @lang('messages.inbox.reservations') </span>
					</li>
					<li class="ml-lg-2">
						<div class="d-inline-flex" style="background-color: #AFE4A4;height: 10px; width: 10px;">
						</div>
						<span> @lang('messages.email.not_available') </span>
					</li>
				</ul>
			</div>
			<div id="monthly_calendar" class="calendar col-12 mt-4 mb-1 "  ng-init="month_calendar_data={{ json_encode($month_calendar) }};minimum_amount='{{ $minimum_amount}}';spots_left_text='{{ __('messages.shared_rooms.spots_left') }}'";></div>
			<div id="calendar" class="calendar col-12 mt-4 mb-1 col-lg-8 weekly_cal" ng-test="@{{ monthclass==true ? 'monthly_cal' : ''}}" ng-class="monthclass==true ? 'monthly_cal' : ''" ng-init="calendar_data={{ json_encode($calendar) }};minimum_amount='{{ $minimum_amount}}';spots_left_text='{{ __('messages.shared_rooms.spots_left') }}'";></div>
			<div class="calendar-side-option col-12 col-lg-4 pt-4 pt-lg-5" ng-show="showUpdateForm">
				<form name="calendar-edit-form" class="ng-pristine ng-valid">
					<div class="panel-header text-center" ng-init="segment_status = 'Available'">
						<div class="segmented-control d-md-flex">
							<label id="avi" class="segmented-control-option segmented-option-selected" ng-class="(segment_status == 'Available') ? 'segmented-option-selected' : '' ">
								<span>
									@lang('messages.lys.Available')
								</span>
								<input type="radio" id="available_check" ng-checked="segment_status == 'Available'" name="radio" ng-model="segment_status" value="Available" class="segmented-control-input ng-pristine ng-untouched ng-valid" checked="checked">
							</label>
							<label id="unavi" class="segmented-control-option" ng-class="(segment_status == 'Not available') ? 'segmented-option-selected' : ''">
								<span>
									@lang('messages.lys.Blocked')
								</span>
								<input type="radio" id="notavailable_check" ng-checked="segment_status == 'Not available'" name="radio" value="Not available" ng-model="segment_status" class="segmented-control-input ng-pristine ng-untouched ng-valid">
							</label>
						</div>
					</div>
					<div class="panel-body text-center">
						<div class="d-flex">
							<div class="col-6">
								<label> 
									@lang('messages.lys.start_date') 
								</label>
								<input type="hidden" name='type' ng-model='type_calendar'>
								<input type="text" id="calendar-edit-start" ng-model="calendar_edit_start_date" readonly="readonly">
								<label class="mt-2"  ng-show="calendar_edit_start_time!='False'"> 
									@lang('messages.space_detail.start_time') 
								</label>
								
								<input type="text" id="calendar-edit-start_time" class="mt-2" ng-model="calendar_edit_start_time" readonly="readonly"  ng-show="calendar_edit_start_time!='False'">
								<input type="hidden" id="calendar-start">
							</div>
							<div class="col-6">
								<label> 
									@lang('messages.lys.end_date') 
								</label>
								<input type="text" id="calendar-edit-end" ng-model="calendar_edit_end_date" readonly="readonly">
								<label class="mt-2" ng-show="calendar_edit_end_time!='False'"> 
									@lang('messages.space_detail.end_time') 
								</label>
								<input type="text" id="calendar-edit-end_time" class="mt-2" ng-model="calendar_edit_end_time" readonly="readonly" ng-show="calendar_edit_end_time!='False'">
								<input type="hidden" id="calendar-end">
							</div>
						</div>
						<div class="notes-wrap mt-3">
							<a data-prevent-default="true" href="#" class="link-icon alg_1" onclick="return false;" ng-click="isAddNote = !isAddNote">
								<span class="link-icon__text">
									@lang('messages.lys.add_note')
								</span>
								<i class="fa fa-caret-down"></i>
							</a>
							<textarea ng-model="notes" ng-show="isAddNote" class="mt-3"></textarea>
						</div>
					</div>
					<div class="panel-footer d-flex align-items-center justify-content-center pt-0">
						<button class="btn btn-default" ng-click="destroyCalendar();full_calendar();">
							@lang('messages.your_reservations.cancel')
						</button>
						<button type="submit" class="btn btn-host" ng-disabled="calendar_edit_price < minimum_amount" ng-click="calendarEditSubmit()">
							@lang('messages.wishlist.save_changes')
						</button>
					</div>
				</form>
			</div>
		</div>
		{{--
		<ul class="my-4 calendar-footer-button">
			<li>
				<a href="javascript:void(0)" id="import_button" data-toggle="modal" data-target="#import_popup">
					@lang('messages.lys.import_calc')
				</a>
			</li>
			<li>
				<a href="{{ url('calendar/sync/'.$result->id) }}" class="js-calendar-sync" data-prevent-default="true">
					@lang('messages.lys.sync_other_calc')
				</a>
			</li>
			<li>
				<a href="javascript:void(0)" id="export_button" data-toggle="modal" data-target="#export_popup">
					@lang('messages.lys.export_calc')
				</a>
			</li>
			<li>
				<a href="javascript:void(0)" class="remove_sync_button" id="remove_sync_button" data-toggle="modal" data-target="#remove_sync_popup">
					@lang('messages.lys.remove_calc')
				</a>
			</li>
		</ul>

		<div id="calendar-rules" class="sidebar-overlay" ng-init="rs_errors = []">
			<div class="sidebar-overlay-inner js-section">
				<h3 class="sidebar-overlay-heading">
					{{ trans('messages.lys.reservation_settings') }}
				</h3>
				<button type="button" id="js-close-calendar-settings-btn" class="close" data-dismiss="modal"></button>
				<div class="js-saving-progress reservation_settings-saving saving-progress" style="display: none;">
					<h5>
						{{ trans('messages.lys.saving') }}...
					</h5>
				</div>
				<div class="clearfix"></div>
				
				<div class="js-calendar-sync-section sidebar-overlay-highlight-section d-none">
					<div></div>
					<h3 id="calendar_sync_heading" data-hook="calendar_sync_heading" class="row-space-4 sidebar-overlay-heading">
						{{ trans('messages.lys.sync_calc') }}
					</h3>
					<div data-hook="calendar_sync">
						<div class="space-2">
							<div class="row row-condensed">
								<div class="col-sm-12">
									<ul class="list-unstyled">
										<li class="space-1">
											<a href="{{ url('manage-listing/'.$space_id.'/calendar') }}" data-prevent-default="true" class="text-muted link-icon">
												<i name="download" class="icon icon-download"></i>
												<span>
													{{ trans('messages.lys.import_calc') }}
												</span>
											</a>
										</li>
										<li>
											<a href="{{ url('manage-listing/'.$space_id.'/calendar') }}" data-prevent-default="true" class="text-muted link-icon">
												<i name="share" class="icon icon-share"></i>
												<span>
													{{ trans('messages.lys.export_calc') }}
												</span>
											</a>
										</li>
									</ul>
									<p class="get_n_day" hidden="hidden">
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		--}}
	</div>
</div>
<!-- Center Part Ending -->
<style>
	.hiddenEvent {
		display: none;
	}
	.status-r {
		background: #e82953 !important;
	}
	.status-r .fc-content *
	{
	color: #fff !important;
	}
	.status-n {
		background: #767676;
		opacity: 1;
	}
	.status-p {
		background: #484848;
		opacity: 1;
	}
	.status-b {
		background: #767676;
	}
	.status-a {
		background: #A2BABF !important;
	}
	.status-a .fc-content *
	{
	color: #333 !important;
	}
	.fc-today{
		opacity: 1;
	}
</style>