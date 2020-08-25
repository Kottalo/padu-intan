<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->string('order_no');
            $table->integer('project_id');
            $table->integer('supplier_id');
            $table->string('voucher_no');
            $table->string('ref_no');
            $table->integer('bank_id');
            $table->decimal('cheque');
            $table->decimal('cash');
            $table->decimal('online');
            $table->string('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
