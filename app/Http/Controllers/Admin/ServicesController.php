<?php

/**
 * Services & Extras Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Services & Extras
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ServicesDataTable;
use App\Models\Services;
use App\Models\ServicesLang;
use App\Models\language;
use App\Models\Space;
use App\Http\Start\Helpers;
use Validator;

class ServicesController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Services & Extras';
        $this->view_data['base_url'] = $this->base_url = route('services');
        $this->view_data['add_url']  = route('create_services');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.services.';
    }

    /**
     * Load Datatable for Bed Type
     *
     * @param array $dataTable  Instance of ServicesDataTable
     * @return datatable
     */
    public function index(ServicesDataTable $dataTable)
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

        $service = new Services;

        for($i=0;$i < count($request->lang_code);$i++) {
          if($request->lang_code[$i]=="en") {
            $service->name        = $request->name[$i];
            $service->status      = $request->status;
            $service->save();
            $lastInsertedId = $service->id;
          }
          else {
            $service_lang = new ServicesLang;
            $service_lang->services_id = $lastInsertedId;
            $service_lang->lang_code   = $request->lang_code[$i];
            $service_lang->name        = $request->name[$i];      
            $service_lang->save();
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
        $this->view_data['result']      = Services::find($id);
        $this->view_data['langresult']  = ServicesLang::where('services_id',$id)->get();

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
        // Delete Old Services & Extra
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);

        if(empty($lang_id_arr)) {
            ServicesLang::where('services_id',$request->id)->delete();
        }

        $services_lang = ServicesLang::select('id')->where('services_id',$request->id)->get();
        
        foreach($services_lang as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                ServicesLang::find($values->id)->delete(); 
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $service = Services::find($request->id);
                $service->name        = $request->name[$i];        
                $service->status      = $request->status;
                $service->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $service_lang = ServicesLang::find($request->lang_id[$i]);
                    $service_lang->lang_code   = $request->lang_code[$i];
                    $service_lang->name        = $request->name[$i];            
                    $service_lang->save();            
                } 
                else {
                    $service_lang =  new ServicesLang; 
                    $service_lang->services_id   = $request->id;    
                    $service_lang->lang_code   = $request->lang_code[$i];
                    $service_lang->name        = $request->name[$i];              
                    $service_lang->save();
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
            ServicesLang::where('services_id',$id)->delete();
            Services::find($id)->delete();
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

        $services_count = Services::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($services_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Services & Extra can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = Services::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        return ['status' => 1, 'message' => ''];
    }
}