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
                    'name'=>'Electronics'
                ),
                array(
                    'name'=>'Fashion'
                ),
                array(
                    'name'=>'Home Appliances'
                ),
                array(
                    'name'=>'Jewelry'
                ),
                array(
                    'name'=>'Health and Beauty'
                ),
                array(
                    'name'=>'Sports and Fitness'
                ),
            )
         );
    }
}
