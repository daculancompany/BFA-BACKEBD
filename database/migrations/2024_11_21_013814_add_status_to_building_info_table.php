<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToBuildingInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_info', function (Blueprint $table) {
            // Add the status column
            $table->string('status')->default('Pending'); // Set default to 'Pending'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_info', function (Blueprint $table) {
            // Remove the status column
            $table->dropColumn('status');
        });
    }
}
