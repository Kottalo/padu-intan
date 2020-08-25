<?php

namespace App\Observers;

use App\Models\{ OrderItem, Order };

class OrderItemObserver
{
    /**
     * Handle the order item "created" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {

    }

    /**
     * Handle the order item "updated" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the order item "deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        $this->clearOrders($orderItem->order);
    }

    /**
     * Handle the order item "restored" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the order item "force deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        //
    }

    public function clearOrders($order)
    {
        if (!$order->order_items->count())
        {
            $order->delete();
        }
    }
}
