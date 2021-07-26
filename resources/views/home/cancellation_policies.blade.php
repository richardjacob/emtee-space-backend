@extends('template')
@section('main')
<main id="site-content" role="main">
	<div class="cancel-policy py-4 py-md-5">
		<div class="container">
			<h3> @lang('messages.cancel_policy.cancellation_policies') </h3>
			<p> @lang('messages.cancel_policy.site_cancel_policy_description_content', ['site_name' => $site_name]) </p>
			<div class="card">
				<div class="card-body">
					<div class="policy">
						<h5 class="font-weight-bold"> @lang('messages.cancel_policy.flexible') </h5>
						<p>
							@lang('messages.your_reservations.flexible_desc')
						</p>
					</div>
					<div class="policy mt-4">
						<h5 class="font-weight-bold"> @lang('messages.cancel_policy.moderate') </h5>
						<p>
							@lang('messages.your_reservations.moderate_desc')
						</p>
					</div>
					<div class="policy mt-4">
						<h5 class="font-weight-bold"> @lang('messages.cancel_policy.strict') </h5>
						<p>
							@lang('messages.your_reservations.strict_desc')
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection