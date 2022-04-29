<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCMSBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_m_s_billings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('project_id');
            $table->string('billing_invoice_no', 10);
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('status', 6);
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
        Schema::drop('c_m_s_billings');
    }
}
