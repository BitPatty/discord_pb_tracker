<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(App\Models\Webhook::class, function (Faker $faker) {
    return [
        'discord_id' => $faker->numberBetween(1, 9999999),
        'manager_id' => $faker->numberBetween(1, 50),
        'channel_id' => $faker->numberBetween(1, 9999999),
        'guild_id' => $faker->numberBetween(1, 9999999),
        'state' => 'CREATED',
        'url' => $faker->url()
    ];
});
