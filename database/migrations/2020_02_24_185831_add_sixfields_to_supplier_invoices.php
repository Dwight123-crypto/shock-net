<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSixfieldsToSupplierInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->date('release_date', 10)->after('zero_rated');
            $table->date('date_of_importation', 10)->after('release_date');
            $table->string('country_of_origin', 50)->after('date_of_importation');
            $table->decimal('dutiable_value', 10, 2)->after('country_of_origin');
            $table->decimal('all_charges_custom_custody', 10, 2)->after('dutiable_value');
            $table->date('date_vat_payment', 10)->after('all_charges_custom_custody');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropColumn('release_date');
            $table->dropColumn('date_of_importation');
            $table->dropColumn('country_of_origin');
            $table->dropColumn('dutiable_value');
            $table->dropColumn('all_charges_custom_custody');
            $table->dropColumn('date_vat_payment');
        });
    }
}
