<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('t_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('discord_id')->unique();
            $table->string('api_token')->unique()->nullable()->default(null);
            $table->string('name');
            $table->string('avatar_url');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_user');
    }
}
