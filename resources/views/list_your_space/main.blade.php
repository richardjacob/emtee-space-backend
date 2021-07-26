@extends('template')
@section('main')
<main id="site-content" class="whole_list" role="main" ng-controller="manage_listing">
  <div class="manage-listing" id="js-manage-listing" ng-init="new_space='{{isset($new_space) ? $new_space:false}}';listing_home_url='{{ route('manage_space',['space_id' => $result->id, 'page' => 'home']) }}'">
    <div id="ajax_header">
      @include('list_your_space.header')
    </div>

    <!-- Center Part Starting  -->
    <div class="manage-listing-container d-flex cls_managetop">
      <div class="manage-content col-md-12 cls_pad0" ng-class="page_loading">
        <div id="ajax_container" ng-init="steps={{$steps}};space={{ $result }};space_id='{{ $result->id }}';activity_types={{ $activity_types }};activities={{ $activities }};sub_activities={{ $sub_activities }};activity_currency='{{ $activity_currency}}'">
          @include('list_your_space.'.$space_step)
        </div>
      </div>
    </div>
    <!-- Center Part Ending -->
  </div>
  @include('list_your_space.popups')
</main>
@endsection