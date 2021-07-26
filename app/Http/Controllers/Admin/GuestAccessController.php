<?php

/**
 * Guest Access Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Guest Access
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\GuestAccessDataTable;
use App\Models\GuestAccess;
use App\Models\GuestAccessLang;
use App\Models\language;
use App\Models\Space;
use Validator;

class GuestAccessController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = $this->main_title = 'Guest Access';
        $this->view_data['base_url'] = $this->base_url = route('guest_access');
        $this->view_data['add_url']  = route('create_guest_access');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.guest_access.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of GuestAccessDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(GuestAccessDataTable $dataTable)
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

        $guest_access = new GuestAccess;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $guest_access->status      = $request->status;
                $guest_access->name        = $request->name[$i];
                $guest_access->save();
                $lastInsertedId = $guest_access->id;
            }
            else {
                $guest_access_lang = new GuestAccessLang;
                $guest_access_lang->guest_access_id = $lastInsertedId;
                $guest_access_lang->lang_code   = $request->lang_code[$i];
                $guest_access_lang->name        = $request->name[$i];
                $guest_access_lang->save();
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
        $this->view_data['result']      = GuestAccess::find($id);
        $this->view_data['langresult']  = GuestAccessLang::where('guest_access_id',$id)->get();

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
            $guest_access_lang = GuestAccessLang::where('guest_access_id',$request->id); 
            $guest_access_lang->delete();
        }

        $property_del = GuestAccessLang::select('id')->where('guest_access_id',$request->id)->get();
        foreach($property_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $guest_access_lang = GuestAccessLang::find($values->id); 
                $guest_access_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        //Update Property type
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $guest_access = GuestAccess::find($request->id);
                $guest_access->name        = $request->name[$i];
                $guest_access->status      = $request->status;
                $guest_access->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $guest_access_lang = GuestAccessLang::find($request->lang_id[$i]);
                    $guest_access_lang->lang_code   = $request->lang_code[$i];
                    $guest_access_lang->name        = $request->name[$i];
                    $guest_access_lang->save();            
                } 
                else{
                    $guest_access_lang =  new GuestAccessLang; 
                    $guest_access_lang->guest_access_id   = $request->id;    
                    $guest_access_lang->lang_code   = $request->lang_code[$i];
                    $guest_access_lang->name        = $request->name[$i];
                    $guest_access_lang->save();
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
            GuestAccessLang::where('guest_access_id',$id)->delete();
            GuestAccess::find($id)->delete();
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

        $property_name_count = GuestAccess::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($property_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Guest Access can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = GuestAccess::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $guest_access_count = Space::whereRaw('find_in_set('.$id.', guest_access)')->count();
        if($guest_access_count > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return ['status' => 1, 'message' => ''];
    }
}