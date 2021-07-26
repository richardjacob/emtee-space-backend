<?php

/**
 * Space Price Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Space Price
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use App\Repositories\CurrencyConversion;

class SpacePrice extends Model
{
	use CurrencyConversion;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'space_price';

	protected $primaryKey = 'space_id';

	public $timestamps = false;

	protected $appends = [];

	protected $guarded = [];

	protected $convert_fields = ['security'];

	// Join with currency table
	public function currency()
	{
		return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
	}
}