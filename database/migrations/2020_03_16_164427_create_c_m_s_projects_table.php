<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCMSProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_m_s_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->string('type', 6);
            $table->string('name', 255);
            $table->decimal('cost', 15, 2);
            $table->decimal('downpayment', 15, 2);
            $table->date('date');
            $table->string('status', 15);
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
        Schema::drop('c_m_s_projects');
    }
}
