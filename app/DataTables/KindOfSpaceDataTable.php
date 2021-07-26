<?php

/**
 * KindOfSpace DataTable
 *
 * @package     Makent Space
 * @subpackage  DataTable
 * @category    KindOfSpace
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\KindOfSpace;
use Helpers;

class KindOfSpaceDataTable extends DataTable 
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
            ->addColumn('popular_val', function ($space) {
                $class = ($space->popular == 'No') ? 'danger' : 'success';
                $popular = '<a href="'.route('admin.popular_space_type',[$space->id]).'" class="btn btn-xs btn-'.$class.'">'.$space->popular.'</a>';
                return $popular;
            })
            ->addColumn('popular', function ($space) {
                return $space->popular;
            })
            ->addColumn('action', function ($property_type) {   
                return '<a href="'.route('edit_kind_of_space',['id' => $property_type->id]).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;<a class="btn btn-xs btn-primary" data-href="'.route('delete_kind_of_space',['id' => $property_type->id]).'" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';
            })
            ->rawColumns(['popular_val','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \KindOfSpace $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(KindOfSpace $model)
    {
        return $model->select();
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
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'popular_val', 'name' => 'popular_val', 'title' => 'Popular'],            
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'kind_of_space_' . date('YmdHis');
    }
}