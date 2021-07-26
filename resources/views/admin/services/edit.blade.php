@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit {{ $main_title }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('admin_dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('services') }}">{{ $main_title }}</a></li>
        <li class="active">Add</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-8 col-sm-offset-2">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Edit {{ $main_title }} Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => route('update_services',['id' => $result->id]), 'class' => 'form-horizontal','id'=>'form']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="multiple_lang">
                  <div class="form-group">
                    <label for="input_lang" class="col-sm-3 control-label">
                      Language <em class="text-danger">*</em>
                    </label>
                    <div class="col-sm-6">
                      @foreach($language as $lang)
                        @php
                          $val[$lang->value] = $lang->name;
                        @endphp 
                      @endforeach
                       {!! Form::select('lang_code[]', $val, 'en', ['class' => 'form-control go','id'=>'lang_1']) !!}
                    </div>
                  </div>

                <div class="form-group">
                  <label for="input_name" class="col-sm-3 control-label">
                    Name<em class="text-danger">*</em>
                  </label>
                  <div class="col-sm-6">
                  {!! Form::text('name[]', $result->name, ['class' => 'form-control name-input', 'id' => 'input_name', 'placeholder' => 'Name','required']) !!}  
                  </div>
                </div>                    
               </div>
               <div class="multiple_lang_add">
                @php
                  $i=2;
                @endphp 
                
                @foreach($langresult as $langs)
                  <div class="multiple_lang">  
                    <input type="hidden" value="{{ $langs->id }}" id ="property_type_id" name="lang_id[]"">
                    
                    <div class="form-group">
                      <label for="input_lang" class="col-sm-3 control-label">
                        Language<em class="text-danger">*</em>
                      </label>
                      <div class="col-sm-6">
                        @foreach($language as $lang)
                          @php
                            $val[$lang->value] = $lang->name;
                          @endphp 
                        @endforeach
                        {!! Form::select('lang_code[]', $val, $langs->lang_code, ['class' => 'form-control go','id'=>'lang_'.$i]) !!}
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="input_name" class="col-sm-3 control-label">
                        Name <em class="text-danger">*</em>
                      </label>
                      <div class="col-sm-6">
                        {!! Form::text('name[]',$langs->name, ['class' => 'form-control name-input', 'id' => 'input_name_'.$i, 'placeholder' => 'Name','required']) !!}
                      </div>
                    </div>
                    <button type="button" class="btn btn-danger remove_lang" style="float:right;">Remove</button>
                  </div>
                  @php
                    $i++;
                  @endphp 
                @endforeach
                </div>
                <input type="hidden" id="increment" value="{{ count($langresult) + 1 }}"> 
                <div class="form-group" style="float:right;margin-right: 10px;">
                  <button type="button" class="btn btn-primary add_lang_bed" > Add </button>
                </div>
              
                <div class="form-group status">
                  <label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status,['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select','required']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span> 
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" id="edt_btn" name="submit" value="submit">Submit</button>
                <a href="{{ route('services') }}" class="btn btn-default pull-left cancel" name="cancel" value="cancel">Cancel</a>
              </div>
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
@stop