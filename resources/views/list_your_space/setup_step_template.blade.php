@section('photos')
<div class="content-heading my-4" ng-init="delete_warning='@lang('messages.setup.cannot_delete')';delete_warning_desc='@lang('messages.setup.delete_warning_desc')';">
  <h3> @lang('messages.lys.photos_title') </h3>
  <p> @lang('messages.lys.photos_desc') </p>
</div>
<div id="js-photos-grid" class="photos-info mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="add-photos-button">
    <input type="file" class="d-none" name="photos[]" multiple="true" id="upload_photos" accept="image/*" onchange="angular.element(this).scope().uploadPhotos(this)">
    <button id="photo-uploader" ng-hide="photos_list.length >= 18"class="btn d-flex align-items-center my-2" onclick="$('#upload_photos').trigger('click');">
      <i class="icon icon-upload mr-2"></i>
      {{ trans_choice('messages.lys.add_photo',2) }}
    </button>
  </div>
  <div id="photo_count" ng-show="photos_list.length > 0" ng-cloak>
    <span>
      @{{ photos_list.length }}
      <span ng-hide="photos_list.length > 1"> @choice('messages.lys.photo',1) </span>
      <span ng-show="photos_list.length > 1"> @choice('messages.lys.photo',2) </span>
    </span>
  </div>
</div>
<div id="js-first-photo-text" class="my-2">
  <span> {{ trans('messages.lys.drag_image_to_set_feature') }} </span>
</div>
<div class="row sortable_image_view">
  <ul id="js-photo-grid" class="photo-grid d-flex flex-wrap w-100">
    <li ng-repeat="item in photos_list" class="col-lg-4 col-md-6 photo_drag_item" data-id="@{{ item.id }}" id="photo_li_@{{ item.id }}">
      <div class="panel photo-item">
        <input type='hidden' class="image_order_list" value="@{{ item.id }}">
        <a class="media-photo media-photo-block text-center photo-size" href="#">
          <img alt="" class="img-responsive-height" ng-src="@{{ item.name }}" >
        </a>
        <button data-photo-id="@{{ item.id }}" ng-click="delete_photo(item,'{{ trans('messages.lys.delete') }}','{{ trans('messages.lys.delete_descrip') }}')" class="delete-photo-btn overlay-btn js-delete-photo-btn">
          <i class="icon icon-trash"></i>
        </button>
        <div class="panel-body panel-condensed">
          <textarea name="@{{ item.id }}" ng-model="item.highlights" ng-keyup="keyup_highlights(item.id, item.highlights)" rows="3" placeholder="@lang('messages.lys.highlights_photo')" class="input-large highlights" tabindex="1"></textarea>
        </div>
      </div>
    </li>
  </ul>
</div>
@endsection

@section('style')
<div class="content-heading my-4">
  <h3> @lang('messages.setup.style_described_as') </h3>
</div>
<div class="mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="form-group">
    <input type="hidden" name="space_style" ng-model="space.space_style">
    <ul class="mt-3">
      @foreach($space_styles as $space_style)
      <li>
        <input type="checkbox" id="space_style_{{$space_style->id}}" class="space_style" value="{{ $space_style->id }}" ng-checked="{{ in_array($space_style->id, $prev_space_style) }}">
        <label for="space_style_{{$space_style->id}}"> {{ $space_style->name }} </label>
      </li>
      @endforeach
    </ul>
  </div>
</div>
@endsection

@section('special_features')
<div class="content-heading my-4">
  <h3> @lang('messages.setup.special_features_have') </h3>
</div>
<div class="mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="form-group">
    <input type="hidden" name="special_feature" ng-model="space.special_feature">
    <ul class="mt-3">
      @foreach($special_features as $special_feature)
      <li>
        <input type="checkbox" id="special_feature_{{$special_feature->id}}" class="special_feature" value="{{ $special_feature->id }}" ng-checked="{{ in_array($special_feature->id, $prev_special_feature) }}">
        <label for="special_feature_{{$special_feature->id}}"> {{ $special_feature->name }} </label>
      </li>
      @endforeach
    </ul>
  </div>
</div>
@endsection

@section('space_rules')
<div class="content-heading my-4">
  <h3> @lang('messages.setup.set_space_rules') </h3>
</div>
<div class="mb-3 pb-3 d-flex align-items-center justify-content-between">
  <div class="form-group">
    <input type="hidden" name="space_rule" ng-model="space.space_rule">
    <ul class="mt-3">
      @foreach($space_rules as $space_rule)
      <li>
        <input type="checkbox" id="space_rule_{{$space_rule->id}}" class="space_rule" value="{{ $space_rule->id }}" ng-checked="{{ in_array($space_rule->id, $prev_space_rule) }}">
        <label for="space_rule_{{$space_rule->id}}"> {{ $space_rule->name }} </label>
      </li>
      @endforeach
    </ul>
  </div>
</div>
@endsection

@section('description')
@include('list_your_space.description')
@endsection

@section('photos_desc')
<h1>
  <img src="@asset(images/list_space/gallery.png)" alt="">
</h1>
<h3>
  <ol>
    <li>
      <span class="font-weight-bold"> @lang('messages.steps.photos_desc1') </span>
      <span class="d-block"> @lang('messages.steps.photos_desc2') </span>
    </li>
  </ol>
</h3>
@endsection

@section('style_desc')
<h1>
  <img src="@asset(images/list_space/style.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.style_desc1') </span> @lang('messages.steps.style_desc2')
</h3>
@endsection

@section('special_features_desc')
<h1>
  <img src="@asset(images/list_space/special_feature.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.feature_desc1') </span> @lang('messages.steps.feature_desc2')
</h3>
@endsection

@section('space_rules_desc')
<h1>
  <img src="@asset(images/list_space/space_rules.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.rules_desc1',['site_name' => $site_name]) </span>
</h3>
@endsection

@section('description_desc')
<h1>
  <img src="@asset(images/list_space/description.png)" alt="">
</h1>
<h3>
  <span class="font-weight-bold"> @lang('messages.steps.description_desc1') </span>
  @lang('messages.steps.description_desc2')
</h3>
@endsection