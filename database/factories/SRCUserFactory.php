<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Models\SRCUser::class, function (Faker $faker) {
    return [
        'src_name' => $faker->userName(),
        'src_id' => $faker->userName(),
    ];
});
