<?php

/**
 * HomePage Sliders DataTable
 *
 * @package     Makent Space
 * @subpackage  DataTable
 * @category    HomePage Sliders
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\HomePageSlider;
use Helpers;

class HomePageSlidersDataTable extends DataTable 
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
            ->addColumn('image', function ($home_slider) {   
                return '<img src="'.$home_slider->image_url.'" width="200" height="100">';
            })
            ->addColumn('action', function ($home_slider) {   
                return '<a href="'.route('homepage_sliders.update',[$home_slider->id]).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;<a data-href="'.route('homepage_sliders.delete',[$home_slider->id]).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';
            })
            ->rawColumns(['image','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \HomePageSlider $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(HomePageSlider $model)
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
            ['data' => 'image', 'name' => 'image', 'title' => 'Image'],
            ['data' => 'order', 'name' => 'order', 'title' => 'Order'],            
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
        return 'homepage_slider_' . date('YmdHis');
    }
}