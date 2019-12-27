<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'discord_id' => $faker->numberBetween(1, 9999999),
        'name' => $faker->userName() . '#' . $faker->numberBetween(0, 9999),
        'avatar_url' => $faker->imageUrl(),
        'remember_token' => Str::random(10),
    ];
});
