<?php

/**
 * Wishlists Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Wishlists
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Wishlists extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wishlists';

    public $timestamps = false;

    protected $fillable = ['name'];

    public $appends = ['space_count', 'all_space_count'];

    public function setNameAttribute($input)
    {
        $this->attributes['name'] = strip_tags($input);
    }

    // Join with users table
    public function users()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    // Join with profile_picture table
    public function profile_picture()
    {
        return $this->belongsTo('App\Models\ProfilePicture','user_id','user_id');
    }

    // Join with saved_wishlists table
    public function saved_wishlists()
    {
        return $this->hasMany('App\Models\SavedWishlists','wishlist_id','id');
    }

    public function getSpaceCountAttribute()
    {
        return \DB::table('saved_wishlists')->where('saved_wishlists.wishlist_id', $this->attributes['id'])->join('space', 'space.id' ,'=', 'saved_wishlists.space_id')->where('space.status','Listed')->where('saved_wishlists.user_id', @$this->attributes['user_id'])->count();
    }

    public function getAllSpaceCountAttribute()
    {
        return \DB::table('saved_wishlists')->where('wishlist_id', $this->attributes['id'])->join('space', 'space.id' ,'=', 'saved_wishlists.space_id')->where('space.status','Listed')->count();
    }

    //all_space_image
    public function getAllSpaceImageAttribute()
    {
        $image =  $this->saved_wishlists()->with('space')->where('wishlist_id', $this->attributes['id'])->get();
        $all_image = $image->map(function ($item, $key) {
            return optional($item->space)->photo_name;
        });

        return $all_image->values()->toArray();
    }
}
