<?php

/**
 * Reviews Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Reviews
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ReviewsDataTable;
use App\Models\Reviews;
use App\Models\ProfilePicture;
use App\Models\ReviewsVerification;
use App\Http\Start\Helpers;
use Validator;

class ReviewsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Reviews
     *
     * @param array $dataTable  Instance of ReviewsDataTable
     * @return datatable
     */
    public function index(ReviewsDataTable $dataTable)
    {
        return $dataTable->render('admin.reviews.view');
    }

    /**
     * Update Reviews Details
     *
     * @param array $request    Input values
     * @return redirect     to Reviews View
     */
    public function update(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['result'] = Reviews::join('space', function($join) {
                $join->on('space.id', '=', 'reviews.space_id');
            })
            ->join('users', function($join) {
                $join->on('users.id', '=', 'reviews.user_from');
            })
            ->join('users as users_to', function($join) {
                $join->on('users_to.id', '=', 'reviews.user_to');
            })
            ->where('reviews.id',$request->id)
            ->select(['reviews.id as id', 'reservation_id', 'space.name as space_name', 'users.first_name as user_from', 'users_to.first_name as user_to', 'review_by', 'comments','comments2','comments3', 'private_feedback','cleanliness','accuracy_comments','cleanliness_comments','checkin_comments','communication_comments','value_comments','amenities_comments','love_comments','improve_comments','communication','respect_house_rules','checkin','reviews.amenities as amenities','accuracy','location','value','rating','location_comments'])
            ->get();

            if($data['result']->count() == 0) {
                abort(404);
            }
            return view('admin.reviews.edit', $data);
        }
        else if($request->submit) {
            // Edit Reviews Validation Rules
            $rules = array(
                'comments' => 'required',
                'comments2' => 'required',
                'comments3' => 'required',
            );
            $messages = array(
                'comments.required' => 'This field is required',
                'comments2.required' => 'This field is required',
                'comments3.required' => 'This field is required',
            );

            $validator = Validator::make($request->all(),$rules,$messages);

            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = Reviews::find($request->id);
            $user->comments     = $request->comments;
            $user->comments2     = $request->comments2;
            $user->comments3    = $request->comments3;
            $user->save();

            flash_message('success', 'Updated Successfully');
            return redirect(ADMIN_URL.'/reviews');
        }
        
        return redirect(ADMIN_URL.'/reviews');
    }
}