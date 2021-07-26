<?php

/**
 * Language Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Language
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\LanguageDataTable;
use App\Models\Language;
use App\Models\KindOfSpaceLang;
use App\Models\GuestAccessLang;
use App\Models\ServicesLang;
use App\Models\StyleLang;
use App\Models\SpecialFeatureLang;
use App\Models\SpaceRuleLang;
use App\Models\AmenitiesLang;
use App\Models\ActivityTypeLang;
use App\Models\ActivityLang;
use App\Models\SubActivityLang;
use App\Http\Start\Helpers;
use Validator;

class LanguageController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Language
     *
     * @param array $dataTable  Instance of LanguageDataTable
     * @return datatable
     */
    public function index(LanguageDataTable $dataTable)
    {
        return $dataTable->render('admin.language.view');
    }

    /**
     * Add a New Language
     *
     * @param array $request  Input values
     * @return redirect     to Language view
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.language.add');
        }
        else if($request->submit)
        {
            // Add Language Validation Rules
            $rules = array(
                    'name'   => 'required|unique:language',
                    'value'  => 'required|unique:language',
                    'is_translatable'  => 'required',
                    'status' => 'required'
                    );

            // Add Language Validation Custom Names
            $niceNames = array(
                        'name'    => 'Name',
                        'value'   => 'Value',
                        'is_translatable'  => 'Is Translatable',
                        'status'  => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $language = new Language;

                $language->name   = $request->name;
                $language->value  = $request->value;
                $language->is_translatable  = $request->is_translatable;
                $language->status = $request->status;
                $language->default_language = '0';

                $language->save();

                $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function

                return redirect(ADMIN_URL.'/language');
            }
        }
        else
        {
            return redirect(ADMIN_URL.'/language');
        }
    }

    /**
     * Update Language Details
     *
     * @param array $request    Input values
     * @return redirect     to Language View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
			$data['result'] = Language::find($request->id);

            if(!$data['result']) 
                abort('404');

            return view('admin.language.edit', $data);
        }
        else if($request->submit)
        {
            // Edit Language Validation Rules
            $rules = array(
                    'name'   => 'required|unique:language,name,'.$request->id,
                    'value'  => 'required|unique:language,value,'.$request->id,
                    'is_translatable'  => 'required',
                    'status' => 'required'
                    );

            // Edit Language Validation Custom Fields Name
            $niceNames = array(
                        'name'    => 'Name',
                        'value'   => 'Value',
                        'is_translatable'  => 'Is Translatable',
                        'status'  => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $language = Language::find($request->id);
                if($language->value == 'en'){
                    $this->helper->flash_message('error','Cannot Edit English Language');
                    return back();
                }
                if($request->status == 'Inactive' || $request->value != $language->value || $request->is_translatable == '0')
                {
                    $result= $this->canDestroy($language,true);
                    if($result['status'] == 0)
                    {
                        $this->helper->flash_message('error',$result['message']);
                        return back();
                    }
                }

			    $language->name   = $request->name;
                $language->value  = $request->value;
                $language->is_translatable  = $request->is_translatable;
                $language->status = $request->status;

                $language->save();

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
                
                return redirect(ADMIN_URL.'/language');
            }
        }
        else
        {
            return redirect(ADMIN_URL.'/language');
        }
    }

    /**
     * Delete Language
     *
     * @param array $request    Input values
     * @return redirect     to Language View
     */
    public function delete(Request $request)
    {
        $language = Language::where('id', $request->id)->first();
        if($language->value == 'en'){
            $this->helper->flash_message('error','Cannot delete English Language');
            return back();
        }
        $result = $this->canDestroy($language,false);
        if($result['status'] == 0)
        {
            $this->helper->flash_message('error',$result['message']);
            return back();
        }
        $language->delete();

        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect(ADMIN_URL.'/language');
    }

    public function canDestroy($language, $check_trans)
    {
        $return  = ['status' => '1', 'message' => ''];

        $active_language_count = Language::where('status', 'Active')->count();
        if($active_language_count < 1) {
            $return = ['status' => 0, 'message' => 'Sorry, Minimum one Active language is required.'];
        }

        $is_default_language  = $language->default_language == 1;
        if($is_default_language) {
            $return = ['status' => 0, 'message' => 'Sorry, This language is Default Language. So, change the Default Language.'];
        }

        if($check_trans) {
            return $return;
        }

        $has_translation  = $this->hasLanguageTranslation($language->value);
        if($has_translation['status']) {
            $return = ['status' => 0, 'message' => 'Sorry, This language has '. $has_translation['type'] .' Translation. So, cannot delete this.'];
        }

        return $return;
    }

    /**
     * Check Given Language Already used in any translation
     *
     * @param String $code
     * @return Array $return Contains status and type
     */
    public function hasLanguageTranslation($code)
    {
        $trans_count = KindOfSpaceLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Space Type'];
        }

        $trans_count = GuestAccessLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Guest Access'];
        }

        $trans_count = ServicesLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Services & Extra'];
        }

        $trans_count = StyleLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Style'];
        }

        $trans_count = SpecialFeatureLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Special Feature'];
        }

        $trans_count = SpaceRuleLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Space Rule'];
        }

        $trans_count = AmenitiesLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Amenities'];
        }

        $trans_count = ActivityTypeLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Activity Type'];
        }

        $trans_count = ActivityLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Activity'];
        }

        $trans_count = SubActivityLang::whereLangCode($code)->count();
        if($trans_count > 0) {
            return ['status' => 1, 'type' => 'Sub Activity'];
        }

        return ['status' => 0, 'type' => ''];
    }

}
