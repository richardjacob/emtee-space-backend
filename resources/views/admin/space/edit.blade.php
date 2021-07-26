@extends('admin.template')
@section('main')

@include('admin.space.template')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="space_admin">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit Space
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="../dashboard">
                    <i class="fa fa-dashboard"></i>Home
                </a>
            </li>
            <li>
                <a href="{{route('admin.space')}}">Spaces</a>
            </li>
            <li class="active">
                Edit
            </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Edit Space Form
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-header with-border">
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_1" ng-click="go_to_edit_step(1)" disabled>
                            Calendar
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_2" ng-click="go_to_edit_step(2)" step="space_type">
                            Space Type
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_3" ng-click="go_to_edit_step(3)" step="basics">
                            Basics
                        </a>
                       
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_5" ng-click="go_to_edit_step(5)" step="guest_count">
                            Guests
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_6" ng-click="go_to_edit_step(6)" step="amenities">
                            Amenities
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_7" ng-click="go_to_edit_step(7)" step="services">
                            Services
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_8" ng-click="go_to_edit_step(8)" step="location">
                            Location
                        </a>
                         <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_4" ng-click="go_to_edit_step(4)" step="guest_access">
                            Guest Access
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_9" ng-click="go_to_edit_step(9)" step="photos">
                            Photos
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_10" ng-click="go_to_edit_step(10)" step="style">
                            Style
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_11" ng-click="go_to_edit_step(11)" step="special_features">
                            Special Features
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_12" ng-click="go_to_edit_step(12)" step="space_rules">
                            Space Rules
                        </a>
                         <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_13" ng-click="go_to_edit_step(13)" step="description">
                            Description
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_14" ng-click="go_to_edit_step(14)" step="activity">
                            Activities
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_15" ng-click="go_to_edit_step(15)" step="price">
                            Price
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_16" ng-click="go_to_edit_step(16)" step="availability">
                            Availability
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning tab_btn" id="tab_btn_17" ng-click="go_to_edit_step(17)" step="rules">
                            Rules
                        </a>
                    </div>
                    {!! Form::open(['url' => ADMIN_URL.'/edit_space/'.$space->id, 'class' => 'form-horizontal', 'id' => 'space_form', 'files' => true, 'ng-cloak' => 'ng-cloak']) !!} 
                        <input type="hidden" value="{{ $space->id }}" name="space_id" id="space_id"> 
                        <div id="sf1" class="frm">
                            <fieldset class="box-body">
                                <div id="monthly_calendar" class="calendar col-12 col-lg-12 mt-4 mb-1" ng-init="month_calendar_data={{ json_encode($month_calendar) }};"></div>
                            </fieldset>
                            <fieldset class="box-body">
                                <div id="calendar" class="calendar col-12 col-lg-12 mt-4 mb-1 weekly_cal" ng-test="@{{ monthclass==true ? 'monthly_cal' : ''}}" ng-class="monthclass==true ? 'monthly_cal' : ''"  ng-init="calendar_data={{ json_encode($calendar) }};"></div>
                            </fieldset>
                        </div>
                        <div id="sf2" class="frm">
                            @yield('space_type')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf3" class="frm">
                            @yield('basic')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf4" class="frm">
                            @yield('guest_access')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf5" class="frm">
                            @yield('guests')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf6" class="frm">
                            @yield('amenities')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf7" class="frm">
                            @yield('services')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf8" class="frm">
                            @yield('location')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf9" class="frm">
                            @yield('photos')
                                <ul class="row list-unstyled sortable all-slides" id="js-photo-grid">
                                    @foreach($space_photos as $row)
                                        <li id="photo_li_{{ $row->id }}" class="col-4 col-lg-3 row-space-4 ng-scope slide photo_drag_item cls_photosize">
                                            <div class="card photo-item">
                                                <div id="photo-5" class="photo-size photo-drag-target js-photo-link">
                                                </div>
                                                <a href="#" class="media-photo media-photo-block text-center photo-size">
                                                    <input type ='hidden' id="hidden_image" name='hidden_image[]' value="{{ $row->id}}">
                                                    <img alt="" class="img-responsive-height" src="{{ $row->name }}">
                                                </a>
                                                <button class="delete-photo-btn overlay-btn js-delete-photo-btn" data-photo-id="{{ $row->id }}" type="button">
                                                    <i class="fa fa-trash" style="color:white;">
                                                    </i>
                                                </button>
                                                <div class="panel-body panel-condensed">
                                                    <textarea tabindex="1" class="input-large highlights ng-pristine ng-untouched ng-valid" id="hidden_high" name='hidden_high[]' data-photo-id="{{ $row->id }}" placeholder="What are the highlights of this photo?" rows="3" name="5">{{ $row->highlights }}</textarea>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                <input type ='hidden' id="hidden_image" name='hidden_image[]' value="">
                                </ul>
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf10" class="frm">
                            @yield('style')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf11" class="frm">
                            @yield('special_features')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf12" class="frm">
                            @yield('space_rules')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf13" class="frm">
                            @yield('description')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf14" class="frm">
                            @yield('activity')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf15" class="frm">
                            @yield('edit_space_price')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf16" class="frm">
                            @yield('availability')
                            @yield('edit_page_submit')
                        </div>
                        <div id="sf17" class="frm">
                            @yield('rules')
                            @yield('edit_page_submit')
                        </div>
                        <!-- /.box-body -->
                        <!-- /.box-footer -->
                    {!! Form::close() !!}
                </div>
                <!-- /.box -->
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<style type="text/css">
ul.list-unstyled {
  width:100%;
  margin-bottom:20px;
  overflow:hidden;
}
.list-unstyled > li{
  line-height:1.5em;
  float:left;
  display:inline;
}
.price_rules input, .availability_rules input {
  margin-bottom: 0px;
}
.input-suffix {
  padding: 6px 10px;
}
#double li  {
  width:50%;
}
#triple li  {
  width:33.333%;
}
#quad li {
  width:25%;
}
#six li {
  width:16.666%;
}

@media (max-width: 760px) {
  #triple li{
    width: 100% !important;
  }
}
@media (min-width: 765px) and (max-width: 1000px) {
  #triple li{
    width: 50% !important;
  }
}
@media (min-width: 1280px) and (max-width: 2000px) {
  .sidebar {
    position: relative !important; top: 0px !important;
  }
}

#ajax_container {
  float: none !important; 
}
.btn-warning{
  margin-bottom: 10px;
}
.sortable-placeholder1 {
  float: left;
  position: relative;
  min-height: 1px;
  padding-left: 12.5px;
  padding-right: 12.5px;
  width: 200px;
  border: 1px dashed #82888a;
  height: 255px;
}
.hiddenEvent{
  display: none;
}
.fc-other-month .fc-day-number {
  /*display:none;*/
}
td.fc-other-month .fc-day-number {
  /*visibility: hidden;*/
}
/*.status-r {
    background: #E2B4B6 !important;
}
.status-r .fc-content *
{
color: #333 !important;
}
.status-n {
    background: #8FDF82 !important;
    opacity: .3 !important;
}

.status-b {
    background: #8592FF !important;
}
.status-a {
    background: #A2BABF !important;
}
.status-a .fc-content *
{
color: #333 !important;
}*/
    .status-r {
        background: #17a498 !important;
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
    .calendar.monthly_cal .status-prev span.notes{
        color: #fff !important;
    }
</style>
@endsection 
