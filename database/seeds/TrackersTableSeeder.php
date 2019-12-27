<?php

use Illuminate\Database\Seeder;

class TrackersTableSeeder extends Seeder
{
    public function run() {
        factory(App\Models\Tracker::class, 500)->create();
    }
}
