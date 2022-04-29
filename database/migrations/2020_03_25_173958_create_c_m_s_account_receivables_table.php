<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCMSAccountReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_m_s_account_receivables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('project_id');
            $table->integer('billing_id');
            $table->string('or_number', 10);
            $table->decimal('amount', 15,2);
            $table->date('date');
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
        Schema::drop('c_m_s_account_receivables');
    }
}
