<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }
        $user = Auth::user();
        $res = [];
        $res['token'] = $user->createToken('authToken')->accessToken;
        $res['name'] = $user ->name;
        // return "helo";
        // $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json($res,200);
    }
    public function logout(Request $request)
    {
        
        $token = $request->user()->token();
        $token->revoke();
        


        return response(['success'=>$request]);


    }
}
