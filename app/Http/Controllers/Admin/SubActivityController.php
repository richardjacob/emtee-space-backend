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
use App\DataTables\SubActivityDataTable;
use App\Models\SubActivity;
use App\Models\SubActivityLang;
use App\Models\language;
use App\Models\Activity;
use App\Models\SpaceActivities;
use Validator;
use Illuminate\Support\Arr;

class SubActivityController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = $this->main_title = 'Sub activity';
        $this->view_data['base_url'] = $this->base_url = route('sub_activities');
        $this->view_data['add_url']  = route('create_sub_activity');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.sub_activities.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of SubActivityDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SubActivityDataTable $dataTable)
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
        $this->view_data['activities'] = Activity::activeOnly()->get()->pluck('name','id');
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

        $sub_activity = new SubActivity;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $sub_activity->name  = $request->name[$i];
                $sub_activity->activity_id = $request->activity_id;
                $sub_activity->status = $request->status;
                $sub_activity->save();
                $lastInsertedId = $sub_activity->id;
            }
            else {
                $sub_activity_lang = new SubActivityLang;
                $sub_activity_lang->activity_id = $lastInsertedId;
                $sub_activity_lang->lang_code   = $request->lang_code[$i];
                $sub_activity_lang->name        = $request->name[$i];
                $sub_activity_lang->save();
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
        $this->view_data['result']      = SubActivity::find($id);
        $this->view_data['langresult']  = SubActivityLang::where('activity_id',$id)->get();
        $this->view_data['activities'] = Activity::get()->pluck('name','id');

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
            $activity_lang = SubActivityLang::where('activity_id',$request->id); 
            $activity_lang->delete();
        }

        $activity_del = SubActivityLang::select('id')->where('activity_id',$request->id)->get();
        foreach($activity_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $activity_lang = SubActivityLang::find($values->id); 
                $activity_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        //Update Activity
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $sub_activity = SubActivity::find($request->id);
                $sub_activity->name        = $request->name[$i];
                $sub_activity->status      = $request->status;
                $sub_activity->activity_id = $request->activity_id;
                $sub_activity->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $sub_activity_lang = SubActivityLang::find($request->lang_id[$i]);
                    $sub_activity_lang->lang_code   = $request->lang_code[$i];
                    $sub_activity_lang->name        = $request->name[$i];
                    $sub_activity_lang->save();            
                } 
                else{
                    $sub_activity_lang =  new SubActivityLang; 
                    $sub_activity_lang->activity_id   = $request->id;    
                    $sub_activity_lang->lang_code   = $request->lang_code[$i];
                    $sub_activity_lang->name        = $request->name[$i];
                    $sub_activity_lang->save();
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
            SubActivityLang::where('activity_id',$id)->delete();
            SubActivity::find($id)->delete();
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

        $activity_name_count = SubActivity::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($activity_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect()->route('sub_activities');
        }

        $sub_activity_count = SpaceActivities::whereRaw('find_in_set('.$id.', sub_activities)')->count();
        if($sub_activity_count > 0 && $request_data['status'] == 'Inactive') {
            $validator->after(function($validator) {
                $validator->errors()->add('status', 'Cannot Inactive already used activity');
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate SubActivity can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $return  = ['status' => 1, 'message' => ''];
        $active_count   = SubActivity::whereNotIn('id',Arr::wrap($id))->where('status','Active')->count();

        if($active_count <= 0) {
            $return = ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $sub_activity_count = SpaceActivities::whereRaw('find_in_set('.$id.', sub_activities)')->count();
        if($sub_activity_count > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return $return;
    }
}