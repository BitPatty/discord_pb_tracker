<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIsGlobalAdminToUsers extends Migration
{
    public function up()
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->boolean('is_global_admin')->default(false);
        });
    }

    public function down()
    {
        Schema::table('t_user', function (Blueprint $table) {
            $table->dropColumn('is_global_admin');
        });
    }
}
