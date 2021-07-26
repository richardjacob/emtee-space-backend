<?php

/**
 * Guest Access Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Guest Access
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Request;

class GuestAccess extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'guest_access';

    public $timestamps = false;

    protected $appends = [];

    // Get all Active status records
    public function ScopeActive($query)
    {
    	return $query->whereStatus('Active');
    }

    // Get all Active status records in lists type
    public static function dropdown()
    {
        $data = GuestAccess::whereStatus('Active')->get();
        return $data->pluck('name','id');
    }

    // Get single field data by using id and field name
    public static function single_field($id, $field)
    {
        return GuestAccess::whereId($id)->first()->$field;
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'];
        if(Request::segment(1)==ADMIN_URL) {
            return $name;
        }
        
        $lang = getLangCode();

        if($lang != 'en') {
            $lang_name = @GuestAccessLang::where('guest_access_id', $this->attributes['id'])->where('lang_code', $lang)->first()->name;
            $name = ($lang_name != '') ? $lang_name : $name;
        }

        return $name;
    }
}