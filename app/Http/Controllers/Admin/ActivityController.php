<?php

/**
 * Activity Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Activity
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ActivityDataTable;
use App\Models\Activity;
use App\Models\ActivityLang;
use App\Models\ActivityType;
use App\Models\language;
use App\Models\SubActivity;
use App\Models\SpaceActivities;
use Validator;
use Illuminate\Support\Arr;

class ActivityController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = $this->main_title = 'Activity';
        $this->view_data['base_url'] = $this->base_url = route('activities');
        $this->view_data['add_url']  = route('create_activity');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.activities.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of ActivityDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ActivityDataTable $dataTable)
    {
        return $dataTable->render('admin.common.view',$this->view_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->view_data['language'] = Language::translatable()->get();
        $this->view_data['activity_types'] = ActivityType::activeOnly()->get()->pluck('name','id');
        return view($this->base_view_path.'add', $this->view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate Data
        $validate_return = $this->validate_request_data($request->all());
        
        if($validate_return) {
            return $validate_return;
        }

        // $target_dir = '/images/activities';
        // $upload_result = uploadImage($request->file('image'),$target_dir,'activity_');
        // if($upload_result['status'] != 'Success') {
        //     flash_message('danger',$upload_result['status_message']);
        //     return back();
        // }

        // $file_name = $upload_result['file_name'];

        $activity = new Activity;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $activity->name      = $request->name[$i];
                $activity->status      = $request->status;
                $activity->activity_type_id      = $request->activity_type_id;
                // $activity->image     = $file_name;
                $activity->save();
                $lastInsertedId = $activity->id;
            }
            else {
                $activity_lang = new ActivityLang;
                $activity_lang->activity_id = $lastInsertedId;
                $activity_lang->lang_code   = $request->lang_code[$i];
                $activity_lang->name        = $request->name[$i];
                $activity_lang->save();
            }
        }

        flash_message('success', 'New '.$this->main_title.' Added Successfully');
        return redirect($this->base_url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->view_data['language']    = Language::get();  
        $this->view_data['result']      = Activity::find($id);
        $this->view_data['langresult']  = ActivityLang::where('activity_id',$id)->get();
        $this->view_data['activity_types'] = ActivityType::get()->pluck('name','id');

        return view($this->base_view_path.'edit', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Delete Activity Lang
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);  
        if(empty($lang_id_arr)) {
            $activity_lang = ActivityLang::where('activity_id',$request->id); 
            $activity_lang->delete();
        }

        $activity_del = ActivityLang::select('id')->where('activity_id',$request->id)->get();
        foreach($activity_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $activity_lang = ActivityLang::find($values->id); 
                $activity_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        // if($request->file('images')) {
        //     $target_dir = '/images/activities';
        //     $upload_result = uploadImage($request->file('images'),$target_dir,'activity_');
        //     if($upload_result['status'] != 'Success') {
        //         flash_message('danger',$upload_result['status_message']);
        //         return back();
        //     }

        //     $file_name = $upload_result['file_name'];
        // }

        //Update Activity
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $activity = Activity::find($request->id);
                $activity->name        = $request->name[$i];
                $activity->status      = $request->status;
                $activity->activity_type_id      = $request->activity_type_id;
                // if(isset($file_name)) {
                //     $activity->image      = $file_name;
                // }
                $activity->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $activity_lang = ActivityLang::find($request->lang_id[$i]);
                    $activity_lang->lang_code   = $request->lang_code[$i];
                    $activity_lang->name        = $request->name[$i];
                    $activity_lang->save();            
                } 
                else{
                    $activity_lang =  new ActivityLang; 
                    $activity_lang->activity_id   = $request->id;    
                    $activity_lang->lang_code   = $request->lang_code[$i];
                    $activity_lang->name        = $request->name[$i];
                    $activity_lang->save();
                }
            }
        }

        flash_message('success', $this->main_title.' Updated Successfully');
        return redirect($this->base_url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $can_delete = $this->canDestroy($id);
        
        if($can_delete['status'] == 0) {
            flash_message('error',$can_delete['message']);
            return redirect($this->base_url);
        }

        try {
            ActivityLang::where('activity_id',$id)->delete();
            Activity::find($id)->delete();
            flash_message('success', $this->main_title.' Deleted Successfully');
        }
        catch(\Exception $e) {
            flash_message('danger', $this->main_title.' Already Deleted');
        }

        return redirect($this->base_url);
    }

    /**
     * Validate Given Request Data.
     *
     * @param  Array  $request_data
     * @param  int  $id
     * @return \Illuminate\Http\Response | void
     */
    protected function validate_request_data($request_data, int $id = 0)
    {
        $rules  = array(
            'status' => 'required',
        );

        $messages = array(

        );

        $attributes = array(
            'status' => 'Status',
        );

        $validator = Validator::make($request_data, $rules, $messages, $attributes);

        $activity_name_count = Activity::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($activity_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect()->route('activities');
        }

        $activity_count = SpaceActivities::whereRaw('find_in_set('.$id.', activities)')->count();
        if($activity_count > 0 && $request_data['status'] == 'Inactive') {
            $validator->after(function($validator) {
                $validator->errors()->add('status', 'Cannot Inactive already used activity');
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Activity can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = Activity::whereNotIn('id',Arr::wrap($id))->where('status','Active')->count();
        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $sub_activity_count = SubActivity::whereIn('activity_id',Arr::wrap($id))->count();
        if($sub_activity_count > 0) {
            return ['status' => 0, 'message' => 'This '.$this->main_title.' already Used in Sub Activity. So can\'t delete this'];
        }

        $activity_count = SpaceActivities::whereRaw('find_in_set('.$id.', activities)')->count();
        if($activity_count > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return ['status' => 1, 'message' => ''];
    }

    public function popular(Request $request)
    {
        $activity = Activity::findOrFail($request->id);

        if($activity->popular == 'No') {
            if($activity->status != 'Active') {
                flash_message('error', 'Not able to popular for Inactive Activity');
                return back();
            }        }

        $activity->popular = ($activity->popular == 'Yes') ? 'No' : 'Yes';
        $activity->save();

        flash_message('success', 'Updated Successfully');
        return redirect()->route('activities');
    }
}