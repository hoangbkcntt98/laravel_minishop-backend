<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Response\Response;
use App\Status\Status;
use Exception;

class AdminController extends Controller
{
    public function adminLogin(Request $request)
	{
		$request->validate([
            'email' => 'required|email',
    		'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);
        $res = [];
		if (Auth::attempt($credentials)) {
			
			$user = Auth::user();
			$success['token'] = $user->createToken('AdminAuthToken', ['*'])->accessToken;
            $res = new Response('Admin Login Success',$user,Status::LOGIN_SUCCESS);
            
			
		}
		else {
			$res = new Response('Admin login faile',null,Status::LOGIN_FAILE);
		}
        return $res ->createJsonResponse();
	}
	
	public function adminRegister(Request $request)
	{
        $res = [];
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $success['name'] = $user->name;
            $success['token'] = $user->createToken('AdminAuthToken', ['*'])->accessToken;
            $res = new Response('Admin Register Success',$success,Status::REGISTER_SUCCESSFULLY);
        }catch(Exception $e){
            $res = new Response('Admin Register Faile',$e->getMessage(),Status::REGISTER_FAILE);
        }
		
        return $res -> createJsonResponse();
	}
}
