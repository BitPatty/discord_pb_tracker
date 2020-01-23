<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    public function up()
    {
        Schema::create('t_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('type',
                ['WEB_REQUEST'
                    , 'USER_LOGON'
                    , 'USER_LOGOFF'
                    , 'USER_REGISTERED'
                    , 'USER_UPDATED'
                    , 'WEBHOOK_CREATED'
                    , 'WEBHOOK_UPDATED'
                    , 'WEBHOOK_INVALIDATED'
                    , 'TRACKER_CREATED'
                    , 'TRACKER_UPDATED'
                    , 'TRACKER_DELETED'
                    , 'PB_UPDATED'
                    , 'PB_POSTED'
                    , 'PROCESS_START'
                    , 'PROCESS_END'
                    , 'ERROR'
                ]);

            $table->enum('process_type', ['WEB', 'PB_UPDATE', 'WEBHOOK_UPDATE'])->nullable();
            $table->string('process_uuid')->nullable();

            $table->text('message')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('user_name')->nullable();

            $table->unsignedBigInteger('tracker_id')->nullable();
            $table->string('tracker_last_updated')->nullable();

            $table->unsignedBigInteger('src_user_id')->nullable();
            $table->string('src_user_src_id')->nullable();
            $table->string('src_user_src_name')->nullable();

            $table->unsignedBigInteger('webhook_id')->nullable();
            $table->unsignedBigInteger('webhook_channel_id')->nullable();
            $table->unsignedBigInteger('webhook_avatar_url')->nullable();
            $table->unsignedBigInteger('webhook_discord_id')->nullable();
            $table->string('webhook_channel_name')->nullable();
            $table->string('webhook_guild_name')->nullable();
            $table->enum('webhook_state', ['CREATED', 'ACTIVE', 'DEAD', 'INVALIDATED'])->nullable();
            $table->string('webhook_name')->nullable();

            $table->foreign('user_id')->references('id')->on('t_user')->onDelete('no action');
            $table->foreign('tracker_id')->references('id')->on('t_tracker')->onDelete('no action');
            $table->foreign('src_user_id')->references('id')->on('t_src_user')->onDelete('no action');
            $table->foreign('webhook_id')->references('id')->on('t_webhook')->onDelete('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_log');
    }
}
