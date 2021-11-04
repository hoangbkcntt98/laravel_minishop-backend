<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use App\Response\Response;
use App\Status\Status;

class AuthController extends Controller
{
    protected $providers = [
        'github', 'facebook', 'google', 'twitter'
    ];
    // public function show()
    // {
    //     return "Hello";
    // }
    public function register(Request $request)
    {
        // return Status::REGISTER_SUCCESSFULLY;
        $res = [];
        $validatedData['password'] = bcrypt($request->password);
        
        try{
            $validatedData = $request->validate([
                'name' => 'required|max:55',
                'email' => 'email|required',
                'password' => 'required|confirmed',
                'phone' => 'max:13',
            ]);
            $user = User::where([['email','=',$request->email]])->first();
            if($user){
                $res = new Response('Duplicated Email',$user,Status::REGISTER_FAILE);
                return $res -> createJsonResponse();
            }
            $user = User::create($validatedData);
        }catch(Exception $e){
            $res = new Response('Cannot regsiter',$e->getMessage(),Status::REGISTER_FAILE);
            return $res -> createJsonResponse();
        }   
       
        try {

            $user->sendEmailVerificationNotification();
        } catch (Exception $e) {
            $user = User::where([['email','=',$request->email]])->first();
            // $user = User::find($user->id);
            $user->delete();
            $res =  new Response('Cannot Send Email', ['errors' => $e->getMessage()], Status::CANNOT_SEND_EMAIL);
            return $res->createJsonResponse();
        }

        $accessToken = $user->createToken('authToken');
        $res =  new Response('Register Successfully', ['user' => $user, 'token' => $accessToken], Status::REGISTER_SUCCESSFULLY);
        return $res->createJsonResponse();
    }

    public function login(Request $request)
    {
        $res = [];
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            $res = new Response('Sai ten dang nhap hoac mat khau', null, Status::LOGIN_FAILE);
            // return response(['message' => 'Invalid Credentials']);
            return $res->createJsonResponse();
        }
        $user = Auth::user();
        $data = [];
        $data['token'] = $user->createToken('authToken')->accessToken;
        $data['user'] = $user;
        // return "helo";
        $res = new Response('Login success', $data, Status::LOGIN_SUCCESS);
        // $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return $res->createJsonResponse();
    }
    public function logout(Request $request)
    {

        $token = $request->user()->token();
        $token->revoke();
        $res = new Response('Logout', null, Status::LOGOUT_SUCCESS);
        return $res->createJsonResponse();
    }
    // callback
    public function redirectToProvider($driver)
    {
        if (!$this->isProviderAllowed($driver)) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
            return Socialite::driver($driver)->stateless()->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }


    public function handleProviderCallback($driver)
    {
        try {
            $user = Socialite::driver($driver)->stateless()->user();
        } catch (Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }
        return empty($user->email)
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    protected function sendSuccessResponse($data)
    {
        $response = [];
        $response = new Response('Login with driver', $data, Status::LOGIN_SUCCESS);
        return $response->createJsonResponse();
    }

    protected function sendFailedResponse($msg = null)
    {
        $response = [];
        $response = new Response($msg, null, Status::LOGIN_FAILE);
        return $response->createJsonResponse();
        // return response(['msg' => $msg], 400);
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
        $user = User::where('email', $providerUser->getEmail())->first();

        // if user already found
        if ($user) {
            // update the avatar and provider that might have changed
            $user->update([
                'avatar' => $providerUser->avatar,
                'provider' => $driver,
                'provider_id' => $providerUser->id,
                'access_token' => $providerUser->token

            ]);
            $user->markEmailAsVerified();
        } else {
            // check if account has email 
            if ($providerUser->getEmail()) {
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
                $user->markEmailAsVerified();
            }else{
                $res= new Response('Not have email',null,Status::REGISTER_FAILE);
                return $res ->createJsonResponse();
            }
        }
        Auth::login($user, true);
        $user = Auth::user();
        $res = [];
        $res['token'] = $user->createToken('authToken')->accessToken;
        $res['name'] = $user->name;
        return $this->sendSuccessResponse($res);
    }

    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }
}
