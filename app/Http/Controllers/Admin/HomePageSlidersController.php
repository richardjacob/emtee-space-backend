<?php

/**
 * Home Page Slider Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Home Page Slider
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HomePageSlidersDataTable;
use App\Models\HomePageSlider;
use App\Http\Start\Helpers;
use Validator;

class HomePageSlidersController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->view_data['main_title'] = 'Home page Slider';
    }

    /**
     * Load Datatable for Slider
     *
     * @param array $dataTable  Instance of HomePageSlidersDataTable
     * @return datatable
     */
    public function index(HomePageSlidersDataTable $dataTable)
    {
        return $dataTable->render('admin.home_page_sliders.view',$this->view_data);
    }

    /**
     * Add a New Slider
     *
     * @param array $request  Input values
     * @return redirect     to Slider view
     */
    public function add(Request $request)
    {
        if(!$_POST) {
            return view('admin.home_page_sliders.add',$this->view_data);
        }
        else if($request->submit)
        {
            // Add Slider Validation Rules
            $rules = array(
                'image'   => 'required|mimes:jpg,png,gif,jpeg,webp',
                'order'   => 'required',
                'status'  => 'required',
            );

            // Add Slider Validation Custom Names
            $attributes = array(
                'image'    => 'Image',
                'order'   => 'Position', 
                'status'  => 'Status',
            );

            // Validate Request
            $request->validate($rules, array(), $attributes);
            $image     =   $request->file('image');

            if(UPLOAD_DRIVER=='cloudinary') {
                $c=$this->helper->cloud_upload($image);
                if($c['status']!="error") {
                    $filename=$c['message']['public_id'];
                    $source    = 'Cloudinary';
                }
                else {
                    flash_message('danger', $c['message']); // Call flash message function
                    return redirect()->route('homepage_sliders');
                }
            }
            else {
                $extension = $image->getClientOriginalExtension();
                $filename  = 'home_page_slider_'.time() . '.' . $extension;
                $source    = 'Local';

                $success = $image->move('images/slider', $filename);

                if(!$success)
                    return back()->withError('Could not upload Image');
            }

            $slider = new HomePageSlider;

            $slider->image = $filename;
            $slider->source = $source;
            $slider->order = $request->order; 
            $slider->status = $request->status;

            $slider->save();

            flash_message('success', 'Added Successfully'); // Call flash message function
            return redirect()->route('homepage_sliders');
        }

        return redirect()->route('homepage_sliders');
    }

    /**
     * Update Slider Details
     *
     * @param array $request    Input values
     * @return redirect     to Slider View
     */
    public function update(Request $request)
    {
        if(!$_POST) {
            $this->view_data['result'] = HomePageSlider::find($request->id);
            return view('admin.home_page_sliders.edit', $this->view_data);
        }
        else if($request->submit) {

            // Update Slider Validation Rules
            $rules = array(
                'image'   => 'mimes:jpg,jpeg,png,gif,webp',
                'order'   => 'required', 
                'status'  => 'required',
            );

            // Update Slider Validation Custom Names
            $attributes = array(
                'order'   => 'Position', 
                'status'  => 'Status',
                'image'    => 'Image',
            );

            // Validate Request
            $request->validate($rules, array(), $attributes);

            $slider = HomePageSlider::find($request->id);

            $image     =   $request->file('image');

            if($image) {

                if(UPLOAD_DRIVER=='cloudinary') {
                    $c=$this->helper->cloud_upload($request->file('image'));
                    if($c['status'] != "error") {
                        $filename=$c['message']['public_id'];
                        $source    = 'Cloudinary';
                    }
                    else {
                        flash_message('danger', $c['message']);
                        return redirect()->route('homepage_sliders');
                    }
                }
                else {
                    $extension = $image->getClientOriginalExtension();
                    $filename  = 'home_page_slider_'.time() . '.' . $extension;
                    $source    = 'Local';

                    $success = $image->move('images/slider', $filename);
                    if(!$success)
                        return back()->withError('Could not upload Image');
                }
                $slider->image = $filename;
                $slider->source = $source;
            }

            $slider->order      = $request->order;
            $slider->status     = $request->status;
            $slider->updated_at = date('Y-m-d H:i:s');

            $slider->save();

            flash_message('success', 'Updated Successfully'); // Call flash message function
            return redirect()->route('homepage_sliders');
        }

        return redirect()->route('homepage_sliders');
    }

    /**
     * Delete HomePageSlider
     *
     * @param array $request    Input values
     * @return redirect     to Slider View
     */
    public function delete(Request $request)
    {
        $slider = HomePageSlider::find($request->id);
        if($slider != '') {
            $slider->delete();
            flash_message('success', 'Deleted Successfully');
        }

        return redirect()->route('homepage_sliders');
    }

}
