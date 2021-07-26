<?php

/**
 * Referrals Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Referrals
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\ReferralsDataTable;
use App\Models\Referrals;
use App\Http\Start\Helpers;
use Validator;

class ReferralsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Amenities Type
     *
     * @param array $dataTable  Instance of ReferralsDataTable
     * @return datatable
     */
    public function index(ReferralsDataTable $dataTable)
    {
        return $dataTable->render('admin.referrals.view');
    }

    public function details(Request $request) 
    {
        $data['result'] = Referrals::whereUserId($request->id)->get();
        return view('admin.referrals.detail', $data);
    }
}
