<?php

/**
 * Special Features Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Special Features
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\SpecialFeatureDataTable;
use App\Models\SpecialFeature;
use App\Models\SpecialFeatureLang;
use App\Models\language;
use App\Http\Start\Helpers;
use Validator;

class SpecialFeatureController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Special Feature';
        $this->view_data['base_url'] = $this->base_url = route('special_features');
        $this->view_data['add_url']  = route('create_special_feature');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.special_feature.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of SpecialFeatureDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SpecialFeatureDataTable $dataTable)
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

        $special_feature = new SpecialFeature;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $special_feature->name      = $request->name[$i];
                $special_feature->status      = $request->status;
                $special_feature->save();
                $lastInsertedId = $special_feature->id;
            }
            else {
                $special_feature_lang = new SpecialFeatureLang;
                $special_feature_lang->special_feature_id = $lastInsertedId;
                $special_feature_lang->lang_code   = $request->lang_code[$i];
                $special_feature_lang->name        = $request->name[$i];
                $special_feature_lang->save();
            }
        }

        flash_message('success', 'New '.$this->main_title.' Added Successfully');
        return redirect($this->base_url);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        $this->view_data['result']      = SpecialFeature::find($id);
        $this->view_data['langresult']  = SpecialFeatureLang::where('special_feature_id',$id)->get();

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
        // Delete Special Feature
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);  
        if(empty($lang_id_arr)) {
            $special_feature_lang = SpecialFeatureLang::where('special_feature_id',$request->id); 
            $special_feature_lang->delete();
        }

        $special_feature_lang = SpecialFeatureLang::select('id')->where('special_feature_id',$request->id)->get();
        foreach($special_feature_lang as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $special_feature_lang = SpecialFeatureLang::find($values->id); 
                $special_feature_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        //Update Special Feature
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $special_feature = SpecialFeature::find($request->id);
                $special_feature->name        = $request->name[$i];
                $special_feature->status      = $request->status;
                $special_feature->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $special_feature_lang = SpecialFeatureLang::find($request->lang_id[$i]);
                    $special_feature_lang->lang_code   = $request->lang_code[$i];
                    $special_feature_lang->name        = $request->name[$i];
                    $special_feature_lang->save();            
                } 
                else{
                    $special_feature_lang =  new SpecialFeatureLang; 
                    $special_feature_lang->special_feature_id   = $request->id;    
                    $special_feature_lang->lang_code   = $request->lang_code[$i];
                    $special_feature_lang->name        = $request->name[$i];
                    $special_feature_lang->save();
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
        }
        else {
            SpecialFeatureLang::where('special_feature_id',$id)->delete();
            SpecialFeature::find($id)->delete();
            flash_message('success', $this->main_title.' Deleted Successfully');
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

        $feature_name_count = SpecialFeature::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($feature_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Special Feature can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = SpecialFeature::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        return ['status' => 1, 'message' => ''];
    }
}