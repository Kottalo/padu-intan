<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\{ Project, Order, Item, OrderItem, Unit, Supplier };
use Carbon\Carbon;

class OrderItemController extends Controller
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
        $date = $request->date;
        $ref_no = $request->ref_no;
        $project_id = $request->project_id;
        $supplier_id = $request->supplier_id;
        $item_name = $request->item_name;
        $unit_name = $request->unit_name;
        $price = $request->price;

        $order = Order::where([
            'date' => $date,
            'ref_no' => $ref_no,
            'project_id' => $project_id,
            'supplier_id' => $supplier_id,
        ])->first();

        if (!$order)
        {
            $order = new Order;

            $order->date = $request->date;
            $order->ref_no = $ref_no;
            $order->project_id = $project_id;
            $order->supplier_id = $supplier_id;

            $order->save();
        }

        $order_id = $order->id;

        $item = Item::whereRaw('BINARY `name` = ?', [$item_name])->first();

        if (!$item)
        {
            $item = new Item;
        }
        $item->name = $item_name;
        $item->price = $price;

        $item->save();

        $unit = Unit::whereRaw('BINARY `name` = ?', [$unit_name])->first();

        if (!$unit)
        {
            $unit = new Unit;
        }
        $unit->name = $unit_name;
        $unit->save();

        $order_item = new OrderItem;

        $order_item->order_id = $order->id;
        $order_item->item_id = $item->id;
        $order_item->return = $request->return ? 1 : 0;
        $order_item->quantity = $request->quantity;
        $order_item->unit_id = $unit ? $unit->id : null;
        $order_item->price = $price;
        $order_item->sst_perc = $request->sst_perc / 100;
        $order_item->remarks = $request->remarks ? $request->remarks : '';

        $order_item->save();

        return 1;
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
        $date = $request->date;
        $ref_no = $request->ref_no;
        $project_id = $request->project_id;
        $supplier_id = $request->supplier_id;
        $item_name = $request->item_name;
        $unit_name = $request->unit_name;
        $price = $request->price;

        $item = Item::whereRaw('BINARY `name` = ?', [$item_name])->first();

        if (!$item)
        {
            $item = new Item;
        }
        $item->name = $item_name;
        $item->price = $price;

        $item->save();

        $unit = Unit::whereRaw('BINARY `name` = ?', [$unit_name])->first();

        if (!$unit)
        {
            $unit = new Unit;
        }
        $unit->name = $unit_name;
        $unit->save();

        $order = Order::where([
            'date' => $date,
            'ref_no' => $ref_no,
            'project_id' => $project_id,
            'supplier_id' => $supplier_id,
        ])->first();

        if (!$order)
        {
            $order = new Order;

            $order->date = $request->date;
            $order->ref_no = $ref_no;
            $order->project_id = $project_id;
            $order->supplier_id = $supplier_id;

            $order->save();
        }

        $order_id = $order->id;

        $order_item = OrderItem::find($id);

        $order_item->order_id = $order_id;
        $order_item->item_id = $item->id;
        $order_item->return = $request->return ? 1 : 0;
        $order_item->quantity = $request->quantity;
        $order_item->unit_id = $unit ? $unit->id : null;
        $order_item->price = $price;
        $order_item->sst_perc = $request->sst_perc / 100;
        $order_item->remarks = $request->remarks ? $request->remarks : '';

        $order_item->save();

        return OrderItem::with(['order.supplier', 'item'])->find($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OrderItem::destroy($id);

        return 1;
    }

    public static function getItems(Request $request)
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

        $projectId_sql = $projectIds ? '(' . join(',', $projectIds) . ')' : '(0)';
        $supplierId_sql = $supplierIds ? '(' . join(',', $supplierIds) . ')' : '(0)';

        return Project::with([
            'orders' => function($query) use ($supplierIds, $date_from, $date_to)
            {
                $query->selectRaw("orders.id,date,ref_no,project_id,supplier_id,order_id,
                round2( SUM(order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) ) AS total_price,
                round2( SUM(order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END * sst_perc) ) AS sst_amount,
                round2( SUM( (order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) +
                ( order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END * sst_perc )) ) AS sub_total")
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('suppliers','orders.supplier_id','=','suppliers.id')
                ->groupBy('orders.id')
                ->whereIn('supplier_id', collect($supplierIds))
                ->whereBetween('date', [$date_from, $date_to])
                ->orderBy('orders.date')
                ->orderBy('suppliers.name')
                ->orderBy('orders.ref_no')
                ->orderBy('order_items.id');
            },
            'orders.order_items' => function($query)
            {
                DB::statement(DB::raw("
                  SET @total_price = 0.0, @sst_amount = 0.0;
                "));
                $query->selectRaw("*, round2( order_items.price ) AS price,
                cast((sst_perc * 100) AS char) * 1 AS sst_perc,
                @total_price:= round2( quantity * CASE WHEN `return` THEN -price ELSE price END ) AS total_price,
                @sst_amount:= round2( @total_price * sst_perc ) AS sst_amount,
                round2( @total_price + @sst_amount ) AS sub_total
                ");
            },
            'orders.order_items.unit',
            'orders.order_items.item',
            'orders.order_items.order.supplier',
            'orders.supplier',
            'suppliers',
        ])
        ->selectRaw("projects.id, projects.name, p1.total_price, p1.sst_amount, p1.sub_total")
        ->whereIn('projects.id', collect($projectIds))
        ->leftJoin('orders','projects.id','=','project_id')
        ->leftJoin('order_items','orders.id','=','order_id')
        ->leftJoin(
            DB::raw("(SELECT projects.id,
            round2( SUM( order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END ) ) AS total_price,
            round2( SUM( order_items.sst_perc * order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END ) ) AS sst_amount,
            round2( SUM( ( order_items.sst_perc * order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END )
            + ( order_items.quantity * CASE WHEN `return` THEN -order_items.price ELSE order_items.price END) ) ) AS sub_total
            FROM projects
            LEFT JOIN orders ON projects.id = orders.project_id
            LEFT JOIN order_items ON orders.id = order_items.order_id
            WHERE projects.id IN $projectId_sql
            AND orders.supplier_id IN $supplierId_sql
            AND orders.date BETWEEN '$date_from' AND '$date_to'
            GROUP BY projects.id)
            AS p1
            "),
            function($join)
            {
                $join->on('projects.id', '=', 'p1.id');
            }
        )
        ->groupBy('projects.id')
        ->orderBy('projects.name')
        ->get();
    }
}
