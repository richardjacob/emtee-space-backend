<?php

/**
 * Style Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Style
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\StyleDataTable;
use App\Models\Style;
use App\Models\StyleLang;
use App\Models\language;
use App\Models\Space;
use App\Http\Start\Helpers;
use Validator;

class StyleController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Style';
        $this->view_data['base_url'] = $this->base_url = route('styles');
        $this->view_data['add_url']  = route('create_style');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.style.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of StyleDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(StyleDataTable $dataTable)
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

        $style = new Style;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $style->name      = $request->name[$i];
                $style->status      = $request->status;
                $style->save();
                $lastInsertedId = $style->id;
            }
            else {
                $style_lang = new StyleLang;
                $style_lang->style_id   = $lastInsertedId;
                $style_lang->lang_code   = $request->lang_code[$i];
                $style_lang->name        = $request->name[$i];
                $style_lang->save();
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
        $this->view_data['result']      = Style::find($id);
        $this->view_data['langresult']  = StyleLang::where('style_id',$id)->get();

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
        // Delete Style Languages
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);  
        if(empty($lang_id_arr)) {
            $style_lang = StyleLang::where('style_id',$request->id); 
            $style_lang->delete();
        }

        $stylelang_del = StyleLang::select('id')->where('style_id',$request->id)->get();
        foreach($stylelang_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $style_lang = StyleLang::find($values->id); 
                $style_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $style = Style::find($request->id);
                $style->name        = $request->name[$i];
                $style->status      = $request->status;
                $style->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $style_lang = StyleLang::find($request->lang_id[$i]);
                    $style_lang->lang_code   = $request->lang_code[$i];
                    $style_lang->name        = $request->name[$i];
                    $style_lang->save();            
                } 
                else{
                    $style_lang =  new StyleLang; 
                    $style_lang->style_id    = $request->id;    
                    $style_lang->lang_code   = $request->lang_code[$i];
                    $style_lang->name        = $request->name[$i];
                    $style_lang->save();
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
            StyleLang::where('style_id',$id)->delete();
            Style::find($id)->delete();
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

        $style_name_count = Style::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($style_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Style can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = Style::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            return ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $style_count = Space::whereRaw('find_in_set('.$id.', space_style)')->count();
        if($style_count > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return ['status' => 1, 'message' => ''];
    }
}