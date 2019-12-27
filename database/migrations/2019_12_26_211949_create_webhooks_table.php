<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    public function up()
    {
        Schema::create('t_webhook', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('discord_id')->unique();
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('guild_id');
            $table->string('channel_name')->nullable();
            $table->string('guild_name')->nullable();
            $table->enum('state', ['CREATED','ACTIVE', 'DEAD', 'INVALIDATED'])->default('CREATED');
            $table->timestamps();

            $table->foreign('manager_id')->references('id')->on('t_user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_webhook');
    }
}
