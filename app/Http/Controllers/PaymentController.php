<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{ Payment, Order, Bank };

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $payment = Payment::find($id);

        $payment->voucher_no = $request->voucher_no ? $request->voucher_no : '';
        $payment->ref_no = $request->ref_no ? $request->ref_no : '';
        $payment->cheque = $request->cheque ? $request->cheque : 0;
        $payment->cash = $request->cash ? $request->cash : 0;
        $payment->online = $request->online ? $request->online : 0;
        $payment->remarks = $request->remarks ? $request->remarks : '';

        $bank = Bank::whereName($request->bank_name)->first();

        if (!$bank)
        {
            $bank = new Bank;

            $bank->name = $request->bank_name ? $request->bank_name : '';
        }

        $bank->save();

        $payment->bank_id = $bank->id;

        $payment->save();

        return 1;
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

    public static function createPayments()
    {
        foreach (Order::get() as $order)
        {
            $p = new Payment;

            $p->order_id = $order->id;
            $p->voucher_no = '';
            $p->ref_no = '';
            $p->cheque = 0;
            $p->cash = 0;
            $p->online = 0;
            $p->remarks = '';

            $p->save();
        }
    }
}
