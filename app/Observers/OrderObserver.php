<?php

namespace App\Observers;

use App\Models\{ Order, Payment };
use Carbon\Carbon;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $this->createPayments($order);
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $this->clearPayments($order);
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }

    public function createPayments($order)
    {
        $year = (new Carbon($order->date))->year;
        $month = (new Carbon($order->date))->month;

        $p = Payment::where([
            'year' => $year,
            'month' => $month,
            'project_id' => $order->project_id,
            'supplier_id' => $order->supplier_id,
        ])->first();

        if ($p)
        {
            return;
        }

        $p = new Payment;

        $p->year = $year;
        $p->month = $month;
        $p->project_id = $order->project_id;
        $p->supplier_id = $order->supplier_id;
        $p->voucher_no = '';
        $p->order_no = '';
        $p->ref_no = '';
        $p->cheque = 0;
        $p->cash = 0;
        $p->online = 0;
        $p->remarks = '';

        $p->save();
    }

    public function clearPayments($order)
    {
        $year = (new Carbon($order->date))->year;
        $month = (new Carbon($order->date))->month;

        $orders = Order::where([
          'project_id' => $order->project_id,
          'supplier_id' => $order->supplier_id,
        ])
        ->whereRaw('YEAR(date) = ?', [$year])
        ->whereRaw('MONTH(date) = ?', [$month])
        ->get();

        if (!$orders->count())
        {
            Payment::where([
              'project_id' => $order->project_id,
              'supplier_id' => $order->supplier_id,
              'year' => $year,
              'month' => $month,
            ])
            ->delete();
        }
    }
}
