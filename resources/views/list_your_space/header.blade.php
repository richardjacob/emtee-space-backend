<div class="manage-listing-header" id="js-manage-listing-header">
  <div class="listing-nav d-flex justify-content-between align-items-center">
    <div>
      <a class="navbar-brand" href="{{route('home_page')}}">
          <img src="{{ EMAIL_LOGO_URL }}" />
        </a>
      <span id="listing-name">
        <span class="d-lg-block d-none d-md-block">
          {{ ($result->name == '') ? $result->sub_name : $result->name }}
        </span>
      </span>
      <span class="see-all-listings-link ml-1" ng-if="space_id != ''">
        (<a href="{{ route('manage_space',['id' => $result->id, 'page' => 'home']) }}">
          {{ trans('messages.new_space.listing_overview') }}
        </a>)
      </span>
    </div>
    <div>
      <button class="d-lg-block d-none" ng-click="saveAndClose(current_step_name)" ng-if="space_id != ''" ng-disabled="is_loading">
        @lang('messages.new_space.save_close')
       <span> <img class="d-lg-none d-sm-block" src="@asset(images/save.png)" alt="save"> </span>
      </button>
       <button class="d-lg-none d-sm-block" ng-click="saveAndClose(current_step_name)" ng-if="space_id != ''" ng-disabled="is_loading">
         @lang('messages.new_space.save_close')
        <!-- <img class="d-lg-none d-sm-block" src="@asset(images/save.png)" alt="save" style="height: 40px;width: 40px;object-fit: cover;"> -->
      </button>
      <a class="mr-4" href="{{ route('home_page') }}" ng-if="space_id == ''">
        @lang('messages.new_space.back_home')
      </a>
    </div>
  </div>

</div>
