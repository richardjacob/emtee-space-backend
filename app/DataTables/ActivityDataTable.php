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
use App\Models\Activity;
use Helpers;

class ActivityDataTable extends DataTable 
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
            ->addColumn('popular', function ($activity) {
                $class = ($activity->popular == 'No') ? 'danger' : 'success';
                $popular = '<a href="'.route('admin.popular_activity',[$activity->id]).'" class="btn btn-xs btn-'.$class.'">'.$activity->popular.'</a>';
                return $popular;
            })
             ->addColumn('activity_type_name', function ($activity) {
                return $activity->activity_type_name;
            })
            ->addColumn('action', function ($activity) {
                $edit = '<a href="'.route('edit_activity',['id' => $activity->id]).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';
                $delete = '<a class="btn btn-xs btn-primary" data-href="'.route('delete_activity',['id' => $activity->id]).'" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';
                return $edit.'&nbsp;'.$delete;
            })
           ->rawColumns(['popular','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Activity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Activity $model)
    {
        return $model->with('activity_type')->get();
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
            ['data' => 'activity_type_name', 'name' => 'activity_type_name', 'title' => 'Activity Type'],            
            ['data' => 'popular', 'name' => 'popular', 'title' => 'Popular'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'activity_' . date('YmdHis');
    }
}