<?php

/**
 * Space Description Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Description
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceDescription extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_description';

    public $timestamps = false;

    protected $primaryKey = 'space_id';

    protected $guarded = [];

    protected $appends = ['space_rules'];

    public function getSpaceAttribute()
    {
        return $this->getTranslatedValue('space');
    }

    public function getHouseRulesAttribute()
    {
      return $this->getTranslatedValue('house_rules');
    }
    public function getSpaceRulesAttribute()
    {
      return $this->getTranslatedValue('house_rules');
    }

    public function getTransitAttribute()
    {
      return $this->getTranslatedValue('transit');
    }

    public function getAccessAttribute()
    {
      return $this->getTranslatedValue('access');
    }

    public function getInteractionAttribute()
    {
      return $this->getTranslatedValue('interaction');
    }

    public function getNotesAttribute()
    {
      return $this->getTranslatedValue('notes');
    }

    public function getNeighborhoodOverviewAttribute()
    {
      return $this->getTranslatedValue('neighborhood_overview');
    }

    // Function to Get The translated value of give field
    protected function getTranslatedValue($field)
    {
        if(!isset($this->attributes[$field])) {
            return '';
        }
        $value = $this->attributes[$field];

        $lang_code = getLangCode();
        if ($lang_code == 'en') {
            return $value;
        }

        $space_desc = SpaceDescriptionLang::where('space_id', $this->attributes['space_id'])->where('lang_code', $lang_code)->first();
        $trans_value = optional($space_desc)->$field;
        if ($trans_value) {
            return $trans_value;
        }
        return $value;
    }
}