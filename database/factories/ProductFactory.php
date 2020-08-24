<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\User;
use Faker\Generator as Faker;
use App\Category;

$factory->define(Product::class, function (Faker $faker) {
    $seller_id = $faker->numberBetween(1,10);
    $categoriesCount = count(Category::get());
    return [
        'seller_id'=> $seller_id,
        'name'=>$faker->unique()->sentence(3),    //generate names of 3 words
        // 'description'=>$faker->text(),  //random text
        'seller_name'=> User::find($seller_id)->name,
        'quantity'=>$faker->numberBetween(1,100), //random quantity range
        'price'=>$faker->numberBetween(5,200),  //random price range
        'category'=>Category::where('id',$faker->numberBetween(1,$categoriesCount))->value('name'),  
        ////randomly select product type from array
        // 'image'=>$faker->randomElement(['1.png','2.png','3.png','4.png','5.png','6.png','7.png','8.png','9.png','10.png','11.png']),    //randomly select product image from images array
    ];
});
