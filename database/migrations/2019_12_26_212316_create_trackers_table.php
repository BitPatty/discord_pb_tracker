<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackersTable extends Migration
{
    public function up()
    {
        Schema::create('t_tracker', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('src_name');
            $table->string('src_id');
            $table->unsignedBigInteger('webhook_id');
            $table->dateTime('last_updated');
            $table->timestamps();

            $table->foreign('webhook_id')->references('id')->on('t_webhook');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_tracker');
    }
}
