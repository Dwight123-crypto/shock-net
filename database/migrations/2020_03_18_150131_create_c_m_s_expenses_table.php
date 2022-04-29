<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCMSExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_m_s_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->integer('project_id');
            $table->string('invoice_no', 10);
            $table->string('invoice_remarks', 6);
            $table->string('terms', 20);
            $table->string('period', 20);
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('expenses_type', 20);
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
        Schema::drop('c_m_s_expenses');
    }
}
