<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Category;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        // 'name'=> $faker->randomElement(['Electronics','Fashion','Home Appliances','Jewelry','Health and Beauty','Sports and Fitness']),
    ];
});
