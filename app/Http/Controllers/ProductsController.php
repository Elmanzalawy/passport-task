<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\User;
use App\Http\Controllers\Controller;
use App\Product;
class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::get();
        // $response = ['message' =>  'index function'];
        // return response($response, 200);    }
        return response()->json($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|min:1|',
            'quantity' => 'required|min:1|',
            // 'type' => 'integer'
        ]);
        if($validator->fails()){
            return response (['errors'=>$validator->errors()->all()], 422);
        }
        $product = Product::create($request->toArray());
        // $response = ['message' =>  'store function'];
        return response($product, 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        return response(['product'=>$product], 200);  
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(!isset($product)){
            return response()->json(['message'=>'Error: product does not exist'], 404);
        }
        $product = Product::find($id)->update($request->all());
        return response()->json(['message'=>'Product updated.', 'product'=>$product], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(!isset($product)){
            return response()->json(['message'=>'Record does not exist.'], 404);
        }
        $product->delete();
        
        return response()->json(['product'=>$product, 'message'=>'successfully deleted [product].'], 200);    
    }
}
