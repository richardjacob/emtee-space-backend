<?php

/**
 * Sub Activity DataTable
 *
 * @package     Makent Space
 * @subpackage  DataTable
 * @category    Sub Activity
 * @author      Trioangle Product Team
 * @version     2.0
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\SubActivity;

class SubActivityDataTable extends DataTable
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
            ->addColumn('activity_name', function ($sub_activities) {
                return $sub_activities->activity_name;
            })
            ->addColumn('action', function ($sub_activity) {
                return '<a href="'.route('edit_sub_activity',['id' => $sub_activity->id]).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;<a class="btn btn-xs btn-primary" data-href="'.route('delete_sub_activity',['id' => $sub_activity->id]).'" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \SubActivity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SubActivity $model)
    {
        return $model->with('activity')->get();
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
                    ->addAction(["printable" => false])
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
            ['data' => 'activity_name', 'name' => 'activity_name', 'title' => 'Activity'],
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
        return 'sub_activity_' . date('YmdHis');
    }
}