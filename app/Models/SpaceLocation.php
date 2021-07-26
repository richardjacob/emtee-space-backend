<?php

/**
 * Space Location Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Location
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceLocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_location';

    public $timestamps = false;

    protected $appends = ['country_name', 'address_line'];
    
    // Get country_name by using country code in Country table
    public function getCountryNameAttribute()
    {
        $country = Country::where('short_name',@$this->attributes['country'])->first();
        return optional($country)->long_name;
    }

    public function getAddressLineAttribute()
    {
        if(@$this->attributes['latitude'] == '' || @$this->attributes['longitude'] == '') {
            return '';
        }

        $formatted_address = $this->attributes['address_line_1'];
        $formatted_address .= ($formatted_address != '') ? ', ':'';
        $formatted_address .= $this->attributes['city'].', '.$this->attributes['state'].', '.$this->country_name;
        
        return $formatted_address;
    }

    public function getFormattedAddressAttribute()
    {
        if(@$this->attributes['latitude'] == '' || @$this->attributes['longitude'] == '') {
            return '';
        }

        $formatted_address = $this->attributes['city'].', '.$this->attributes['state'].', '.$this->country_name;
        
        return $formatted_address;
    }

    public function getCompleteAddressAttribute()
    {
        if(@$this->attributes['latitude'] == '' || @$this->attributes['longitude'] == '') {
            return '';
        }
        $formatted_address = '';
        if($this->attributes['address_line_1'] != '') {
            $formatted_address .= $this->attributes['address_line_1'].'<br>';
        }
        if($this->attributes['city'] != '') {
            $formatted_address .= $this->attributes['city'].', ';
        }
        if($this->attributes['state'] != '') {
            $formatted_address .= $this->attributes['state'].', ';
        }
        if($this->attributes['postal_code'] != '') {
            $formatted_address .= $this->attributes['postal_code'].'<br>';
        }
        if($this->country_name != '') {
            $formatted_address .= $this->country_name;
        }
        
        return $formatted_address;
    }
}