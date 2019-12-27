<?php

use Illuminate\Database\Seeder;

class WebhooksTableSeeder extends Seeder
{
    public function run() {
        factory(App\Models\Webhook::class, 50)->create();
    }
}
