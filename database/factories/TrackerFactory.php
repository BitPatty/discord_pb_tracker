<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Tracker::class, function (Faker $faker) {
    return [
        'webhook_id' => $faker->numberBetween(1, 50),
        'last_updated' => $faker->dateTime(),
        'src_user_id' => $faker->numberBetween(1, 50)
    ];
});
