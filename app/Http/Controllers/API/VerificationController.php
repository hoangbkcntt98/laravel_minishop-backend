<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Status\Status;
use Illuminate\Http\Request;
use App\Models\User;
use App\Response\Response;
use Exception;

class VerificationController extends Controller
{
    public function verify($user_id,Request $request){
        $user = User::findOrFail($user_id);
        $res = [];
        if(!$user->hasVerifiedEmail()){
            $user->markEmailAsVerified();
            $res = new Response('Mail verfied',null,Status::MAIL_VERIFIED);
           
        }
        $res = new Response('Send_email',null,Status::SEND_MAIL);
        return $res ->createJsonResponse();
    }
    public function resend(){
        $res = [];
        if(auth()->user()->hasVerifiedEmail()){
            $res = new Response('Email Verified',null,Status::MAIL_VERIFIED);
        }
        try{
            auth()->user()->sendEmailVerificationNotification();
        }catch(Exception $e){
            $res = new Response('Send mail Error',null,Status::CANNOT_SEND_EMAIL);
        }
        $res = new Response('Send mail success',null,Status::SEND_MAIL_SUCCESS);
        return $res ->createJsonResponse();
    }
}
