@extends('template')
@section('main')
<main role="main" id="site-content">
  <div class="container py-4 py-md-5">
    <div class="log-page p-4">
      <h4 class="text-center mb-3">    
        {{ trans('messages.login.reset_pwd') }}
      </h4>
      <div class="log-form">  
        {!! Form::open(['url' => url('forgot_password')]) !!}
        <div class="control-group">
          <p>{{ trans('messages.login.reset_pwd_desc') }}</p>
          @if ($errors->has('email')) 
          <p class="error-msg mb-1">{{ $errors->first('email') }}</p> 
          @endif
          <div class="d-flex align-items-center">
            {!! Form::email('email', '', ['placeholder' => trans('messages.login.email'), 'id' => 'forgot_email', 'class' => $errors->has('email') ? 'decorative-input inspectletIgnore invalid' : 'decorative-input inspectletIgnore']) !!}
          </div>    
        </div>
        <div class="mt-3">
          <button id="reset-btn" class="btn btn-primary" type="submit">
            {{ trans('messages.login.send_reset_link') }}
          </button>      
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</main>
@stop