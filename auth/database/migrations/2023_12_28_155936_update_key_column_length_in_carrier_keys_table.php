<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // delete old keys
        DB::table('carrier_keys')->delete();

        // change the column size to support new key format
        Schema::table('carrier_keys', function (Blueprint $table) {
            $table->mediumText('key')->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrier_keys', function (Blueprint $table) {
            $table->string('key')->change();
        });
    }
};
