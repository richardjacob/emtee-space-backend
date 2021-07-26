<?php

/**
 * Home Page Sliders Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Home Page Slider
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSlider extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'home_page_sliders';

    public $timestamps = false;

    public $appends = ['image_url'];

    public function scopeActiveOnly($query)
    {
        return $query->whereStatus('Active');
    }

    // Get Ordered Photos List
    public function scopeOrdered($query)
    {
        return $query->orderBy('order','asc');
    }

    public function getImageUrlAttribute()
    {
        if($this->attributes['source'] == 'Local') {
            return asset('/images/slider/'.$this->attributes['image']);
        }
        $options['secure']  = TRUE;
        $options['crop']    = 'fill';
        return $src=\Cloudder::show($this->attributes['image'],$options);
    }
}
