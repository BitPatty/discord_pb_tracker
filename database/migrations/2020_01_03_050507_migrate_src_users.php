<?php

use App\Models\SRCUser;
use App\Models\Tracker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateSRCUsers extends Migration
{
    public function up()
    {
        Schema::create('t_src_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('src_name')->unique();
            $table->string('src_id')->unique();
            $table->timestamps();
        });

        Schema::table('t_tracker', function (Blueprint $table) {
            $table->unsignedBigInteger('src_user_id')->nullable();
            $table->foreign('src_user_id')->references('id')->on('t_src_user');
        });

        $trackers = Tracker::all();

        foreach ($trackers as $tracker) {
            $u = SRCUser::where(['src_id' => $tracker->src_id])->first();

            if (isset($u)) {
                $tracker->src_user_id = $u->id;
            } else {
                $u = new SRCUser();
                $u->src_id = $tracker->src_id;
                $u->src_name = $tracker->src_name;
                $u->save();
                $tracker->src_user_id = $u->id;
                $tracker->save();
            }
        }

        Schema::table('t_tracker', function (Blueprint $table) {
            $table->unsignedBigInteger('src_user_id')->nullable(false)->change();
        });

        Schema::table('t_tracker', function (Blueprint $table) {
            $table->dropColumn('src_name');
            $table->dropColumn('src_id');
        });
    }

    public function down()
    {

    }
}
