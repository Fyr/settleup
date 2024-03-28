<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // change the column size to support new key format
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->mediumText('token')->change();
            $table->mediumText('secret')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tokens', function (Blueprint $table) {
            $table->string('token')->change();
            $table->string('secret')->change();
        });
    }
};
