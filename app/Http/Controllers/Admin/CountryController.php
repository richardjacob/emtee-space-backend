<?php

/**
 * Country Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Country
 * @author      Trioangle Product Team
 * @version     1.6
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\CountryDataTable;
use App\Models\Country;
use App\Models\SpaceLocation;
use App\Models\Reservation;
use App\Models\PayoutPreferences;
/*HostExperiencePHPCommentStart*/
use App\Models\HostExperienceLocation;
/*HostExperiencePHPCommentEnd*/
use App\Http\Start\Helpers;
use Validator;

class CountryController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Country
     *
     * @param array $dataTable  Instance of CountryDataTable
     * @return datatable
     */
    public function index(CountryDataTable $dataTable)
    {
        return $dataTable->render('admin.country.view');
    }

    /**
     * Add a New Country
     *
     * @param array $request  Input values
     * @return redirect     to Country view
     */
    public function add(Request $request)
    {
        if(!$_POST) {
            return view('admin.country.add');
        }
        else if($request->submit) {
            // Add Country Validation Rules
            $rules = array(
                'short_name' => 'required|unique:country',
                'long_name'  => 'required|unique:country',
                'phone_code' => 'required'
            );

            // Add Country Validation Custom Names
            $attributes = array(
                'short_name' => 'Short Name',
                'long_name'  => 'Long Name',
                'phone_code' => 'Phone Code'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $country = new Country;

            $country->short_name = $request->short_name;
            $country->long_name  = $request->long_name;
            $country->iso3       = $request->iso3;
            $country->num_code   = $request->num_code;
            $country->phone_code = $request->phone_code;

            $country->save();

            flash_message('success', 'Added Successfully');

            return redirect()->route('country');
        }
        return redirect()->route('country');
    }

    /**
     * Update Country Details
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function update(Request $request)
    {
        if(!$_POST) {
            $data['result'] = Country::find($request->id);

            if(!$data['result']) {
                flash_message('error', 'Country Not Found');
                return redirect()->route('country');
            }

            return view('admin.country.edit', $data);
        }
        else if($request->submit) {
            // Edit Country Validation Rules
            $rules = array(
                'short_name' => 'required|unique:country,short_name,'.$request->id,
                'long_name'  => 'required|unique:country,long_name,'.$request->id,
                'phone_code' => 'required'
            );

            // Edit Country Validation Custom Fields Name
            $attributes = array(
                'short_name' => 'Short Name',
                'long_name'  => 'Long Name',
                'phone_code' => 'Phone Code'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes); 

            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $country = Country::find($request->id);

            $country->short_name = $request->short_name;
            $country->long_name  = $request->long_name;
            $country->iso3       = $request->iso3;
            $country->num_code   = $request->num_code;
            $country->phone_code = $request->phone_code;

            $country->save();

            flash_message('success', 'Updated Successfully');
            return redirect()->route('country');
        }

        return redirect()->route('country');
    }

    /**
     * Delete Country
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function delete(Request $request)
    {
        $country_code = Country::find($request->id)->short_name;
        $can_delete = $this->canDestroy($country_code);
        
        if($can_delete['status'] == 0) {
            flash_message('error',$can_delete['message']);
            return redirect()->route('country');
        }

        try {
            Country::find($request->id)->delete();
            flash_message('success', 'Country Deleted Successfully');
        }
        catch(\Exception $e) {
            flash_message('danger', 'Country Already Deleted');
        }

        return redirect()->route('country');
    }

    /**
     * Validate Country can destroyed or not.
     *
     * @param  String  $country_code
     * @return Array
     */
    protected function canDestroy($country_code)
    {
        $payout_preferences_count = PayoutPreferences::where('country', $country_code)->count();
        if ($payout_preferences_count > 0) {
            return ['status' => 0, 'message' => 'Some User\'s Payout Preferences have this Country. So, can\'t delete the country.'];
        }

        $reservation_count = Reservation::where('country', $country_code)->count();
        if ($reservation_count > 0) {
            return ['status' => 0, 'message' => 'Some Reservations have this Country. So, can\'t delete the country.'];
        }

        $addr_count = SpaceLocation::where('country', $country_code)->count();
        if($addr_count > 0) {
            return ['status' => 0, 'message' => 'Some Space have this Country. So, Delete that Space or Change that Space Country.'];
        }

        return ['status' => 1, 'message' => ''];
    }
}