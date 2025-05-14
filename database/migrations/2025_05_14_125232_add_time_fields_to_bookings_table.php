<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->time('from_time')->nullable();
        $table->time('to_time')->nullable();
    });
}

public function down()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn(['from_time', 'to_time']);
    });
}
};
