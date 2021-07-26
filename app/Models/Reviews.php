<?php

/**
 * Reviews Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Reviews
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reviews';

    protected $appends =['date_fy'];

    protected function getDateInFormat($field, $format = PHP_DATE_FORMAT)
    {
        $date_str = (array_key_exists($field,$this->attributes)) ? $this->attributes[$field] : @$this->$field;
        
        return date($format, strtotime($date_str));
    }

    // Join with users table
    public function users()
    {
      return $this->belongsTo('App\Models\User','user_to','id');
    }

    // Join with users table
    public function users_from()
    {
      return $this->belongsTo('App\Models\User','user_from','id');
    }

    // Join with reservation table
    public function reservation()
    {
      return $this->belongsTo('App\Models\Reservation','reservation_id','id');
    }

    // Check give record is Hidden review or not
    public function getHiddenReviewAttribute()
    {
        $reservation_id = $this->attributes['reservation_id'];
        $user_from = $this->attributes['user_from'];
        $user_to = $this->attributes['user_to'];
        $check = Reviews::where(['user_from'=>$user_to, 'user_to'=>$user_from, 'reservation_id'=>$reservation_id])->get();
        if($check->count()) {
            return false;
        }
        return true;
    }
    
    // Get updated_at date in fy format
    public function getDateFyAttribute()
    {
        return date(PHP_DATE_FORMAT, strtotime($this->attributes['updated_at']));
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->getDateInFormat('created_at', PHP_DATE_FORMAT.' H:i:s');
    }

    public function getFormattedUpdatedAtAttribute()
    { 
        return $this->getDateInFormat('updated_at', PHP_DATE_FORMAT.' H:i:s');
    }
}
