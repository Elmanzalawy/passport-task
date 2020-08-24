<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Cart;
use App\History;
use App\Product;
use Auth;
class HistoryController extends Controller
{
    public function index(){
        $user = User::find(auth()->user()->id);
        // $historyItems = History::where('user_id',$user->id)->get();
        $historyItems = User::find($user->id)->histories;

        if(!$historyItems){
            return response()->json(['message'=>__('messages.historyEmpty')], 404);
        }
        return response()->json(['purchase_history'=>$historyItems], 200);

    }
}
