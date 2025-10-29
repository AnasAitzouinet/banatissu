<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPiecesCountToProductWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->float('pieces_count', 10, 0)->nullable()->after('qte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_warehouse', function (Blueprint $table) {
            $table->dropColumn('pieces_count');
        });
    }
}
