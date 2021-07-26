@include('common.head')

@if((!in_array(Route::currentRouteName(),['manage_space.new','manage_space'])) || request()->segment(3) == 'home')
	@if(!isset($exception))
		@if (Route::current()->uri() != 'api_payments/book/{id?}') 
		 	@if(Session::get('get_token')=='')
		   		@include('common.header')
		 	@endif
		@endif
	@else
	    @if(session('get_token')=='')
			@include('common.header')
		@endif
	@endif
@endif

@yield('main')

@if (!isset($exception))
	@if (Route::current()->uri() != 'payments/book/{id?}' && Route::current()->uri() != 'reservation/receipt' && Route::current()->uri() != 'api_payments/book/{id?}')
	    @if(Session::get('get_token')=='')
			@include('common.footer')
		@endif
	@endif
@else
    @if(session('get_token')=='')
		@include('common.footer')
	@endif
@endif

@include('common.foot')