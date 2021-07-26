<!--Location popup-->
<div class="modal fade address-modal" role="dialog" id="address-flow-view">
  <div class="modal-dialog" id="js-address-container"></div>
</div>
<!-- Save Step Details popup -->
<div id="save_warning-popup" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('messages.basics.save_changes') }}
        </h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4">
        <p> @lang('messages.basics.unsaved_warning') </p>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">
          {{ trans('messages.home.close') }}
        </button>
        <button class="btn btn-primary" ng-click="saveStep()">
          @lang('messages.basics.save_continue')
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Save Step Details popup -->
<div id="js-error" class="photo-delete-modal modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      </div>
      <div class="modal-body py-4">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn">
          {{ trans('messages.home.close') }}
        </button>
        <button class="btn btn-primary js-delete-photo-confirm" data-id="">
          {{ trans('messages.lys.delete') }}
        </button>
      </div>
    </div>
  </div>
</div>
@if($result->status == NULL)
<div id="js-list-space-tooltip" class="tooltip tooltip-bottom-left list-space-tooltip finish_tooltip" style="display: none;">
  <h4>
    {{ trans('messages.lys.listing_congratulation') }}
  </h4>
  <p>
    {{ trans('messages.lys.listing_congratulation_desc') }}
  </p>
</div>
@endif
<div class="modal fade finish-modal" aria-hidden="false" tabindex="-1">
  <div class="modal-table">
    <div class="modal-cell">
      <div class="modal-content content-container">
        <div class="panel">
          <a data-behavior="modal-close" class="modal-close" href="javascript:void(0);" onclick="window.location.reload()"></a>
          <div class="finish-modal-header"></div>
          <div class="listing-card-container">
            <div class="listing">
              <div class="panel-image listing-img">
                <a class="media-photo media-cover" target="" href="{{ url('space/'.$result->id) }}">
                  <div class="listing-img-container media-cover text-center">
                    <img alt="@{{ room_name }}" ng-src="@{{ popup_photo_name }}" data-current="0" itemprop="image">
                  </div>
                </a>
                <a class="panel-overlay-bottom-left panel-overlay-label panel-overlay-listing-label" target="" href="{{ url('space/'.$result->id) }}">
                  <div>
                    <sup class="h6">
                      <span id="symbol_finish"></span>
                    </sup>
                    <span class="price-amount">
                      @{{ popup_night }}
                    </span>
                    <sup></sup>
                  </div>
                </a>
                <div class="panel-overlay-top-right wl-social-connection-panel"></div>
              </div>
              <div class="panel-body panel-card-section">
                <div class="media">
                  <a class="media-photo-badge card-profile-picture card-profile-picture-offset" href="{{ route('show_profile',['id' => auth()->user()->id]) }}">
                    <div class="media-photo media-round">
                      <img alt="" src="{{ auth()->user()->profile_picture->src }}">
                    </div>
                  </a>
                  <h3 class="listing-name text-truncate mt-1" itemprop="name" title="d">
                    <a class="text-normal" target="" href="{{ url('space/'.$result->id) }}">
                      @{{ popup_room_name }}
                    </a>
                  </h3>
                  <div class="listing-location text-truncate" itemprop="description">
                    @{{ popup_room_type_name }} · @{{ popup_state }}, @{{ popup_country }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body finish-modal-body">
            <h3 class="text-center">
              {{ trans('messages.lys.listing_published') }}!
            </h3>
            <p class="col-11 text-center">
              {{ trans('messages.lys.listing_published_desc') }}
            </p>
            <div class="row mt-5">
              <div class="col-offset-1 col-5">
                <a target="_blank" href="{{ url('space/'.$result->id) }}" id="view-listing-button" class="btn">
                  {{ trans('messages.lys.view_listing') }}
                </a>
              </div>
              <div class="col-5">
                <a href="{{ url('manage-listing/'.$result->id.'/calendar') }}" class="btn btn-primary">
                  {{ trans('messages.lys.go_to_calendar') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div tabindex="-1" aria-hidden="true" role="dialog" class="modal fade export_pop" id="export_popup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('messages.lys.export_calc') }}
        </h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>
          {{ trans('messages.lys.copy_past_ical_link') }}
        </p>
        <input type="text" value="{{ url('calendar/ical/'.$result->id.'.ics') }}" readonly="">
      </div>
    </div>
  </div>
</div>
<div tabindex="-1" class="modal fade import_pop" id="import_popup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('messages.lys.import_new_calc') }}
        </h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>
          {{ trans('messages.lys.import_calendar_desc') }}
        </p>
        {!! Form::open(['url' => url('calendar/import/'.$result->id), 'name' => 'export']) !!}
        <div class="form-group">
          <label>
            {{ trans('messages.lys.calendar_address') }}
          </label>
          <input type="text" value="{{ old('url') }}" name="url" placeholder="{{ trans('messages.lys.ical_url_placeholder') }}" class="space-1 {{ ($errors->has('url')) ? 'invalid' : '' }}">
          <span class="text-danger">
            {{ $errors->first('url') }}
          </span>
        </div>
        <div class="form-group">
          <label>
            {{ trans('messages.lys.name_your_calendar') }}
          </label>
          <input type="text" value="{{ old('name') }}" name="name" placeholder="{{ trans('messages.lys.ical_name_placeholder') }}" class="space-1 {{ ($errors->has('name')) ? 'invalid' : '' }}">
          <span class="text-danger">
            {{ $errors->first('name') }}
          </span>
        </div>
        <button data-prevent-default="true" class="btn btn-primary" ng-disabled="export.$invalid">
          <span>
            {{ trans('messages.lys.import_calc') }}
          </span>
        </button>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<!-- Remove Already synced Calendar -->
<div tabindex="-1" class="modal fade remove_sync_popup" id="remove_sync_popup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('messages.lys.remove_calc') }}
        </h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body remove_sync_cal_container">
        <div ng-repeat="sync_cal in sync_cal_details">
          <a class="sync_cal_name" href="javascript:;" id="remove_cal_confirm" data-ical_id="@{{ sync_cal.id }}" ng-click="show_confirm_popup(sync_cal.id)">
            @{{ sync_cal.name }}
          </a>
        </div>
        <div ng-show="sync_cal_details.length == 0">
          <p>
            {{ trans('messages.lys.no_cal_synced') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Remove Already synced Calendar -->
<!-- Confirm Remove Synced Calendar -->
<div tabindex="-1" class="modal fade remove_sync_confirm_popup" id="remove_sync_confirm_popup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('messages.lys.remove_calc') }}
        </h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>
          {{ trans('messages.lys.remove_calc_confirm_message') }}
        </p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger remove_sync_button" data-dismiss="modal" class="btn btn-danger">
          {{ trans('messages.your_reservations.cancel') }}
        </button>
        <button class="btn btn-primary remove_ical_link" data-ical_id="" ng-click="remove_sync_cal()">
          {{ trans('messages.lys.delete') }}
        </button>
      </div>
    </div>
  </div>
</div>
  <!-- End Confirm Remove Synced Calendar -->