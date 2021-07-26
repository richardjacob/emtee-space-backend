<?php

/**
 * Event Service Provider
 *
 * @package     Makent Space
 * @subpackage  Provider
 * @category    Event
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Space;
use App\Models\SpaceActivities;
use App\Models\ActivityPrice;
use App\Models\SpaceLocation;
use App\Models\SpacePhotos;
use App\Models\SpaceStepsStatus;
use App\Observers\SpaceObserver;
use App\Http\Controllers\EmailController;
use Schema;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if(env('DB_DATABASE') != '') {
            if(!defined('ADMIN_URL')) {
                $admin_url = 'admin';
            }
            else {
                $admin_url = ADMIN_URL;
            }
            $this->not_admin =  (request()->segment(1) != $admin_url);

            if(Schema::hasTable('space')) {
                Space::observe(SpaceObserver::class);
                $this->registerSpaceLocationEvent();
                $this->registerSpacePhotosEvent();
                $this->registerSpaceActivityEvent();
                $this->registerActivityPriceEvent();
            }
        }
    }

    protected function registerActivityPriceEvent()
    {
        // Update Pricing Step Status when create new Activity Price
        ActivityPrice::created(function ($activity_price) {
            $space_status = SpaceStepsStatus::find($activity_price->space_id);
            $space_status->pricing = '0';
            $space_status->save();
        });

        ActivityPrice::updating(function ($activity_price) {
            $space_detail = Space::find($activity_price->space_id);

            $changed_cols = array_keys($activity_price->getDirty());
            $intersect_cos = array_intersect($space_detail->mandatory_fields,$changed_cols);
            $can_require_verification = count($intersect_cos) > 0;
            logger($this->not_admin);
            if($this->not_admin && $can_require_verification && $this->isSpaceApproved($space_detail->status)) {
                $space_detail->status = 'Pending';
                $space_detail->admin_status = 'Pending';
                $space_detail->save();
                $this->sendApprovalMail($space_detail->id);
            }
        });
    }

    protected function registerSpaceActivityEvent()
    {
        SpaceActivities::created(function ($activity) {
            $space_detail = Space::find($activity->space_id);

            if($this->not_admin && $this->isSpaceApproved($space_detail->status)) {
                $space_detail->status = 'Pending';
                $space_detail->admin_status = 'Pending';
                $space_detail->save();
                $this->sendApprovalMail($space_detail->id);
            }
        });

        SpaceActivities::updating(function ($activity) {
            $space_detail = Space::find($activity->space_id);

            $changed_cols = array_keys($activity->getDirty());
            $intersect_cos = array_intersect($space_detail->mandatory_fields,$changed_cols);
            $can_require_verification = count($intersect_cos) > 0;
            if($this->not_admin && $can_require_verification && $this->isSpaceApproved($space_detail->status)) {
                $space_detail->status = 'Pending';
                $space_detail->admin_status = 'Pending';
                $space_detail->save();
                $this->sendApprovalMail($space_detail->id);
            }
        });
    }

    protected function registerSpaceLocationEvent()
    {
        SpaceLocation::updating(function ($space_location) {
            $space_detail = Space::find($space_location->space_id);

            $changed_cols = array_keys($space_location->getDirty());
            $intersect_cos = array_intersect($space_detail->mandatory_fields,$changed_cols);
            $can_require_verification = count($intersect_cos) > 0;
            if($this->not_admin && $can_require_verification && $this->isSpaceApproved($space_detail->status)) {
                $space_detail->status = 'Pending';
                $space_detail->admin_status = 'Pending';
                $space_detail->save();
                $this->sendApprovalMail($space_detail->id);
            }
        });
    }

    protected function registerSpacePhotosEvent()
    {
        SpacePhotos::created(function ($space_photos) {
            $space_detail = Space::find($space_photos->space_id);

            if($this->not_admin && $this->isSpaceApproved($space_detail->status)) {
                $space_detail->status = 'Pending';
                $space_detail->admin_status = 'Pending';
                $space_detail->save();
                $this->sendApprovalMail($space_detail->id);
            }
        });
    }

    protected function isSpaceApproved($status)
    {
        $space_approved = ($status == 'Listed' || $status == 'Resubmit');
        return $space_approved;
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
