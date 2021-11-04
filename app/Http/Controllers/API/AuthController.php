<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $providers = [
        'github','facebook','google','twitter'
    ];
    public function show(){
        return "Hello";
    }
    public function register(Request $request)
    {
        // return response(['user' => 'access_token' ]);

        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);
        $user = User::create($validatedData);
        $user->sendEmailVerificationNotification();
        // try{
            

        // }catch(Exception $e){
        //     return response()->json(['error'=>$e],200);
        // }
        
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
    // callback
    public function redirectToProvider($driver)
    {
        if( ! $this->isProviderAllowed($driver) ) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
            return Socialite::driver($driver)->stateless()->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }

  
    public function handleProviderCallback( $driver )
    {
        try {
            $user = Socialite::driver($driver)->stateless()->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }
        return empty( $user->email )
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    protected function sendSuccessResponse($res)
    {
        return response()->json(['user'=>$res],200);
    }

    protected function sendFailedResponse($msg = null)
    {
        return response(['msg'=>$msg],400);   
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
        // dump($providerUser);
        $user = User::where('email', $providerUser->getEmail())->first();

        // if user already found
        if( $user ) {
            // update the avatar and provider that might have changed
            $user->update([
                'avatar' => $providerUser->avatar,
                'provider' => $driver,
                'provider_id' => $providerUser->id,
                'access_token' => $providerUser->token
            ]);
        } else {
            // create a new user
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'avatar' => $providerUser->getAvatar(),
                'provider' => $driver,
                'provider_id' => $providerUser->getId(),
                'access_token' => $providerUser->token,
                // user can use reset password to create a password
                'password' => ''
            ]);
        }
        Auth::login($user, true);
        $user = Auth::user();
        $res = [];
        $res['token'] = $user->createToken('authToken')->accessToken;
        $res['name'] = $user ->name;
        return $this->sendSuccessResponse($res);
    }

    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }

}
