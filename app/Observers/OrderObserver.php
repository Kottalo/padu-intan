<?php

namespace App\Observers;

use App\Models\{ Order, Payment };

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
        $payment = new Payment;

        $payment->order_id = $order->id;
        $payment->voucher_no = '';
        $payment->ref_no = '';
        $payment->cheque = 0;
        $payment->cash = 0;
        $payment->remarks = '';

        $payment->save();
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
        //
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
}
