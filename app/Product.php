<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'quantity', 'price',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'seller_id');
    }
    public function category(){
        return $this->belongsTo('App\Category','product_id');
    }
    public function history(){
        return $this->hasMany('App\History');
    }
}
