<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    //used to test auth related bugs
    public function test(){
        return response()->json(['message'=>'this is test method'], 200);
    }


    //registering new users in the database
    public function register (Request $request) {
        //setting rules for user register through the validator class
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            // 'type' => 'integer'
        ]);
        if ($validator->fails()) //if invalid
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']); //encrypt password
        $request['remember_token'] = Str::random(10);   //generate random token
        // $request['type'] = $request['type'] ? $request['type']  : 0;

        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200); //return success response with access token
    }


    //login to existing user record
    public function login (Request $request) {
        //setting rules for user login through the validator class
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            //return validator errors if it fails
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('name', $request->name)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    //logout if already logged in (must use token)
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
