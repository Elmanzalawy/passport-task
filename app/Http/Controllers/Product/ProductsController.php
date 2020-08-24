<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\User;
use App\Product;
use App\History;
use Auth;

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
            'name' => 'required|string|unique:products|max:255',
            'price' => 'required|min:1|',
            'quantity' => 'required|min:1|',
            'category' => Rule::in(['Electronics','Fashion','Home Appliances','Jewelry','Health and Beauty','Sports and Fitness']),
            // 'type' => 'integer'
        ]);
        if($validator->fails()){
            return response (['errors'=>$validator->errors()->all()], 422);
        }
        // $request['seller_name'] = auth()->user()->name;
        // $request['seller_id'] = auth()->user()->id;
        

        // $product = Product::create($request->toArray());
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category = $request->category;
        $product->quantity = $request->quantity;
        $product->seller_id = auth()->user()->id;
        $product->seller_name = auth()->user()->name;
        $product->save();
        // $response = ['message' =>  'store function'];
        return response([__('messages.productStored'), $product], 200);

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
        if(!$product){
            return response()->json(__('messages.productNotFound'), 404);
        }

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
        $validator = Validator::make($request->all(), [
            'name' => 'string|unique:products|max:255',
            'price' => 'min:1|',
            'quantity' => 'min:1|',
            'category' => Rule::in(['Electronics','Fashion','Home Appliances','Jewelry','Health and Beauty','Sports and Fitness']),
            // 'type' => 'integer'
        ]);
        if($validator->fails()){
            return response (['errors'=>$validator->errors()->all()], 422);
        }

        $product = Product::find($id);
        if(!isset($product)){
            return response()->json(['message'=>'Error: product does not exist'], 404);
        }

        $product->name = $request->name ?? $product->name;
        $product->price = $request->price ?? $product->price;
        $product->quantity = $request->quantity ?? $product->quantity;
        $product->category = $request->category ?? $product->category;
        $product->save();

        return response()->json([__('messages.productUpdated'), 'product'=>$product], 200);

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
        $user = User::find($product->seller_id);
        if($product->seller_id != auth()->user()->id){
            return response()->json(['message'=>__('messages.unauthorized')], 403);
        }
        $product->delete();
        
        return response()->json(['product'=>$product, 'message'=>'successfully deleted [product].'], 200);    
    }


    public function purchase(Request $request, $id){
        $product = Product::find($id);
        $user = User::find(auth()->user()->id);
        if(!isset($product)){
            return response()->json(__('productNotFound'), 200);
        }
        if($product->quantity==0){
            return response()->json(__('messages.productUnavailable'), 404);
        }
        $validator = Validator::make($request->all(), [
            'quantity' => "required|integer|min:1|max:".$product->quantity,
        ]);
        if($validator->fails()){
            return response (['errors'=>$validator->errors()->all()], 422);
        }
        //subtract purchased quantity from product quantity
        $product->quantity -= $request->quantity;
        $product->save();
        $cost = ($request->quantity) * ($product->price);
        
        //add cost to user's bill
        $user->bill += $cost;
        $user->save();

        //store transaction in user History
        $history = new History;
        $history->user_id = $user->id;
        $history->product_id = $product->id;
        $history->price = $product->price;
        $history->quantity = $request->quantity;
        $history->total_cost = $cost;
        $history->save();
        return response()->json(['message'=>__('messages.productPurchased'), 'product'=>$product, 'cost'=>$cost], 200);
    }
}
