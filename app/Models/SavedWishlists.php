<?php

/**
 * SavedWishlists Model
 *
 * @package     Makent Space
 * @subpackage  Model
 * @category    Saved Wishlists
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedWishlists extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'saved_wishlists';

    public $timestamps = false;

    protected $appends = ['photo_name','space_count'];
    
    // Join with wishlists table
    public function wishlists()
    {
        return $this->belongsTo('App\Models\Wishlists','wishlist_id','id');
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

     // Join with Space table
    public function space()
    {
        return $this->belongsTo('App\Models\Space','space_id','id')->where('status','Listed');
    }

    public function getSpaceCountAttribute()
    {
        return \DB::table('saved_wishlists')->where('wishlist_id', $this->attributes['wishlist_id'])->count();
    }

    // Get Space First Image URL
    public function getPhotoNameAttribute()
    {
        $result = SpacePhotos::where('space_id', @$this->attributes['space_id'])->ordered();
        if($result->count() > 0) {
            return $result->first()->name;
        }
        return asset('images/default_image.png');
    }
}