@extends('template')
@section('main')
<main id="site-content" role="main">
  <div class="container py-4 py-md-5">
    <div class="log-page p-4">
      <h4 class="text-center mb-3">
        {{ trans('messages.login.reset_your_pwd') }}
      </h4>    
      <div class="log-form">
        {!! Form::open(['url' => url('users/set_password'), 'id' => 'password-form']) !!}
        <input id="id" name="id" type="hidden" value="{{ $result->id }}">
        <input id="token" name="reset_token" type="hidden" value="{{ $reset_token }}">
        <div class="control-group">
          @if ($errors->has('password')) <p class="error-msg mb-1">{{ $errors->first('password') }}</p> @endif
          <div class="d-flex align-items-center">
            {!! Form::password('password', ['id' => 'new_password', 'placeholder' => trans('messages.login.new_pwd'), 'size' => '30', 'class' => $errors->has('password') ? 'invalid' : '']) !!}
            <div data-hook="password-strength" class="password-strength"></div>
          </div>
        </div>
        <div class="control-group">
          @if ($errors->has('password_confirmation')) <p class="error-msg mb-1">{{ $errors->first('password_confirmation') }}</p> @endif
          <div class="d-flex align-items-center">
            {!! Form::password('password_confirmation', ['id' => 'user_password_confirmation', 'placeholder' => trans('messages.login.confirm_pwd'), 'size' => '30', 'class' => $errors->has('password_confirmation') ? 'invalid' : '']) !!}             
          </div>            
        </div>                     
        <input class="btn btn-primary btn-block btn-large mt-3" name="commit" type="submit" value="{{ trans('messages.login.save_continue') }}">
        <div class="mt-3">
          <span>
            {{ trans('messages.login.reset_pwd_agree') }} <a class="theme-link" href="{{ url('terms_of_service') }}" target="_blank">{{ trans('messages.login.terms_service') }}</a> &amp; <a class="theme-link" href="{{ url('privacy_policy') }}" target="_blank">{{ trans('messages.login.privacy_policy') }}</a>.
          </span>
        </div>
        {!! Form::close() !!}
      </div>      
    </div>
  </div>
</main>
@stop