<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class VerificationController extends Controller
{
    public function verify($user_id,Request $request){
        $user = User::findOrFail($user_id);
        if(!$user->hasVerifiedEmail()){
            $user->markEmailAsVerified();
            return response()->json(['msg'=>'mail verified'],200);
        }
        return response()->json(['msg'=>'send_email'],200);
    }
    public function resend(){
        if(auth()->user()->hasVerifiedEmail()){
            return response()->json(['msg'=>'email sended'],200);
        }
        try{
            auth()->user()->sendEmailVerificationNotification();
        }catch(Exception $e){
            return response(['msg'=>'error'],400);
        }
        return response()->json(['msg'=>'send_email'],200);
    }
}
