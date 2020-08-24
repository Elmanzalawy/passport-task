<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Category;
// use DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('categories')->insert(
            array(
                array(
                    'name'=>'Electronics',
                    'user_id'=>rand(1,10),
                ),
                array(
                    'name'=>'Fashion',
                    'user_id'=>rand(1,10),

                ),
                array(
                    'name'=>'Home Appliances',
                    'user_id'=>rand(1,10),
                ),
                array(
                    'name'=>'Jewelry',
                    'user_id'=>rand(1,10),
                ),
                array(
                    'name'=>'Health and Beauty',
                    'user_id'=>rand(1,10),

                ),
                array(
                    'name'=>'Sports and Fitness',
                    'user_id'=>rand(1,10),
                ),
            )
         );
    }
}
