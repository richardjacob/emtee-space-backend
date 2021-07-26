<?php

/**
 * Space Rules Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Space Rules
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\SpaceRulesDataTable;
use App\Models\SpaceRule;
use App\Models\SpaceRuleLang;
use App\Models\language;
use App\Models\Space;
use App\Http\Start\Helpers;
use Validator;

class SpaceRulesController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = $this->main_title = 'Space Rule';
        $this->view_data['base_url'] = $this->base_url = route('space_rules');
        $this->view_data['add_url']  = route('create_space_rule');
        $this->view_data['base_view_path'] = $this->base_view_path = 'admin.space_rule.';
    }

    /**
     * Display a listing of the resource.
     *
     * @param array $dataTable  Instance of SpaceRulesDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SpaceRulesDataTable $dataTable)
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

        $space_rule = new SpaceRule;
        
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i] == "en") {
                $space_rule->name      = $request->name[$i];
                $space_rule->status      = $request->status;
                $space_rule->save();
                $lastInsertedId = $space_rule->id;
            }
            else {
                $space_rule_lang = new SpaceRuleLang;
                $space_rule_lang->space_rule_id = $lastInsertedId;
                $space_rule_lang->lang_code   = $request->lang_code[$i];
                $space_rule_lang->name        = $request->name[$i];
                $space_rule_lang->save();
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
        $this->view_data['result']      = SpaceRule::find($id);
        $this->view_data['langresult']  = SpaceRuleLang::where('space_rule_id',$id)->get();

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
        // Delete Space Rule
        $lang_id_arr = $request->lang_id;
        unset($lang_id_arr[0]);  
        if(empty($lang_id_arr)) {
            $space_rule_lang = SpaceRuleLang::where('space_rule_id',$request->id); 
            $space_rule_lang->delete();
        }

        $space_rule_del = SpaceRuleLang::select('id')->where('space_rule_id',$request->id)->get();
        foreach($space_rule_del as $values) {
            if(!in_array($values->id,$lang_id_arr)) {
                $space_rule_lang = SpaceRuleLang::find($values->id); 
                $space_rule_lang->delete();
            }
        }

        // Validate Data
        $validate_return = $this->validate_request_data($request->all(),$id);
        if($validate_return) {
            return $validate_return;
        }

        //Update Space Rule
        for($i=0;$i < count($request->lang_code);$i++) {
            if($request->lang_code[$i]=="en") {
                $space_rule = SpaceRule::find($request->id);
                $space_rule->name        = $request->name[$i];
                $space_rule->status      = $request->status;
                $space_rule->save();
            }
            else {
                if(isset($request->lang_id[$i])) {
                    $space_rule_lang = SpaceRuleLang::find($request->lang_id[$i]);
                    $space_rule_lang->lang_code   = $request->lang_code[$i];
                    $space_rule_lang->name        = $request->name[$i];
                    $space_rule_lang->save();            
                } 
                else{
                    $space_rule_lang =  new SpaceRuleLang; 
                    $space_rule_lang->space_rule_id   = $request->id;    
                    $space_rule_lang->lang_code   = $request->lang_code[$i];
                    $space_rule_lang->name        = $request->name[$i];
                    $space_rule_lang->save();
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
            SpaceRuleLang::where('space_rule_id',$id)->delete();
            SpaceRule::find($id)->delete();
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

        $rule_name_count = SpaceRule::whereNotIn('id',[$id])->where('name',$request_data['name'][0])->count();

        if($rule_name_count > 0) {
            flash_message('error', 'This Name already exists');
            return redirect($this->base_url);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Validate Space Rule can destroyed or not.
     *
     * @param  int  $id
     * @return Array
     */
    protected function canDestroy(int $id)
    {
        $active_count   = SpaceRule::whereNotIn('id',[$id])->where('status','Active')->count();

        if($active_count <= 0) {
            $return = ['status' => 0, 'message' => 'Atleast one Active '.$this->main_title.' in admin panel. So can\'t delete this'];
        }

        $rules_count = Space::whereRaw('find_in_set('.$id.', space_rules)')->count();
        if($rules_count > 0) {
            return ['status' => 0, 'message' => 'Space have this '.$this->main_title.'. So, Delete that Space or Change that Space '.$this->main_title.'.'];
        }

        return ['status' => 1, 'message' => ''];
    }
}