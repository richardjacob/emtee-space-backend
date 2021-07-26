<?php

/**
 * Services & Extra Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Services & Extra
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services';
    public $appends = [];

    public $timestamps = false;

    // Get all Active status records
    public static function scopeActive($query)
    {
    	return $query->whereStatus('Active');
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'];
        if(request()->segment(1)==ADMIN_URL) {
            return $name;
        }
        
        $lang = getLangCode();

        if($lang != 'en') {
            $lang_name = @ServicesLang::where('services_id', $this->attributes['id'])->where('lang_code', $lang)->first()->name;
            $name = ($lang_name != '') ? $lang_name : $name;
        }

        return $name;
    }
}