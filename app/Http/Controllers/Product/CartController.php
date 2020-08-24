<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Cart;
use App\Product;
use App\User;
use App\History;
use PDO;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get user's cart items
        $user = User::find(auth()->user()->id);
        $cartItems = User::find($user->id)->carts->toArray();
        if(!$cartItems){    //if no cart items were found
            return response()->json(['message'=>__('messages.noCartItems')], 404);
        }
        return response()->json(['cart_items'=>$cartItems], 200);

    }

    public function show($id){
        $user = User::find(auth()->user()->id);
        $cartItem = Cart::find($id);
        // $cartItem = User::find(auth()->user()->id)->carts($id);

        if(!$cartItem){ //if cart item doesnt exist
            return response()->json([__('messages.cartItemNotFound')], 404);
        }
        if($cartItem->user_id != $user->id){  //if user_id doesnt match with seller_id
            return response()->json(['message'=>__('messages.unauthorized')], 403);
        }
        return response()->json($cartItem, 200);
    }

    public function store(Request $request, $id){
        $product = Product::find($id);
        if(!$product){  //if product doesnt exist
            return response()->json(['message'=>__('messages.productNotFound')], 404);
        }
        if($product->quantity == 0){    //if product is currently unavailable
            return response()->json(['message'=>__('messages.productUnavailable')], 404);
        }
        //setting up validator rules
        $validator = Validator::make($request->all(),
        [
            "quantity"=> "required|integer|min:1|max:".$product->quantity,
        ]
        );
        if($validator->fails()){    //log validator errors if exist
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        //create new cart item
        $cartItem = new Cart;
        $cartItem->user_id = auth()->user()->id;
        $cartItem->product_id = $id;
        $cartItem->price = $product->price;
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        //subtract the chosen quantity from product quantity
        $product->quantity -= $cartItem->quantity;
        $product->save();
        return response()->json(['message'=>__('messages.cartItemAdded'), $cartItem], 200);
    }

    public function update(Request $request, $id){
        $user = User::find(auth()->user()->id);
        $cartItem = Cart::find($id);
        if(!$cartItem){ //if cart item doesnt exist
            return response()->json(__('messages.cartItemNotFound'), 404);
        }
        if($cartItem->user_id != $user->id){
            return response()->json(['message'=>__('messages.unauthorized')], 403);
        }
        $product = Product::where('id',$cartItem->product_id)->first();
        $validator = Validator::make($request->all(),
        [
            'quantity'=> "required|integer|min:1|max:".$product->quantity,
        ]
        );
        if($validator->fails()){
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        return response()->json(['message'=>__('messages.cartItemUpdated'), 'cart_item'=>$cartItem],200);
    }

    public function destroy($id){
        $user = User::find(auth()->user()->id);
        $cartItem = Cart::find($id);
        if(!$cartItem){
            return response()->json([__('messages.cartItemNotFound')], 404);
        }
        if($cartItem->user_id != $user->id){
            return response()->json(['message'=>__('messages.unauthorized')], 403);
        }
        $cartItem->delete();
        return response()->json([__('messages.cartItemDeleted'), $cartItem], 200);
        
    }

    public function emptyCart(){
        $cartItems = User::find(auth()->user()->id)->carts;
        if(!$cartItems){
            return response()->json(['message'=>__('messages.noCartItems')], 404);
        }
        foreach($cartItems as $cartItem){
            $cartItem->delete();
        }
        return response()->json(['message'=>__('messages.cartEmptySuccess')], 404);
    }


    public function buyCartItem($id){
        $user = User::find(auth()->user()->id);
        $cartItem = Cart::find($id);
        if(!$cartItem){
            return response()->json(['message'=>__('messages.cartItemNotFound')], 404);
        }
        if($cartItem->user_id != $user->id){
            return response()->json(['message'=>__('messages.unauthorized')], 403);
        }
        $product = Product::find($cartItem->product_id);
        $user->bill += $product->price * $cartItem->quantity;
        $user->save();

        //make new history entry
        $history = new History();
        $history->user_id = $user->id;
        $history->product_id = $product->id;
        $history->price = $product->price;
        $history->quantity = $cartItem->quantity;
        $history->total_cost = $cartItem->quantity * $product->price;
        $history->save();
        
        //delete cart item
        $cartItem->delete();
        return response()->json(['message'=>__('messages.cartItemPurchased')], 200);
    }


    public function buyAllCartItems(){
        $user = User::find(auth()->user()->id);
        $cartItems = $user->carts;
        $totalCost=0;
        if(!$cartItems){
            return response()->json(['message'=>__('messages.noCartItems')], 404);
        }
        foreach($cartItems as $cartItem){
            $totalCost += ($cartItem->price) * ($cartItem->quantity);
            $product = Product::find($cartItem->product_id);


            //make new history entry
            $history = new History();
            $history->user_id = $user->id;
            $history->product_id = $product->id;
            $history->price = $product->price;
            $history->quantity = $cartItem->quantity;
            $history->total_cost = $cartItem->quantity * $product->price;
            $history->save();
            
            //delete cart item
            $cartItem->delete();
        }
        $user->bill += $totalCost;
        $user->save();
        
        return response()->json(['message'=>__('messages.cartItemsPurchased')], 200);
        return;
    }



}
