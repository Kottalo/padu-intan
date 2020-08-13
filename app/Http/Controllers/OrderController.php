<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use DB;

use App\Models\{ Order, Supplier };

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getItems(Request $request)
    {
        $allSupplierIds = [];

        foreach (Supplier::get() as $supplier) {
            array_push($allSupplierIds, $supplier->id);
        }

        $earliestDate = Order::get() ? Order::orderBy('date')->first()->date : Carbon::today()->format('Y-m-d');
        $lastDate = Order::get() ? Order::orderBy('date')->get()->last()->date : Carbon::today()->format('Y-m-d');

        $projectIds = $request->projectIds;
        $supplierIds = $request->supplierIds ? $request->supplierIds : $allSupplierIds;
        $date_from = $request->date_from ? $request->date_from : $earliestDate;
        $date_to = $request->date_to ? $request->date_to : $lastDate;

        $supplierId_sql = $supplierIds ? '(' . join(',', $supplierIds) . ')' : '(0)';
        $projectId_sql = $projectIds ? '(' . join(',', $projectIds) . ')' : '(0)';

        return Supplier::with([
          'orders' => function($query) use ($projectIds, $supplierIds, $date_from, $date_to)
          {
              $query->selectRaw("orders.id,date,ref_no,project_id,supplier_id,order_id,
              round2( SUM(order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) ) AS total_price,
              round2( SUM(order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END * sst_perc) ) AS sst_amount,
              round2( SUM( (order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) +
              ( order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END * sst_perc )) ) AS sub_total")
              ->join('order_items','orders.id','=','order_items.order_id')
              ->join('suppliers','orders.supplier_id','=','suppliers.id')
              ->join('projects','orders.project_id','=','projects.id')
              ->groupBy('orders.id')
              ->whereIn('project_id', collect($projectIds))
              ->whereIn('supplier_id', collect($supplierIds))
              ->whereBetween('date', [$date_from, $date_to])
              ->orderBy('orders.date')
              ->orderBy('suppliers.name')
              ->orderBy('projects.name')
              ->orderBy('orders.ref_no')
              ->orderBy('order_items.id');
          },
          'orders.payment' => function($query)
          {

          },
          'orders.project' => function($query)
          {

          },
          'orders.supplier' => function($query)
          {

          },
        ])
        ->selectRaw('suppliers.id, s1.order_total, round2(cheque + cash + online) AS payment_total')
        ->leftJoin('orders', 'suppliers.id', '=', 'orders.supplier_id')
        ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
        ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
        ->leftJoin('projects', 'orders.project_id', '=', 'projects.id')
        ->leftJoin(
            DB::raw("(SELECT supplier_id AS id,
            round2( SUM( (order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) +
            ( order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END * sst_perc )) ) AS order_total
            FROM orders
            LEFT JOIN order_items ON orders.id = order_items.order_id
            WHERE orders.supplier_id IN $supplierId_sql
            AND orders.project_id IN $projectId_sql
            AND orders.date BETWEEN '$date_from' AND '$date_to'
            GROUP BY supplier_id)
            AS s1
            "),
            function($join)
            {
                $join->on('suppliers.id', '=', 's1.id');
            }
        )
        ->groupBy('suppliers.id')
        ->get();
    }

    public function getOrderBySupplierId_Date(Request $request)
    {
        return Order::where([
          'supplier_id' => $request->supplier_id,
          'date' => $request->date,
        ])->distinct()->get();
    }
}
