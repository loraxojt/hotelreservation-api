<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Exceptions\Handler;

class AuthController extends Controller
{
    public function register(Request $request){
        try {
            $request->validate([
                'first_name'=>'required|string',
                'last_name'=>'required|string',
                'gender'=>'string',
                'address'=>'string',
                'city'=>'string',
                'province'=>'string',
                'country'=>'string',
                'postal_code'=>'integer',
                'birthday'=>'date',
                'email'=>'required|string|email|unique:users',
                'password'=>'required|string|confirmed|min:6'
            ]);
            $user = new User([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
    
            $user->save();
            return response()->json(['message' => 'Successfully Registered'], 200);

        } catch (\Exception $e) {
             return response()->json(['message' => $e->getMessage()],406);
        }
        

    }

    public function login(Request $request) {
        try {
            $request->validate([
                'email'=>'required|string',
                'password'=>'required|string',
                'remember_me'=>'boolean'
            ]);

            $credentials = $request->only(['email','password']);

            if(!Auth::attempt($credentials)){
                    return response()->json(['message'=>'Unauthorized User'],401);
            }
            $user = $request->user();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return response()->json([
                'access_token'=>$tokenResult->accessToken,
                'token_type'=>'Bearer',
                'expires_at'=>Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]);


        } catch (\Exception $e){
            return response()->json(['message'=>$e->getMessage()],406);
        }
        
    }

    public function logout(Request $request){
        try {
            $request->user()->token()->revoke();
            return response()->json(['message'=>'Successfully log out']);
        } catch (\Exception $e){
            return response()->json(['error'=>$e->getmessage()],404);
        }

    }

    public function user(Request $request){
        try {

            return response()->json($request->user());
        } catch (\Exception $e) {
            return response()->json(['status'=>'error','message'=>$e->getMessage()],402);

        }
    }
}
