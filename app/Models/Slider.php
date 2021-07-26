<?php

/**
 * Slider Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Slider
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'slider';

    public $appends = ['image_url'];

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
