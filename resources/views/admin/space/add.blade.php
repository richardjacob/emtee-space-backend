@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
@include('admin.space.template')
<div class="content-wrapper" ng-controller="space_admin" ng-init="add_space_steps();page='add_space';" ng-cloak>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Add Space
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="dashboard">
          <i class="fa fa-dashboard">
          </i> Home
        </a>
      </li>
      <li>
        <a href="{{route('admin.space')}}">Spaces
        </a>
      </li>
      <li class="active">Add
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
            <h3 class="box-title">Add Space Form
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {{ Form::open(['url' => ADMIN_URL.'/add_space', 'class' => 'form-horizontal', 'id' => 'space_form', 'files' => true]) }}
          <div class="box-body" ng-cloak>
            <div class="box-header with-border">
              <h4 class="box-title">Step @{{step+1}} of @{{steps.length}} - @{{step_name}}</h4>
            </div>
            <p class="text-danger">(*)Fields are Mandatory</p>
            <div id="sf1" class="frm hide" data-step-name="Calendar">

            </div>
            <div id="sf2" class="frm" data-step-name="Space Type">
              @yield('space_type')
            </div>
            <div id="sf3" class="frm" data-step-name="Basics">
              @yield('basic')
            </div>
            <div id="sf4" class="frm" data-step-name="Guest Access">
              @yield('guest_access')
            </div>
            <div id="sf5" class="frm" data-step-name="Guests">
              @yield('guests')
            </div>
            <div id="sf6" class="frm" data-step-name="Amenities">
              @yield('amenities')
            </div>
            <div id="sf7" class="frm" data-step-name="Services">
              @yield('services')
            </div>
            <div id="sf8" class="frm" data-step-name="Location">
              @yield('location')
            </div>
            <div id="sf9" class="frm" data-step-name="Photos">
              @yield('photos')
            </div>
            <div id="sf10" class="frm" data-step-name="Style">
              @yield('style')
            </div>
            <div id="sf11" class="frm" data-step-name="Special Features">
              @yield('special_features')
            </div>
            <div id="sf12" class="frm" data-step-name="Space Rules">
              @yield('space_rules')
            </div>
            <div id="sf13" class="frm" data-step-name="Description">
              @yield('description')
            </div>
            <div id="sf14" class="frm" data-step-name="Activities">
              @yield('activity')
            </div>
            <div id="sf15" class="frm" data-step-name="Price">
              @yield('add_space_price')
            </div>
            <div id="sf16" class="frm" data-step-name="Availability">
              @yield('availability')
            </div>
            <div id="sf17" class="frm" data-step-name="Rules">
              @yield('rules')
            </div>
            <div id="sf18" class="frm" data-step-name="User">
              <fieldset class="box-body">
                <div class="form-group">
                  <label for="user_id" class="col-sm-3 control-label">Username
                    <em class="text-danger">*
                    </em>
                  </label>
                  <div class="col-sm-6">
                    {!! Form::select('user_id', $users_list, '', ['class' => 'form-control', 'id' => 'user_id', 'placeholder' => 'Select...']) !!}
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer" ng-cloak>
            <button ng-show="step > 0" class="btn btn-warning back2" type="button" ng-click="back_step(step)"><span class="fa fa-arrow-left"></span> Back</button>
            <span class="pull-right">
              <span id="Availability_time_error" style="padding-right: 10px;"></span>
              <button class="btn btn-primary open2" type="button" ng-click="next_step(step);" ng-disabled="submit_disable==1">
                <span ng-if="step_name == 'User'"> Submit </span> 
                <span ng-if="step_name != 'User'"> Next </span>
                <span class="fa fa-arrow-right" ng-if="step_name != 'User'">
                </span>
              </button>
            </div>
          </div>
          <!-- /.box-footer -->
          {{ Form::close() }}
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
<!-- hide for admin side space_add page settings/skins problems -->
@stop
@push('scripts')
<style type="text/css">
  .price_rules input, .availability_rules input {
    margin-bottom: 0px;
  }
  .input-suffix {
    padding: 6px 10px;
  }
</style>
@endpush