<?php

/**
 * Wishlist DataTable
 *
 * @package     Makent
 * @subpackage  DataTable
 * @category    Wishlist
 * @author      Trioangle Product Team
 * @version     2.0
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Wishlists;
use Auth;


class WishlistDataTable extends DataTable
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
            ->addColumn('pick', function ($wishlists) {
                $class = ($wishlists->pick == 'No') ? 'danger' : 'success';
                $pick = '<a href="'.url(ADMIN_URL.'/pick_wishlist/'.$wishlists->id).'" class="btn btn-xs btn-'.$class.'">'.$wishlists->pick.'</a>';
                return $pick;
            })
           ->rawColumns(['pick']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Wishlists $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Wishlists $model)
    {
        return $model->join('users', function($join) {
                                $join->on('users.id', '=', 'wishlists.user_id');
                            })->select(['wishlists.id','wishlists.user_id','wishlists.name','wishlists.pick','users.first_name']);
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
            ['data' => 'id', 'name' => 'wishlists.id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'User Name'],
            ['data' => 'name', 'name' => 'wishlists.name', 'title' => 'Wish List Name'],
            ['data' => 'all_space_count', 'name' => 'all_space_count', 'title' => 'Lists Count','searchable' => false,'orderable' => false],
            ['data' => 'pick', 'name' => 'pick', 'title' => 'Pick'],

        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'wishlists_' . date('YmdHis');
    }
}