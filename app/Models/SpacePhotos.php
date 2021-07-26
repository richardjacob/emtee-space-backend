<?php

/**
 * Space Photos Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Photos
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpacePhotos extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_photos';

    public $timestamps = false;

    protected $appends = [];

    // Get Ordered Photos List
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_id','asc');
    }

    public function getOriginalNameAttribute(){
        return @$this->attributes['name'];
    }

    protected function getImageName($compress_size)
    {
        $site_settings_url = @SiteSettings::where('name' , 'site_url')->first()->value;
        $url = \App::runningInConsole() ? $site_settings_url : url('/');
        $photo_details = pathinfo($this->attributes['name']); 
        if(@$photo_details['extension']=='gif' || @$photo_details['extension'] == 'webp') {
            $name = @$photo_details['filename'].'.'.@$photo_details['extension'];
        }
        else {
            $name = @$photo_details['filename'].$compress_size.'.'.@$photo_details['extension'];
        }
        return $url.'/images/space/'.$this->attributes['space_id'].'/'.$name;
    }

    // Get Name Attribute
    public function getNameAttribute()
    {
        $photo_src=explode('.',$this->attributes['name']);
        if(count($photo_src)>1) {
            return $this->getImageName('_450x250');
        }
        $options['secure']=TRUE;
        $options['width']=450;
        $options['height']=250;
        $options['crop']='fill';
        return $src=\Cloudder::show($this->attributes['name'],$options);
    }

    // Get Slider Image Name Attribute
    public function getSliderImageNameAttribute()
    {
        $photo_src=explode('.',$this->attributes['name']);
        if(count($photo_src)>1) {
            return $this->getImageName('_1440x960');
        }

        $options['secure']=TRUE;
        $options['width']=1440;
        $options['height']=960;
        $options['crop']='fill';
        return $src=\Cloudder::show($this->attributes['name'],$options);
    }

    // Get Banner Image Name Attribute
    public function getBannerImageNameAttribute()
    {
        $photo_src=explode('.',$this->attributes['name']);
        if(count($photo_src)>1) {
            return $this->getImageName('_1349x402');
        }
        $options['secure']=TRUE;
        $options['width']=1349;
        $options['height']=402;
        $options['crop']='fill';
        return $src=\Cloudder::show($this->attributes['name'],$options);
    }
}