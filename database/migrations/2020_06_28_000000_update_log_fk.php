<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLogFk extends Migration
{
  public function up()
  {
    Schema::table('t_log', function (Blueprint $table) {
      $table->dropForeign(['src_user_id']);
      $table->foreign('src_user_id')->references('id')->on('t_src_user')->onDelete('SET NULL');
      $table->dropForeign(['tracker_id']);
      $table->foreign('tracker_id')->references('id')->on('t_tracker')->onDelete('SET NULL');
    });
  }

  public function down()
  {
    Schema::table('t_log', function (Blueprint $table) {
      $table->dropForeign('src_user_id');
      $table->foreign('src_user_id')->references('id')->on('t_src_user')->onDelete('NO ACTION');
      $table->dropForeign(['tracker_id']);
      $table->foreign('tracker_id')->references('id')->on('t_tracker')->onDelete('NO ACTION');
    });
  }
}
