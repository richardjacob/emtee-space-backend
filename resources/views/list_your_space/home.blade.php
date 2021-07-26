@extends('template')
@section('main')
<main class="whole_list">
	<div class="cls_managetop">
		<!-- Center Part Starting  -->
		
		<div class="manage-content {{($space_step == 'home') ? 'cls_listing_home' : ''}}">
			<div class="manage-listing-container d-lg-flex  ">
					<div class="col-lg-7 col-md-12 p-0" ng-init="steps_status = {{ json_encode($space_status->all_status)}};progress_style = {width: {{ $percent_completed }}+ '%' };">
						<div class="cls_flexd">
							<div class="nav-sections cls_mlist">
								<h3>
									@lang('messages.steps.tell_us_your_space',['first_name' => auth()->user()->first_name])
									<span class="d-inline-block h6"> @lang('messages.steps.more_you_share_get_book') </span>
								</h3>
								<div class="progress cls_prgress">
							      <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" ng-style="progress_style">
							      </div>
							    </div>
							    <p> @lang('messages.steps.listing_completed',['percent' => $percent_completed]) </p>

								<ul class="list-unstyled list-nav-link cls_ul">
									<li class="nav-item mt-4 row" data-track="basics">
										<div class="col-md-10">
											<span class="text-truncate font-weight-bold h5" style="display: inline-block; margin-bottom: 0;">
												1. @lang('messages.steps.the_basics')
												<a href="{{ route('manage_space',['id' => $result->id, 'page' => 'basics', 'step_num' => 1]) }}" class="h6 theme-link" ng-show="steps_status.basics"> Need to make a change? </a>
											</span>
											<p class="text-truncate h6 ml-2"> @lang('messages.steps.basics_desc') </p>
											<a href="{{ route('manage_space',['space_id' => $result->id, 'page' => 'basics', 'step_num' => 1]) }}" class="btn btn-primary m-2" ng-hide="steps_status.basics">
												@lang('messages.new_space.continue')
											</a>
											<span class="text-truncate h6 ml-2" ng-hide="steps_status.basics"> {{ $result->steps_status->basics['remaining_steps']}} @lang('messages.steps.steps_to_complete') </span>

										</div>
										<div class="col-md-2">
											<img class="img step_completed-icon float-right" src="@asset(images/completed.png)" ng-if="steps_status.basics">
										</div>
									</li>

									<li class="nav-item mt-4 row" data-track="setup">
										<div class="col-md-10">
											<span class="text-truncate font-weight-bold h5" style="    display: inline-block;margin-bottom: 0;">
												2. @lang('messages.steps.setup')
												<a href="{{ route('manage_space',['space_id' => $result->id, 'page' => 'setup', 'step_num' => 1]) }}" class="h6 theme-link" ng-show="steps_status.setup"> @lang('messages.reviews.edit') </a>
											</span>
											<p class="text-truncate h6 ml-2"> @lang('messages.steps.setup_desc') </p>
											<a href="{{ route('manage_space',['space_id' => $result->id, 'page' => 'setup', 'step_num' => 1]) }}" class="btn btn-primary m-2" ng-hide="steps_status.setup">
												@lang('messages.new_space.continue1')
											</a>
											<span class="text-truncate h6 ml-2" ng-hide="steps_status.setup"> {{ $result->steps_status->setup['remaining_steps']}} @lang('messages.steps.steps_to_complete1') </span>
										</div>
										<div class="col-md-2">
											<img class="img step_completed-icon float-right" src="@asset(images/completed.png)" ng-if="steps_status.setup">
										</div>
									</li>

									<li class="nav-item mt-4 row" data-track="ready_to_host">
										<div class="col-md-10">
											<span class="text-truncate font-weight-bold h5" style="    display: inline-block;margin-bottom: 0;">
												3. @lang('messages.steps.ready_to_host')
												<a href="{{ route('manage_space',['space_id' => $result->id, 'page' => 'ready_to_host', 'step_num' => 1]) }}" class="h6 theme-link" ng-show="steps_status.ready_to_host"> @lang('messages.reviews.edit') </a>
											</span>
											<p class="text-truncate h6 ml-2"> @lang('messages.steps.ready_to_host_desc') </p>
											<a href="{{ route('manage_space',['space_id' => $result->id, 'page' => 'ready_to_host', 'step_num' => 1]) }}" class="btn btn-primary m-2" ng-hide="steps_status.ready_to_host">
												@lang('messages.new_space.continue1')
											</a>
											<span class="text-truncate h6 ml-2" ng-hide="steps_status.ready_to_host"> <!-- {{ $result->steps_status->ready_to_host['remaining_steps']}}  -->
											1 @lang('messages.steps.steps_to_complete') </span>
										</div>
										<div class="col-md-2">
											<img class="img step_completed-icon float-right" src="@asset(images/completed.png)" ng-if="steps_status.ready_to_host">
										</div>
									</li>
									<div class="mt-4 d-flex justify-content-between align-items-center " data-track="ready_to_host_desc">
									<div class="">
									<a href="{{ route('space_details',['id' => $result->id]) }}" class="btn btn-primary" target="listing_{{ $result->id }}">
											@lang('messages.lys.preview')
										</a>
									</div>
									@if($result->status != '')
									<div class="cls_fix_btm d-none">
										<div class="publish-list">
											<button class="btn btn-default Publish-btn">
											
												@lang('messages.header.publish')
											</button>
										</div>									
										<div class="pending-list d-none">
											<button class="btn btn-default">
											{{ $result->admin_status }}
											</button>
										</div>
										
									</div>
									@endif 
								</div>
								</ul>
								
							</div>
						</div>
					</div>

					<div class="col-lg-5 col-md-12">
					    <h1>
					    	<img src="@asset(images/list_space/space_home.png)" alt="" style="height: 500px;">
					    </h1>
				  	</div>
			</div>
		</div>
		</div>

		<!-- Center Part Ending -->

	<!-- Modal -->
<div class="myAlert-top alert alert-success" style="display: none;">
	<strong>Thank you for your time.</strong>The changes made to your listing are now being reviewed. We will notify you shortly.
</div>
<div id="myModal" class="modal fade reservation_complete" role="dialog" >
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"></button>
        <h4 class="modal-title">@lang('messages.your_listing.listing_completed_desc0')</h4>
      </div>
      <div class="modal-body">  
      	<b>@lang('messages.your_listing.listing_completed_desc1_title')</b>   	
        <p>@lang('messages.your_listing.listing_completed_desc1')</p>
        <b>@lang('messages.your_listing.listing_completed_desc2_title')</b>
        <p>@lang('messages.your_listing.listing_completed_desc2')</p>
        <b>@lang('messages.your_listing.listing_completed_desc3_title')</b>
        <p>@lang('messages.your_listing.listing_completed_desc3')</p>
        <b>@lang('messages.your_listing.listing_completed_desc4_title')</b>
        <p>@lang('messages.your_listing.listing_completed_desc4')</p>
        <b>@lang('messages.your_listing.listing_completed_desc5_title')</b>
        <p>@lang('messages.your_listing.listing_completed_desc5')</p>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div> -->
    </div>

  </div>
</div>
</main>
@endsection
@push('scripts')
<!-- <script type="text/javascript">
 var show_popup  = {!! $percent_completed  !!};
 console.log(show_popup);
 if(show_popup==100)
  $('.reservation_complete').modal('show');  
</script> -->
@endpush