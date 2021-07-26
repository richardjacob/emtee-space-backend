<?php

/**
 * Activity DataTable
 *
 * @package     Makent Space
 * @subpackage  DataTable
 * @category    Activity
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Space;
use App\Models\SpaceStepsStatus;
use Helpers;
use Auth;


class SpaceDataTable extends DataTable
{    
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query)
            ->addColumn('host_name', function ($space) {
                return $space->users->first_name;
            })
            ->addColumn('popular', function ($space) {
                $class = ($space->popular == 'No') ? 'danger' : 'success';
                $popular = '<a href="'.route('admin.popular_space',[$space->id]).'" class="btn btn-xs btn-'.$class.'">'.$space->popular.'</a>';
                return $popular;
            })
            ->addColumn('space_status', function ($space) {
                $status = $space->status;
                if ($status == null && $space->steps_count <= 0) {
                   $status = 'Pending';
                }
                return $status;
            })
            ->addColumn('verified', function ($space) {
                $verified =  '<select class="admin_space form-control" data-type="admin_status" id="'.$space->id.'" name="'.$space->id.'" ' . (($space->steps_count > 0) ? 'disabled="disabled"' : '') . '>
               <option value="Pending" '.($space->admin_status == 'Pending' ? ' selected="selected"' : '').'>Pending</option>
               <option value="Approved" '.($space->admin_status == 'Approved' ? ' selected="selected"' : '').' >Approved</option>
               <option value="Resubmit" '.($space->admin_status == 'Resubmit' ? ' selected="selected"' : '').' >Resubmit</option>
               </select>';

                return $verified;
            })
            ->addColumn('action', function ($space) {

                $edit = (Auth::guard('admin')->user()->can('edit_space')) ? '<a href="'.url(ADMIN_URL.'/edit_space/'.$space->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                $delete = (Auth::guard('admin')->user()->can('delete_space')) ? '<a data-href="'.url(ADMIN_URL.'/delete_space/'.$space->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>' : '';

                return $edit.$delete;
            })
            ->rawColumns(['popular','verified','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Space $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Space $model)
    {
        return $model->with('users')->get();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->addAction()
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->orderBy(0)
                    ->buttons(
                        ['csv','excel', 'print', 'reset']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return array(
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'host_name', 'name' => 'host_name', 'title' => 'Host Name'],
            ['data' => 'space_type_name', 'name' => 'space_type_name', 'title' => 'Space Type'],
            ['data' => 'space_status', 'name' => 'space_status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'],
             ['data' => 'views_count', 'name' => 'views_count', 'title' => 'Viewed Count'],
            ['data' => 'popular', 'name' => 'popular', 'title' => 'Popular'],
            ['data' => 'verified', 'name' => 'verified', 'title' => 'Verified', 'exportable' => false, 'printable'=>false],
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'space_' . date('YmdHis');
    }
}