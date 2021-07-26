<?php

/**
 * SpaceRule Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    SpaceRule
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceRule extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_rules';

    public $timestamps = false;

    protected $appends = [];

    // Get all Active status records
    public function ScopeActive($query)
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
            $lang_name = @SpaceRuleLang::where('space_rule_id', $this->attributes['id'])->where('lang_code', $lang)->first()->name;
            $name = ($lang_name != '') ? $lang_name : $name;
        }

        return $name;
    }
}