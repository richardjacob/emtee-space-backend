@extends('emails.template')
@section('emails.main')
<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;margin-top:1em">
	<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;margin-top:1em">
		@lang('messages.email.congratulations')! <a href="{{ $space_link }}" style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#ff5a5f;text-decoration:none" target="_blank"> @lang('messages.new_email.space_for') {{ $space_name }}</a> @lang('messages.email.was_approved_by_admin',['site_name' => $site_name ]).
	</div>

	<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;margin-top:1em">
		@lang('messages.email.not_ready_for_guests')
		<a href="{{ $calendar_link }}" style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#ff5a5f;text-decoration:none" target="_blank"> @lang('messages.email.your_calendar') </a>,
		@lang('messages.email.pervent_people_booking')
		<strong style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
		@lang('messages.email.not_available')
	</strong>
		@lang('messages.email.and_click')
		<strong style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
		@lang('messages.profile.save')
		</strong>.
	</div>
	<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;margin-top:1em">
		@lang('messages.email.please_unlist_it')
	</div>
</div>
@stop