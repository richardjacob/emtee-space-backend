@if(Route::currentRouteName() == 'home_page')
<footer ng-controller="footer" class="cls_ftrnew">
	<div class="container">

		<a class="footer-toggle_menu d-lg-none d-sm-block" style="position: absolute;right: 20px;top: 10px;"><i class="fa fa-remove"></i> </a>
		<img src="images/logos/logo.png" width="178" style="margin-bottom: 80px;">
		<div class="footer-wrap row justify-content-between pb-4">
			
			<div class="col-md-4 col-lg-4 mt-4 mt-md-0">
				<h2>SITE MAP</h2>
				<ul class="list-layout">
					@foreach($company_pages as $company_page)
					<li>
						<a href="{{ url($company_page->url) }}" class="link-contrast">
							{{ $company_page->name }}
						</a>
					</li>
					@endforeach
					<li>
						<a href="{{ url('contact') }}" class="link-contrast">
							@lang('messages.contactus.contactus')
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-4 col-lg-4 d-none d-md-block">
				<h2> CONTACT </h2>
				<ul class="list-layout">
					<li>
						<a href="{{ url('invite') }}" class="link-contrast">
							@lang('messages.referrals.travel_credit')
						</a>
					</li>
					@foreach($discover_pages as $discover_page)
					<li>
						<a href="{{ url($discover_page->url) }}" class="link-contrast">
							{{ $discover_page->name }}
						</a>
					</li>
					@endforeach
				</ul>
				<p class="paragraph-3 white">
					P: <a href="tel:{{$support_number}}" class="link-3"> {{ $support_number }} </a>
					<br>
					E: <a href="mailto:{{ $admin_email }}" class="link-3"> {{ $admin_email }} </a>
				</p>
			</div>
			<div class="col-md-4 col-lg-4 social-links">
				<h2>CONNECT WITH US</h2>
				<ul>
					@for($i=0; $i < count($join_us); $i++)
					@if($join_us[$i]->value)
					<li>
						<a href="{{ $join_us[$i]->value }}" class="link-contrast footer-icon-container" target="_blank" title="{{ ucfirst($join_us[$i]->name) }}">
							<span class="screen-reader-only">
								{{ ucfirst($join_us[$i]->name) }}
							</span>
							<i class="icon footer-icon icon-{{ str_replace('_','-',$join_us[$i]->name) }}"></i>
						</a>
					</li>
					@endif
					@endfor
				</ul>
			</div>
		</div>
		<div class="copyright d-md-flex justify-content-between align-items-center text-center text-md-left pt-3">
			<div class="company-info">
				<p>© Copyright {{ date("Y") }} {{ $site_name }}, All Rights Reserved.</p>
			</div>
			<div class=" mt-3 mt-md-0 d-flex align-items-center justify-content-between">
				<div class="language-selector" style="padding-right: 15px;">
					{!! Form::select('language',$language, (Session::get('language')) ? Session::get('language') : $default_language[0]->value, ['class' => 'language-selector footer-select', 'aria-labelledby' => 'language-selector-label', 'id' => 'language_footer']) !!}
				</div>
				<div class="currency-selector ">
					{!! Form::select('currency',$currency, (Session::get('currency')) ? Session::get('currency') : $default_currency[0]->code, ['class' => 'currency-selector footer-select', 'aria-labelledby' => 'currency-selector-label', 'id' => 'currency_footer']) !!}
				</div>
			</div>
		</div>
	</div>
</footer>
@endif
<footer ng-controller="footer" class="cls_ftrnew cls_showftr">
	<div class="container">

		<a class="footer-toggle_menu d-lg-none d-sm-block" style="position: absolute;right: 20px;top: 10px;"><i class="fa fa-remove"></i> </a>
		<img src="images/logos/logo.png" width="178" style="margin-bottom: 80px;">
		<div class="footer-wrap row justify-content-between pb-4">
			
			<div class="col-md-4 col-lg-4 mt-4 mt-md-0">
				<h2>SITE MAP</h2>
				<ul class="list-layout">
					@foreach($company_pages as $company_page)
					<li>
						<a href="{{ url($company_page->url) }}" class="link-contrast">
							{{ $company_page->name }}
						</a>
					</li>
					@endforeach
					<li>
						<a href="{{ url('contact') }}" class="link-contrast">
							@lang('messages.contactus.contactus')
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-4 col-lg-4 d-none d-md-block">
				<h2> CONTACT </h2>
				<ul class="list-layout">
					<li>
						<a href="{{ url('invite') }}" class="link-contrast">
							@lang('messages.referrals.travel_credit')
						</a>
					</li>
					@foreach($discover_pages as $discover_page)
					<li>
						<a href="{{ url($discover_page->url) }}" class="link-contrast">
							{{ $discover_page->name }}
						</a>
					</li>
					@endforeach
				</ul>
				<p class="paragraph-3 white">
					P: <a href="tel:{{$support_number}}" class="link-3"> {{ $support_number }} </a>
					<br>
					E: <a href="mailto:{{ $admin_email }}" class="link-3"> {{ $admin_email }} </a>
				</p>
			</div>
			<div class="col-md-4 col-lg-4 social-links">
				<h2>CONNECT WITH US</h2>
				<ul>
					@for($i=0; $i < count($join_us); $i++)
					@if($join_us[$i]->value)
					<li>
						<a href="{{ $join_us[$i]->value }}" class="link-contrast footer-icon-container" target="_blank" title="{{ ucfirst($join_us[$i]->name) }}">
							<span class="screen-reader-only">
								{{ ucfirst($join_us[$i]->name) }}
							</span>
							<i class="icon footer-icon icon-{{ str_replace('_','-',$join_us[$i]->name) }}"></i>
						</a>
					</li>
					@endif
					@endfor
				</ul>
			</div>
		</div>
		<div class="copyright d-md-flex justify-content-between align-items-center text-center text-md-left pt-3">
			<div class="company-info">
				<p>© Copyright 2019 {{ $site_name }}, All Rights Reserved.</p>
			</div>
			<div class=" mt-3 mt-md-0 d-flex align-items-center justify-content-between">
				<div class="language-selector" style="padding-right: 15px;">
					{!! Form::select('language',$language, (Session::get('language')) ? Session::get('language') : $default_language[0]->value, ['class' => 'language-selector footer-select', 'aria-labelledby' => 'language-selector-label', 'id' => 'language_footer']) !!}
				</div>
				<div class="currency-selector ">
					{!! Form::select('currency',$currency, (Session::get('currency')) ? Session::get('currency') : $default_currency[0]->code, ['class' => 'currency-selector footer-select', 'aria-labelledby' => 'currency-selector-label', 'id' => 'currency_footer']) !!}
				</div>
			</div>
		</div>
	</div>
</footer>
<div class="search-mobile-modal modal fade" role="dialog" id="search-modal-sm">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
				</button>
				<h1 class="modal-title">{{ trans('messages.home.search') }}</h1>
			</div>
			<div class="modal-body">
				<input type="hidden" name="source" value="mob">
				<div class="col-md-12 p-0">
					<span class="search_location_error d-none">
						{{ trans('messages.home.search_validation') }}
					</span>
					<label class="d-block" for="header-location-sm">
						<span class="screen-reader-only">
							{{ trans('messages.header.where_are_you_going') }}
						</span>
						<input type="text" placeholder="{{ trans('messages.header.where_are_you_going') }}" autocomplete="off" name="location" id="header-search-form-mob" class="location input-large" value="{{ @$location }}">
					</label>
				</div>
				<div class="col-md-12 p-0">
					<div class="select-date d-flex flex-column cls_mulselect">
						<select name="activity_type" class="selectpicker" data-show-subtext="true" data-live-search="true">
							<option value="" disabled selected> @lang('messages.new_space.select_activity') </option>
							@foreach($header_activties as $activity)
								<option value="{{ $activity->id }}" > {{ $activity->name }} </option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer justify-content-center mt-3">
					<button type="submit" id="search-form-sm-btn" class="btn btn-primary d-flex align-items-center">
					<i class="icon icon-search mr-2"></i>
					<span> @lang('messages.new_home.find_space') </span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>