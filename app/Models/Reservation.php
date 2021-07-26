<?php

/**
 * Reservation Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Reservation
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use JWTAuth;
use Auth;
use App\Repositories\CurrencyConversion;

class Reservation extends Model
{
	use CurrencyConversion;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reservation';

	protected $appends = ['created_at_timer', 'status_color', 'dates_subject', 'checkin_arrive', 'host_payout', 'guest_payout', 'admin_host_payout', 'admin_guest_payout',  'check_total', 'review_end_date', 'grand_total', 'avablity', 'checkin_formatted', 'checkout_formatted','checkin_with_time','checkout_with_time', 'status_language', 'review_link', 'guests_text', 'booking_period'];

	protected $convert_fields = ['per_hour','per_week','per_day','per_month','base_per_hour', 'cleaning', 'security', 'service', 'coupon_amount', 'host_fee', 'subtotal', 'total'];

	public function scopeUserRelated($query, $user_id = null)
	{
		$user_id = $user_id ?: @auth()->user()->id;

		$query = $query->where(function ($query) use ($user_id) {
			$query->where('user_id', $user_id)->orWhere('host_id', $user_id);
		});

		return $query;
	}

	public function scopeUser($query)
	{
		return $query->where('user_id', auth()->id());
	}

	public function scopeHost($query)
	{
		return $query->where('host_id', auth()->id());
	}

	public function scopeExceptContact($query)
	{
		return $query->where('type', '!=', 'contact');
	}

	// Join with space_address table
	public function reservation_times()
	{
		return $this->hasOne('App\Models\ReservationTimes');
	}
	
	// Join with Space table
	public function space()
	{
		return $this->belongsTo('App\Models\Space');
	}
	public function space_location()
	{
		return $this->belongsTo('App\Models\SpaceLocation','space_id', 'space_id');
	}

	// Join with SpaceCalendar table
	public function space_calendar()
	{
		return $this->hasMany('App\Models\SpaceCalendar', 'space_id', 'space_id');
	}

	public function guest_details()
	{
		return $this->hasMany('App\Models\ReservationGuestDetails', 'reservation_id', 'id');
	}

	// Join with users table
	public function users()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	public function host_users()
	{
		return $this->belongsTo('App\Models\User', 'host_id', 'id');
	}

	// Join with Space table
	public function activity_types()
	{
		return $this->belongsTo('App\Models\ActivityType','activity_type','id');
	}

	// Join with currency table
	public function currency()
	{
		return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
	}

	// Join with currency table
	public function refund_currency()
	{
		return $this->belongsTo('App\Models\Currency', 'paypal_currency', 'code');
	}

	// Join with messages table
	public function messages()
	{
		return $this->belongsTo('App\Models\Messages', 'id', 'reservation_id');
	}

	// Join with special_offer table
	public function special_offer()
	{
		return $this->belongsTo('App\Models\SpecialOffer', 'id', 'reservation_id')->latest();
	}
	// Join with special_offer table
	public function special_offer_details()
	{
		return $this->belongsTo('App\Models\SpecialOffer', 'special_offer_id', 'id');
	}

	// Join with payouts table
	public function payouts()
	{
		return $this->belongsTo('App\Models\Payouts', 'id', 'reservation_id');
	}

	// Get Host Payout Details
	public function hostPayouts()
	{
		return $this->belongsTo('App\Models\Payouts', 'id', 'reservation_id')->where('user_type', 'host');
	}

	// Get Guest Payout Details
	public function guestPayouts()
	{
		return $this->belongsTo('App\Models\Payouts', 'id', 'reservation_id')->where('user_type', 'guest');
	}

	// Join with host_penalty table
	public function host_penalty()
	{
		return $this->belongsTo('App\Models\HostPenalty', 'id', 'reservation_id');
	}

	// Join with reviews table
	public function reviews()
	{
		return $this->hasMany('App\Models\Reviews', 'reservation_id', 'id');
	}

	// Join with reviews table
	public function guest_reviews()
	{
		return $this->hasMany('App\Models\Reviews', 'reservation_id', 'id')->where('user_from', @Auth::user()->id);
	}

	// Join with Activities table
    public function activities()
    {
        return $this->hasOne('App\Models\Activity','id','activity');
    }

    // Join with Subactivities table
    public function sub_activities()
    {
        return $this->hasOne('App\Models\SubActivity','id','sub_activity');
    }

	// Join with payout preferences table
	public function host_payout_preferences()
	{
		return $this->belongsTo('App\Models\PayoutPreferences', 'host_id', 'user_id')->where('default', 'Yes');
	}

	protected function getDateInFormat($field, $format = PHP_DATE_FORMAT)
	{
		$date_str = (array_key_exists($field,$this->attributes))?$this->attributes[$field]:@$this->$field;
		
		return date($format, strtotime($date_str));
	}

	// Get Review Details using Review ID
	public function review_details($id)
	{
		return Reviews::find($id);
	}

	// Get Review User Details using User ID
	public function review_user($id)
	{
		if($this->attributes['user_id'] == $id) {
			$user_id = $this->attributes['host_id'];
		}
		else {
			$user_id = $this->attributes['user_id'];
		}

		return User::find($user_id);
	}

	// Get Review Remaining Days
	public function getReviewDaysAttribute()
	{
		$start_date = $this->checkout;
		$end_date = date('Y-m-d', strtotime($this->checkout . ' +14 days'));

		$datetime1 = new DateTime(date('Y-m-d'));
		$datetime2 = new DateTime($end_date);
		$interval = $datetime1->diff($datetime2);
		$days = $interval->format('%R%a');
		return $days + 1;
	}

	// Get Review Remaining Days
	public function getReviewEndDateAttribute()
	{
		$start_date = @$this->checkout;
		$end_date = date(PHP_DATE_FORMAT, strtotime(@$this->checkout . ' +14 days'));

		return $end_date;
	}

	// Get Host Payout Email ID
	public function getHostPayoutEmailIdAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['host_id'])->where('default', 'yes')->get();
		return @$payout[0]->paypal_email;
	}

	// Get Guest Payout Email ID
	public function getGuestPayoutCurrencyAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['user_id'])->where('default', 'yes')->get();
		return @$payout[0]->currency_code;
	}

	// Get Guest Payout Email ID
	public function getPaypalCurrencyAttribute()
	{
		if ($this->attributes['paypal_currency'] != null) {
			return @$this->attributes['paypal_currency'];
		}
		$payout = PayoutPreferences::where('user_id', $this->attributes['user_id'])->where('default', 'yes')->get();
		return @$payout[0]->currency_code;
	}

	// Get Host Payout Email ID
	public function getHostPayoutCurrencyAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['host_id'])->where('default', 'yes')->get();
		return @$payout[0]->currency_code;
	}

	// Get Guest Payout Email ID
	public function getGuestPayoutEmailIdAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['user_id'])->where('default', 'yes')->get();
		return @$payout[0]->paypal_email;
	}

	// Get Host Payout ID
	public function getHostPayoutIdAttribute()
	{
		$payout = Payouts::where('user_id', $this->attributes['host_id'])->where('reservation_id', $this->attributes['id'])->get();
		return @$payout[0]->id;
	}

	// Get Guest Payout ID
	public function getGuestPayoutIdAttribute()
	{
		$payout = Payouts::where('user_id', $this->attributes['user_id'])->where('reservation_id', $this->attributes['id'])->get();
		return @$payout[0]->id;
	}

	// Get Host Payout Preference ID
	public function getHostPayoutPreferenceIdAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['host_id'])->where('default', 'yes')->get();
		return @$payout[0]->id;
	}

	// Get Guest Payout Preference ID
	public function getGuestPayoutPreferenceIdAttribute()
	{
		$payout = PayoutPreferences::where('user_id', $this->attributes['user_id'])->where('default', 'yes')->get();
		return @$payout[0]->id;
	}

	// Check Host is eligible or not for amount transfer using Payouts table
	public function getCheckHostPayoutAttribute()
	{
		$check = Payouts::where('reservation_id', $this->attributes['id'])->where('user_type', 'host')->where('status', 'Completed')->get();

		if ($check->count()) {
			return 'yes';
		}
		return 'no';
	}

	// Check Guest is eligible or not for amount transfer using Payouts table
	public function getCheckGuestPayoutAttribute()
	{
		$check = Payouts::where('reservation_id', $this->attributes['id'])->where('user_type', 'guest')->where('status', 'Completed')->get();

		if ($check->count()) {
			return 'yes';
		}
		return 'no';
	}

	// Get Host Payout Amount
	public function getHostPayoutAttribute()
	{
		$check = Payouts::where('user_id', $this->attributes['host_id'])->where('reservation_id', $this->attributes['id']);
		if($check->count()) {
			return $check->first()->amount;
		}
		else {
			return $this->total - $this->service - $this->host_fee + $this->coupon_amount;
		}
	}

	// Get Host/Guest Total and check with the service and coupon amount
	public function getCheckTotalAttribute()
	{
		$host_id = $this->attributes['host_id'];

		if (request()->segment(1) == 'api') {
			$user = JWTAuth::parseToken()->authenticate();
			$user_id = $user->id;
		}
		else {
			$user_id = @auth()->user()->id;
		}

		if ($host_id == $user_id) {
			return $this->total + $this->coupon_amount - $this->service - $this->host_fee-@$this->hostPayouts->total_penalty_amount;
		}
		else {
			return $this->total;
		}
	}

	public function getGrandTotalAttribute()
	{
		$host_id = $this->attributes['host_id'];

		if ($host_id == @auth()->id()) {
			return $this->subtotal + $this->coupon_amount - $this->service;
		}
		return $this->subtotal;
	}

	// Admin host /Guest payout
	public function getAdminHostPayoutAttribute()
	{
		$check = Payouts::where('user_id', $this->attributes['host_id'])->where('reservation_id', $this->attributes['id'])->get();

		if ($check->count()) {
			return $check[0]->amount;
		}
		return 0;
	}

	public function getAdminGuestPayoutAttribute()
	{
		$check = Payouts::where('user_id', $this->attributes['user_id'])->where('reservation_id', $this->attributes['id'])->get();

		if ($check->count()) {
			return $check[0]->amount;
		}
		return 0;
	}

	// Get Guest Payout Amount
	public function getGuestPayoutAttribute()
	{
		$check = Payouts::where('user_id', $this->attributes['user_id'])->where('reservation_id', $this->attributes['id'])->get();

		if ($check->count()) {
			return $check[0]->amount;
		}
		return $this->total;
	}

	public function getCheckinAttribute()
	{
		$booking_times = $this->reservation_times;
		return optional($booking_times)->start_date;
	}

	public function getCheckoutAttribute()
	{
		$booking_times = $this->reservation_times;
		return optional($booking_times)->end_date;
	}

	// Get Date for Email Subject
	public function getDatesSubjectAttribute()
	{
		$booking_times = $this->reservation_times;
		if(is_null($booking_times)) {
			return '';
		}
		if($this->booking_period == 'Multiple') {
			$dates_subject = $booking_times->checkin_formatted .' '.$booking_times->start_time_formatted.' - '. $booking_times->checkout_formatted .' '.$booking_times->end_time_formatted;
		}
		else {
			$dates_subject = $booking_times->checkin_formatted .' ( '.$booking_times->times_formatted.' )';
		}

		return $dates_subject;
	}

	public function getCreatedAtDateAttribute()
	{
		return $this->getDateInFormat('created_at');
	}
	
	// Get Checkin Arrive Date in md format
	public function getCheckinArriveAttribute()
	{
		return $this->getDateInFormat('checkin', 'D, d F, Y');
	}

	public function getDurationAttribute()
	{
		return $this->attributes['hours'];
	}

	public function getDurationTextAttribute()
	{
		$duration = $this->duration;
		return $duration . ' ' . trans_choice('messages.booking.hour', $duration);
	}

	// Get Created At Timer for Expired
	public function getCreatedAtTimerAttribute()
	{
		$expired_at = getDateObject($this->attributes['created_at'])->addDay();
		return $expired_at->format('Y/m/d H:i:s');
	}

	// Get value of Checkin crossed days
	public function getCheckinCrossAttribute()
	{
		$date1 = date_create($this->checkin);
		$date2 = date_create(date('Y-m-d'));
		$diff = date_diff($date1, $date2);
		if ($date2 < $date1) {
			return 1;
		}
		return 0;
	}

	// Get value of Checkout crossed days
	public function getCheckoutCrossAttribute()
	{
		$date1 = date_create($this->checkout);
		$date2 = date_create(date('Y-m-d'));

		if ($date2 > $date1) {
			return 1;
		}
		return 0;
	}

	public function getOriginalCurrencyCodeAttribute()
	{
		return $this->attributes['currency_code'];
	}

	// Set Reservation Status Color
	public function getStatusColorAttribute()
	{
		if (@$this->attributes['type'] == 'contact') {
			return 'inquiry';
		}
		else if ($this->attributes['status'] == 'Accepted') {
			return 'success';
		}
		else if ($this->attributes['status'] == 'Expired') {
			return 'info';
		}
		else if ($this->attributes['status'] == 'Pending') {
			return 'warning';
		}
		else if ($this->attributes['status'] == 'Declined') {
			return 'info';
		}
		else if ($this->attributes['status'] == 'Cancelled') {
			return 'info';
		}
		else {
			return '';
		}
	}

	// Get Reservation Status
	public function getStatusAttribute()
	{
        $date = date('Y-m-d', time());
		if (@$this->attributes['status'] == null) {
           if (isset($this->checkin) && strtotime($this->checkin) < strtotime($date)) {
				$this->status = 'Expired';
				$this->save();
				return 'Expired';
	     	}
			return 'Inquiry';
		}
		else if($this->attributes['status'] == 'Pre-Accepted' || $this->attributes['status'] == 'Pending' || $this->attributes['status'] == 'Pre-Approved') {
			if (isset($this->checkin) && strtotime($this->checkin) < strtotime($date)) {
				$this->status = 'Expired';
				$this->save();
				return 'Expired';
			}
		}
		return $this->attributes['status'];
	}

	// Get This reservation date is avaablie
	public function getAvablityAttribute()
	{
		if (isset($this->attributes['date_check'])) {
			if($this->attributes['date_check'] == 'No') {
				return 1;
			}
			$calendar_not_available = 0;
			if ($calendar_not_available > 0) {
				$this->attributes['date_check'] = 'No';
				$this->save();
				return 1;
			}
			return 0;
		}
	}

	public function getBookedReservationAttribute()
	{
		$booked_room = Reservation::where('id', $this->attributes['id'])->where('status', 'Accepted')->count();
		if ($booked_room) {
			return false;
		}
		return true;
	}

	public function getCheckinFormattedAttribute()
	{
		return $this->getDateInFormat('checkin');
	}

	public function getCheckoutFormattedAttribute()
	{
		return $this->getDateInFormat('checkout');
	}

	public function getCreatedAtAttribute()
	{
		return $this->getDateInFormat('created_at', PHP_DATE_FORMAT . ' H:i:s');
	}

	public function getCancelledAtAttribute()
	{
		return $this->getDateInFormat('cancelled_at', PHP_DATE_FORMAT . ' H:i:s');
	}

	public function getUpdatedAtAttribute()
	{
		return $this->getDateInFormat('updated_at', PHP_DATE_FORMAT . ' H:i:s');
	}

	public function getOriginalCreatedAtAttribute()
	{
		return $this->attributes['created_at'];
	}

	// status_language
	public function getStatusLanguageAttribute()
	{
		if (@$this->attributes['status'] == null) {
			return trans('messages.dashboard.Inquiry');
		}
		return trans('messages.dashboard.'.$this->attributes['status']);
	}

	public function getReviewLinkAttribute()
	{
		return siteUrl() . '/reviews/edit/' . $this->id;
	}

	/*
	* Join Diputes Table
	*/
	public function dispute()
	{
		return $this->belongsTo('App\Models\Disputes', 'id', 'reservation_id');
	}

	/*
	* To get the Current User relation to this reservation
	*/
	public function getHostOrGuestAttribute()
	{
		$host_or_guest = 'Host';
		$current_user_id = @auth()->id();
		if ($this->attributes['user_id'] == $current_user_id) {
			$host_or_guest = 'Guest';
		} elseif ($this->attributes['host_id'] == $current_user_id) {
			$host_or_guest = 'Host';
		}

		if (request()->segment(1) == ADMIN_URL) {
			$host_or_guest = '';
		}
		return $host_or_guest;
	}

	/*
	* To get the Maximum amount host can apply dispute
	*/
	public function getMaximumHostDisputeAmountAttribute()
	{
		return $this->security;
	}

	/*
	* To get the Maximum amount guest can apply dispute
	*/
	public function getMaximumGuestDisputeAmountAttribute()
	{
		$guest_payout = Payouts::where('user_id', $this->attributes['user_id'])->where('reservation_id', $this->attributes['id'])->first();

		$guest_payout_amount = $this->total - $this->service;
		if ($guest_payout) {
			$original_guest_payout_amount = $guest_payout->amount;
			$guest_payout_amount -= $guest_payout->currency_convert($guest_payout->currency_code, $this->currency_code, $original_guest_payout_amount);
		}

		return $guest_payout_amount;
	}

	/*
	* To get the Maximum amount can current user can apply dispute
	*/
	public function getMaximumDisputeAmountAttribute()
	{
		$host_or_guest = $this->getHostOrGuestAttribute();
		if ($host_or_guest == 'Guest') {
			return $this->getMaximumGuestDisputeAmountAttribute();
		}
		if ($host_or_guest == 'Host') {
			return $this->getMaximumHostDisputeAmountAttribute();
		}
		return $this->getMaximumGuestDisputeAmountAttribute() ? $this->getMaximumGuestDisputeAmountAttribute() : $this->getMaximumHostDisputeAmountAttribute();
	}

	/*
	* To get the last date to apply for the dispute
	*/
	public function getLastDateForDisputeAttribute()
	{
		$start_date = date('Y-m-d', strtotime(' -5 days'));

		if ($this->attributes['status'] == 'Cancelled' && ($this->checkin <= $this->attributes['cancelled_at'])) {
			$start_date = date('Y-m-d', strtotime($this->attributes['cancelled_at']));
		}
		else if ($this->attributes['status'] == 'Accepted') {
			if (date('Y-m-d') > $this->checkout) {
				$start_date = $this->checkout;
			}
		}

		$end_date = date('Y-m-d', strtotime($start_date . ' +4 days'));
		return $end_date;
	}

	/*
	* To get the remaining days to apply for dispute
	*/
	public function getRemainingDaysForDisputeAttribute()
	{
		$remaining_days_for_dispute = 0;
		$last_date_for_dispute = $this->getLastDateForDisputeAttribute();

		$today_date = new DateTime(date('Y-m-d'));
		$last_date = new DateTime($last_date_for_dispute);
		$interval = $today_date->diff($last_date);

		$remaining_days_for_dispute = $interval->format('%R%a');

		return $remaining_days_for_dispute;
	}

	/*
	* To check if the current user can apply for the dispute
	*/
	public function getCanApplyForDisputeAttribute()
	{
		$remaining_days_for_dispute = $this->getRemainingDaysForDisputeAttribute();
		$maximuim_dispute_amount = $this->getMaximumDisputeAmountAttribute();
		$already_applied_dispute = $this->dispute()->count();

		$can_apply_dipute = true;
		if (($remaining_days_for_dispute <= 0) || ($maximuim_dispute_amount <= 0) || ($already_applied_dispute > 0)) {
			$can_apply_dipute = false;
		}

		return $can_apply_dipute;
	}

	/**
	 * To check if the payout button can show
	 *
	 * @return bool $can_payout_button_show
	 **/
	function can_payout_button_show()
	{
		$date1 = date_create($this->checkout);
		$date2 = date_create(date('Y-m-d'));

		$checkout_cross = 0;

		if ($date2 > $date1) {
			$checkout_cross = 1;
		}
		$open_dipsutes = Disputes::reservationBased($this->attributes['id'])
			->where(function ($query) {
				$query->where('status', 'Open')->orwhere('admin_status', 'Open');
			})->count();
		return (!$this->getCanApplyForDisputeAttribute() && !$open_dipsutes && ($checkout_cross || $this->attributes['status'] == 'Cancelled'));
	}

	// Get Formatted Payment Type Attribute
	public function getFormattedPaymodeAttribute()
	{
		if($this->attributes['total'] > 0) {
			$paymode = $this->attributes['paymode'];
		}
		else {
			$paymode = ($this->attributes['coupon_code'] == 'Travel_Credit') ? trans('messages.referrals.travel_credit'):trans('messages.payments.coupon_code');
		}
		return $paymode;
	}

	public function getBookingPeriodAttribute()
	{
		$booking_period = ($this->checkin == $this->checkout) ? 'Single' : 'Multiple';

		return $booking_period;
	}

	// Get Date for Email Subject
	public function getCheckinWithTimeAttribute()
	{
		$booking_times = $this->reservation_times;
		if(is_null($booking_times)) {
			return '';
		}

		return $booking_times->checkin_formatted .' '.$booking_times->start_time_formatted;
	}

	// Get Date for Email Subject
	public function getCheckoutWithTimeAttribute()
	{
		$booking_times = $this->reservation_times;
		if(is_null($booking_times)) {
			return '';
		}
		
		return $booking_times->checkout_formatted .' '.$booking_times->end_time_formatted;
	}

	public function getGuestsTextAttribute()
	{
		$guests = $this->attributes['number_of_guests'];
		$guests_text = $guests . ' ' . trans_choice('messages.home.guest', $guests);
		return $guests_text;
	}

	public function getEventTypeNameAttribute()
	{
		$activity_name = optional($this->activities)->name;
		$sub_activity_name = optional($this->subactivities)->name;

		$display_name = '';
		if(isset($activity_name)) {
			$display_name .= $activity_name;
		}
		if(isset($sub_activity_name)) {
			if(isset($activity_name)) {
				$display_name .= ', ';
			}
			$display_name .= $sub_activity_name;
		}
		return $display_name;
	}
}
