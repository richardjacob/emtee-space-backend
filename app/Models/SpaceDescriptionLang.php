<?php

/**
 * Space Description Lang Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Description Lang
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceDescriptionLang extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_description_lang';

    public $timestamps = false;

    protected $appends = ['language_name'];

    protected $guarded = [];

    // Join with language table
    public function language()
    {
        return $this->belongsTo('App\Models\Language','lang_code','value');
    }
    
    public function getLanguageNameAttribute()
    {
        return $this->language->name;
    }
}