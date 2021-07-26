<?php

/**
 * Space Activities Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Activities
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class SpaceActivities extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_activities';

    protected $appends = [];

    protected $guarded = [];

    public $timestamps = false;

    public function scopeType($query, $type)
    {
      $query = $query->where('type', $type);
      return $query;
    }

    // Join with activity_price table
    public function activity_price()
    {
        return $this->hasOne('App\Models\ActivityPrice','activity_id','id');
    }

    // Join with activity_price table
    public function activity_type()
    {
        return $this->belongsTo('App\Models\ActivityType','activity_type_id','id');
    }

    // Join with activity_price table
    public function getActivityTypeNameAttribute()
    {
        return $this->activity_type->name;;
    }
}