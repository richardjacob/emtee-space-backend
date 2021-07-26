<?php

/**
 * Style Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Style
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'styles';

    public $timestamps = false;

    protected $appends = [];

    // Get all Active status records
    public function ScopeActive($query)
    {
        return $query->whereStatus('Active');
    }

    // Get Translated value of given column
    protected function getTranslatedValue($field)
    {
        if(!isset($this->attributes[$field])) {
            return '';
        }
        $value = $this->attributes[$field];

        if(request()->segment(1) == ADMIN_URL) {
            return $value;
        }

        $lang_code = getLangCode();
        if($lang_code == 'en') {
            return $value;
        }

        $trans_value = @StyleLang::where('style_id', $this->attributes['id'])->where('lang_code', $lang_code)->first()->$field;
        if ($trans_value) {
            return $trans_value;
        }
        return $value;
    }

    public function getNameAttribute()
    {
        return $this->getTranslatedValue('name');
    }
}