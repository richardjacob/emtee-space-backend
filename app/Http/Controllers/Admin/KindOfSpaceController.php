<?php

/**
 * Space Type Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Space Type
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\KindOfSpaceDataTable;
use App\Models\KindOfSpace;
use App\Models\KindOfSpaceLang;
use App\Models\Space;
use App\Models\language;
use App\Http\Start\Helpers;
use Validator;
use Illuminate\Support\Arr;

class KindOfSpaceController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Space Type';
        $this->view_data['base_url'] = $this->base_url = route('kind_of_space');
        $this->view_data['add_url']  = route('create_kind_of_space');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.kind_of_space.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of KindOfSpaceDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(KindOfSpaceDataTable $dataTable)
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

        $folder = dirname($_SERVER['SCRIPT_FILENAME']).'/images/space_type';
        if(!file_exists($folder))
        {
            mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/space_type', 0777, true);
        }   
        $target_dir = '/images/space_type';
        $upload_result = uploadImage($request->file('image'),$target_dir,'space_type_');
        if($upload_result['status'] != 'Success') {
            flash_message('danger',$upload_result['status_message']);
            return back();
        }

        $file_name = $upload_result['file_name'];

        $kind_of_space = new KindOfSpace;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $kind_of_space->name      = $request->name[$i];
                $kind_of_space->status      = $request->status;
                $kind_of_space->image     = $file_name;
                $kind_of_space->save();
                $lastInsertedId = $kind_of_space->id;
            }
            else {
                $kind_of_space_lang = new KindOfSpaceLang;
                $kind_of_space_lang->kind_of_space_id = $lastInsertedId;
                $kind_of_space_lang->lang_code   = $request->lang_code[$i];
                $kind_of_space_lang->name        = $request->name[$i];
                $kind_of_space_lang->save();
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
        $this->view_data['result']      = KindOfSpace::find($id);
        $this->view_data['langresult']  = KindOfSpaceLang::where('kind_of_space_id',$id)->get();
      
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
        // Delete Property Type
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);  
        if(empty($lang_id_arr)) {
            $kind_of_space_lang = KindOfSpaceLang::where('kind_of_space_id',$request->id); 
            $kind_of_space_lang->delete();
        }

        $property_del = KindOfSpaceLang::select('id')->where('kind_of_space_id',$request->id)->get();
        foreach($property_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $kind_of_space_lang = KindOfSpaceLang::find($values->id); 
                $kind_of_space_lang->delete();
            }
        }

        

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }


        if($request->file('images')) {

            $folder = dirname($_SERVER['SCRIPT_FILENAME']).'/images/space_type';
            if(!file_exists($folder))
            {
                mkdir(dirname($_SERVER['SCRIPT_FILENAME']).'/images/space_type', 0777, true);
            }

            $target_dir = '/images/space_type';
            $upload_result = uploadImage($request->file('images'),$target_dir,'space_type_');
            if($upload_result['status'] != 'Success') {
                flash_message('danger',$upload_result['status_message']);
                return back();
            }

            $file_name = $upload_result['file_name'];
        }

        //Update Property type
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $kind_of_space = KindOfSpace::find($request->id);
                $kind_of_space->name        = $request->name[$i];
                $kind_of_space->status      = $request->status;
                if(isset($file_name)) {
                $kind_of_space->image      = $file_name;
                }
                $kind_of_space->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $kind_of_space_lang = KindOfSpaceLang::find($request->lang_id[$i]);
                    $kind_of_space_lang->lang_code   = $request->lang_code[$i];
                    $kind_of_space_lang->name        = $request->name[$i];
                    $kind_of_space_lang->save();            
                } 
                else{
                    $kind_of_space_lang =  new KindOfSpaceLang; 
                    $kind_of_space_lang->kind_of_space_id   = $request->id;    
                    $kind_of_space_lang->lang_code   = $request->lang_code[$i];
                    $kind_of_space_lang->name        = $request->name[$i];
                    $kind_of_space_lang->save();
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
            KindOfSpaceLang::where('kind_of_space_id',$id)->delete();
            KindOfSpace::find($id)->delete();
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
            'image' => 'mimes:png,jpeg,jpg,gif,svg,webp',
        );

        $messages = array(

        );

        $attributes = array(
            'status' => 'Status',
            'image'  => 'Image',

        );

        $validator = Validator::make($request_data, $rules, $messages, $attributes);

        $space_type_name_count = KindOfSpace::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($space_type_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Space Type can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = KindOfSpace::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $space_type = Space::whereIn('space_type', Arr::wrap($id))->count();
        if($space_type > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return ['status' => 1, 'message' => ''];
    }

    public function popular(Request $request)
    {
        $space_type = KindOfSpace::findOrFail($request->id);

        if($space_type->popular == 'No') {
            if($space_type->status != 'Active') {
                flash_message('error', 'Not able to popular for Inactive Activity');
                return back();
            }        }

        $space_type->popular = ($space_type->popular == 'Yes') ? 'No' : 'Yes';
        $space_type->save();

        flash_message('success', 'Updated Successfully');
        return redirect()->route('kind_of_space');
    }

}