<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(SRCUsersTableSeeder::class);
        $this->call(WebhooksTableSeeder::class);
        $this->call(TrackersTableSeeder::class);
    }
}
