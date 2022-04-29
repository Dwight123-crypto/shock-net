<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCMSAdditionalWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_m_s_additional_works', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->integer('customer_id');
            $table->date('date');
            $table->decimal('amount', 15,2);
            $table->string('description');
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
        Schema::drop('c_m_s_additional_works');
    }
}
