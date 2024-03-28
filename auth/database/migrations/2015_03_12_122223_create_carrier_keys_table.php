<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierKeysTable extends Migration
{
    public function up()
    {
        Schema::create(
            'carrier_keys',
            static function (Blueprint $table) {
                $table->unsignedInteger('carrier_id')->unique();
                $table->string('key');
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::drop('carrier_keys');
    }
}
