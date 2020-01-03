<?php

use Illuminate\Database\Seeder;

class SRCUsersTableSeeder extends Seeder
{
    public function run() {
        factory(App\Models\SRCUser::class, 50)->create();
    }
}
