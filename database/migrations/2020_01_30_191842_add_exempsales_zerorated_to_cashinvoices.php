<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExempsalesZeroratedToCashinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashinvoices', function (Blueprint $table) {
            $table->decimal('exempt', 10,2)->after('credit_total');
            $table->decimal('zero_rated', 10,2)->after('exempt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashinvoices', function (Blueprint $table) {
            $table->dropColumn('exempt');
            $table->dropColumn('zero_rated');
        });
    }
}
