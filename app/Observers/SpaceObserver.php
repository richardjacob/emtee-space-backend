<?php

/**
 * Space Observer
 *
 * @package     Makent Space
 * @subpackage  Observer
 * @category    Space
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Observers;

use App\Models\Space;
use App\Models\SpaceStepsStatus;
use App\Models\SpacePhotos;
use App\Models\SpaceDescription;
use App\Models\SpaceLocation;
use App\Models\SpaceAvailability;
use App\Http\Controllers\EmailController;

class SpaceObserver
{
    protected $not_admin;

    public function __construct()
    {
        $this->not_admin =  (request()->segment(1) != ADMIN_URL);
    }

    /**
     * Listen to the Space created event.
     *
     * @param  Space  $space_detail
     * @return void
     */
    public function created(Space $space_detail)
    {
        $space_step_status = new SpaceStepsStatus;
        $space_step_status->space_id = $space_detail->id;
        $space_step_status->save();

        $space_description = new SpaceDescription;
        $space_description->space_id = $space_detail->id;
        $space_description->save();

        $space_location = new SpaceLocation;
        $space_location->space_id = $space_detail->id;
        $space_location->country = view()->shared('default_country_code');
        $space_location->save();

        $day_options = getDayOptions();
        foreach ($day_options as $day_num => $day_name) {
            $space_availability = new SpaceAvailability;
            $space_availability->space_id = $space_detail->id;
            $space_availability->day = $day_num;
            $space_availability->save();
        }
    }

    /**
     * Listen to the Space updating event.
     *
     * @param  Space  $space_detail
     * @return void
     */
    public function updating(Space $space_detail)
    {
        if($space_detail->isDirty('status')) {
            if($space_detail->status == 'Unlisted') {
                $space_detail->popular = 'No';
            }
        }
        $changed_cols = array_keys($space_detail->getDirty());
        $intersect_cos = array_intersect($space_detail->mandatory_fields,$changed_cols);
        $can_require_verification = count($intersect_cos) > 0;
        if($this->not_admin && $can_require_verification && $space_detail->status != NULL) {
            $space_detail->status = 'Pending';
            $space_detail->admin_status = 'Pending';
            $this->sendApprovalMail($space_detail->id);
        }

    }

    /**
     * Listen to the Space updated event.
     *
     * @param  Space  $space_detail
     * @return void
     */
    public function updated(Space $space_detail)
    {
        $space_status = SpaceStepsStatus::find($space_detail->id);
        $space_status->basics       = 0;
        $space_status->description  = 0;

        $space_address = SpaceLocation::where('space_id',$space_detail->id)->first();

        if($space_detail->space_type != '' && $space_detail->sq_ft > 0 && $space_detail->number_of_guests > 0 && $space_detail->guest_access != '' && $space_address->latitude != '' && $space_address->longitude != '') {
            $space_status->basics = 1;
        }
        if($space_detail->name != '' && $space_detail->summary != '') {
            $space_status->description = 1;
        }
        $space_status->save();
    }

    /**
     * Send waiting for approval mail to admin and host
     *
     * @param String $space_id
     */
    protected function sendApprovalMail($space_id)
    {
        $email_controller = new EmailController;
        $email_controller->awaiting_approval_admin($space_id);
        $email_controller->awaiting_approval_host($space_id);
    }
}