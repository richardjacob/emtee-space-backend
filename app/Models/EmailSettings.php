<?php

/**
 * Email Settings Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Email Settings
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSettings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_settings';

    public $timestamps = false;
}
