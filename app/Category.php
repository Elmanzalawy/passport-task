<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    
    public function products(){
        $this->hasMany('App\Product','product_id');
    }
}
