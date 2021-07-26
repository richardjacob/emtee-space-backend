<?php

/**
 * Space Steps Status Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Steps Status
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceStepsStatus extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'space_steps_status';

    public $timestamps = false;

    protected $primaryKey = 'space_id';

    protected $appends = ['total_steps'];

    public function setAttribute($attribute, $value)
    {
        if($attribute != 'id') {
            $this->attributes[$attribute] = $value.'';
        }
    }

    public function getTotalStepsAttribute()
    {
        $total_steps = \Schema::getColumnListing('space_steps_status');
        $total_steps = count($total_steps) - 1; // Decrease space_id Column
        return $total_steps;
    }

    public function getAllStatusAttribute()
    {
        $data['basics'] = false;
        $data['setup'] = false;
        $data['ready_to_host'] = false;

        if($this->attributes['basics'] == 1 && $this->attributes['location'] == 1) {
            $data['basics'] = true;
        }
        if($this->attributes['photos'] == 1 && $this->attributes['description'] == 1 ) {
            $data['setup'] = true;
        }
        if($this->attributes['pricing'] == 1) {
            $data['ready_to_host'] = true;
        }
        return $data;
    }
}