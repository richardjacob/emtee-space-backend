<?php

/**
 * kind Of Space Model
 *
 * @package     Makent
 * @subpackage  Model
 * @category    Space Type
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Request;

class KindOfSpace extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'kind_of_space';

    public $timestamps = false;

    protected $appends = ['search_url'];

    // Get all Active status records
    public static function ScopeActive($query)
    {
    	return $query->whereStatus('Active');
    }

    // Get Popular records Only
    public function scopePopularOnly($query)
    {
        return $query->wherePopular('Yes');
    }


    // Get all Active status records in lists type
    public static function dropdown()
    {
        $data=KindOfSpace::whereStatus('Active')->get();
        return $data->pluck('name','id');
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'];
        if(Request::segment(1)==ADMIN_URL) {
            return $name;
        }

        $lang = getLangCode();

        if($lang != 'en') {
            $lang_name = @KindOfSpaceLang::where('kind_of_space_id', $this->attributes['id'])->where('lang_code', $lang)->first()->name;
            $name = ($lang_name != '') ? $lang_name : $name;
        }

        return $name;
    }
    // Get Image  Attribute
    public function getImageAttribute()
    {
        return getImage($this->attributes['image'],'/images/space_type/');
    }
     public function getSearchUrlAttribute()
    {
        return route('search_page',['space_type' => $this->attributes['id']]);
    }

}