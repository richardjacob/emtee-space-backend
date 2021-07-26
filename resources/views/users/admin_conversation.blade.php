@extends('template')
@section('main')
<main id="site-content" role="main">
  @include('common.subheader')
  <div class="container mt-4">
    <h3 class="h2 row-space-4 conversation_head">
      {{ trans('messages.inbox.resubmit_reasons')}}:
    </h3>
    <p>
      <a href="{{ url('users/edit_verification')}}" style="color: #e82953;"> 
        {{ trans('messages.inbox.click_here_to_resubmit')}} 
      </a>
    </p>
    <div class="row">
      <div class="col-12 col-md-12 col-lg-8 host_conver">
        <ul class="list-unstyled host_ul">
          @foreach($messages as $message)
          <div id="thread-list">
            <li id="question2_post_11" class="thread-list-item cls_resubmit mb-3">
              <div class="row row-condensed">
                <div class="col-2 text-center">
                  <a aria-label="Test" data-behavior="tooltip" class="media-photo media-round" href="#">
                    <img width="50" height="50" class="" title="{{ $message->admin_name }}" src="@asset(admin_assets/dist/img/avatar04.png)" alt="{{ $message->admin_name }}">
                  </a>
                </div>
                <div class="col-sm-10 col-md-10">
                  <div class="row-space-4">
                    <div class="panel panel-quote panel-quote-flush panel-quote-right card p-2">
                      <div class="panel-body">
                        <div class="message-text">
                          <b>
                            {{ trans('messages.inbox.'.$message->message_type_reason) }} :
                          </b>
                          <p class="trans" style="margin: 0px;">
                            {{ $message->message }}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="time-container text-muted text-left">
                      <small title="{{ $message->created_time }}" class="time">
                        {{ $message->created_time }}
                      </small>
                      <small class="exact-time d-none">
                        {{ $message->created_time }}
                      </small>
                    </div>
                  </div>
                </div>
                
              </div>
            </li>
          </div>
          @endforeach
        </ul>
      </div>
      <div class="col-md-5 col-lg-4 host-mini"></div>
    </div>
  </div>
</main>
@stop