<?php

/**
 * Amenities Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Amenities
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use session;

class Amenities extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'amenities';

    public $timestamps = false;
    protected $appends = ['image_name'];

    // Get all Active status records
    public static function ScopeActive($query)
    {
        return $query->whereStatus('Active');
    }

    public function getNameAttribute()
    {
        return $this->getTranslatedValue('name');
    }
   
    public function getDescriptionAttribute()
    {
        return $this->getTranslatedValue('description');
    }

    protected function getTranslatedValue($field)
    {
        $trans_value = $this->attributes[$field];
        if(request()->segment(1)==ADMIN_URL){ 
            return $trans_value;
        }
        
        $language = getLangCode();

        if($language != 'en') {
            $lang_name = @AmenitiesLang::where('amenities_id', $this->attributes['id'])->where('lang_code', $language)->first()->$field;
            $trans_value = ($lang_name != '') ? $lang_name : $trans_value;
        }
        
        return $trans_value;
    }

    // Get Image Name Attribute
    public function getImageNameAttribute()
    {
        return getImage($this->attributes['icon'],'/images/amenities/');
    }
}