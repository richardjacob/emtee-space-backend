@extends('template')
@section('main')
<main role="main" id="site-content">
	<div class="receipt-content py-4 py-md-5">
		<div class="container">
			<div class="mb-3" id="receipt-id">
				<ul class="receipt-info">
					<li>
						{{ @$reservation_details->receipt_date }}
					</li>
					<li>
						@lang('messages.your_trips.receipt') # {{ $reservation_details->id }}
					</li>
				</ul>
			</div>
			<div class="card">
				<div class="card-body d-md-flex">
					<div class="customer-receipt">
						<h2>
						@lang('messages.your_trips.customer_receipt')
						</h2>
						<label>
							@lang('messages.your_reservations.confirmation_code')
						</label>
						<h4>
						{{ $reservation_details->code }}
						</h4>
					</div>
					<div class="mt-3 mt-md-0 ml-auto d-print-none">
						<a id="print_receipt" onclick="print_receipt()" class="btn" href="#">
							@lang('messages.your_trips.print')
						</a>
					</div>
				</div>
				<div class="card-body border-top border-bottom">
					<div class="row mb-4">
						<div class="col-md-3">
							<label>
								@lang('messages.payments.name')
							</label>
							<p>
								{{ $reservation_details->users->full_name }}
							</p>
						</div>
						<div class="col-md-2">
							<label>
								@lang('messages.your_trips.duration')
							</label>
							@if($reservation_details->hours>0)
							<p>
								{{ $reservation_details->hours }} {{($reservation_details->hours==1)?trans('messages.space_detail.hour'):trans('messages.space_detail.hours')}}
							</p>
							@endif
							@if($reservation_details->days>0)
							<p>
								{{ $reservation_details->days }} {{($reservation_details->days==1)?trans('messages.space_detail.day'):trans('messages.space_detail.days')}}
							</p>
							@endif
							@if($reservation_details->weeks>0)
							<p>
								{{ $reservation_details->weeks }} {{($reservation_details->weeks==1)?trans('messages.space_detail.week'):trans('messages.space_detail.weeks')}}
							</p>
							@endif
							@if($reservation_details->months>0)
							<p>
								{{ $reservation_details->months }} {{($reservation_details->months==1)?trans('messages.space_detail.month'):trans('messages.space_detail.months')}}
							</p>
							@endif
						</div>
						<div class="col-md-2">
							<label>
								@lang('messages.space_detail.guests')
							</label>
							<p>
								{{ $reservation_details->number_of_guests }}
							</p>
						</div>
						<div class="col-md-2">
							<label>
								@lang('messages.space_detail.space_type')
							</label>
							<p>
								{{ $reservation_details->space->space_type_name }}
							</p>
						</div>
						<div class="col-md-3">
							<label>
								@lang('messages.space_detail.event_type')
							</label>
							<p>
								{{ $reservation_details->event_type_name }}
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>
								@lang('messages.your_trips.accommodation_address')
							</label>
							<p>
								{{ $reservation_details->space->name }}
							</p>
							<p>
								{{ html_string($reservation_details->space->space_address->complete_address) }}
							</p>
						</div>
						<div class="col-md-3">
							<label>
								@lang('messages.your_trips.accommodation_host')
							</label>
							<p>
								{{ $reservation_details->space->users->full_name }}
							</p>
						</div>
						<div class="col-md-3">
							<label>
								@lang('messages.home.checkin')
							</label>
							<p>
								{{ $reservation_details->checkin_with_time }}<br>
							</p>
						</div>
						<div class="col-md-3">
							<label>
								@lang('messages.home.checkout')
							</label>
							<p>
								{{ $reservation_details->checkout_with_time }}<br>
							</p>
						</div>
					</div>
				</div>
				<div class="card-body reservation-charges">
					<h2>
					@lang('messages.your_trips.reservation_charges')
					</h2>
					<table class="table table-bordered payment-table">
						<tbody>
							@if($reservation_details->hours>0) 
							<tr>
								<th class="receipt-label">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_hour }} x
									{{ $reservation_details->hours }}
									 @if($reservation_details->hours<=1) 
			                          {{trans('messages.space_detail.hour')}}
			                          @else
			                          {{trans('messages.space_detail.hours')}}
			                          @endif  
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_hour*$reservation_details->hours }}
								</td>
							</tr>
							@endif
							@if($reservation_details->days>0) 
							<tr>
								<th class="receipt-label">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_day }} x
									{{ $reservation_details->days }}
									@if($reservation_details->days<=1) 
			                       {{trans('messages.space_detail.day')}}
			                       @else
			                       {{trans('messages.space_detail.days')}}
			                       @endif 
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_day*$reservation_details->days }}
								</td>
							</tr>
							@endif
							@if($reservation_details->weeks>0) 
							<tr>
								<th class="receipt-label">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_week }} x
									{{ $reservation_details->weeks }}
									@if($reservation_details->weeks<=1) 
			                       {{trans('messages.space_detail.week')}}
			                       @else
			                       {{trans('messages.space_detail.weeks')}}
			                       @endif 
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_week*$reservation_details->weeks }}
								</td>
							</tr>
							@endif
							@if($reservation_details->months>0) 
							<tr>
								<th class="receipt-label">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_month }} x
									{{ $reservation_details->months }}
									@if($reservation_details->months<=1) 
			                       {{trans('messages.space_detail.month')}}
			                       @else
			                       {{trans('messages.space_detail.months')}}
			                       @endif 
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->per_month*$reservation_details->months }}
								</td>
							</tr>
							@endif
							@if(@$reservation_details->special_offer_id == '' || @$reservation_details->special_offer_details->type == 'pre-approval')
							@if($reservation_details->cleaning)
							<tr>
								<th class="receipt-label">
									@lang('messages.your_reservations.cleaning_fee')
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->cleaning }}
								</td>
							</tr>
							@endif
							@endif
							<tr>
								<th class="receipt-label">
									{{ $site_name }} @lang('messages.your_reservations.service_fee')
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->service }}
								</td>
							</tr>
							@if($reservation_details->coupon_amount)
							<tr>
								<th class="receipt-label">
									@if($reservation_details->coupon_code == 'Travel_Credit')
										@lang('messages.referrals.travel_credit')
									@else
										@lang('messages.payments.coupon_amount')
									@endif
								</th>
								<td class="receipt-amount">
									-{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->coupon_amount }}
								</td>
							</tr>
							@endif
						</tbody>
						<tfoot>
						<tr>
							<th class="receipt-label">
								@lang('messages.rooms.total')
							</th>
							<td class="receipt-amount">
								{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->total }}
							</td>
						</tr>
						</tfoot>
					</table>
					<table class="table table-bordered payment-table">
						<tbody>
							<tr>
								<th class="receipt-label">
									@lang('messages.your_trips.payment_received'):
									{{ $reservation_details->receipt_date }}
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->total }}
								</td>
							</tr>
							@if($reservation_details->security)
							<tr>
								<th class="receipt-label">
									@lang('messages.your_reservations.security_fee')
								</th>
								<td class="receipt-amount">
									{{ html_string($reservation_details->currency->symbol) }}{{ $reservation_details->security }}
									<small>
									(@lang('messages.disputes.security_deposit_will_not_charge'))
									</small>
								</td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<div class="mt-4" id="legal-disclaimer">
				<p>
					@lang('messages.your_trips.authorized_to_accept',['site_name' => $site_name])
				</p>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script>
  function print_receipt()
  {
    window.print();
  }
</script>
@endpush