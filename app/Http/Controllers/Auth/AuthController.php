<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('google')->with(['hd' => 'nuwm.edu.ua'])->stateless()->redirect();
    }

    public function handleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error logged in.',
                'status' => 'error'
            ], 401);
        }

        //only allow people with @company.com to login
        if(explode("@", $user->email)[1] !== 'nuwm.edu.ua'){
            return response()->json([
                'message' => 'Not from nuwm.edu.ua',
                'status' => 'error'
            ], 401);
        }
        // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();

        if($existingUser){
            // log them in
            return $this->login($existingUser);
        } else {
            // create a new user
            $newUser                  = new User();
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->google_id       = $user->id;
            $newUser->avatar          = $user->avatar;
            $newUser->avatar_original = $user->avatar_original;
            $newUser->save();
            return $this->login($newUser);
        }
    }

    private function login($user)
    {

        if (!$user) {
            return response()->json([
                'message' => 'Invalid email or password.',
                'status' => 'error'
            ], 401);
        }

        if ($token = JWTAuth::fromUser($user)) {
            return response()->json([
                'message' => 'Successfully logged in.',
                'status' => 'success'
            ], 200)->header('Authorization', $token);
        }

        return response()->json([
            'message' => 'Error logged in.',
            'status' => 'error'
        ], 401);
    }

    public function logout()
    {
        auth("api")->logout(true);

        return response()->json([
            'message' => 'Successfully logged out.',
            'status' => 'success'
        ], 200);
    }

    public function me()
    {
        $user = Auth::guard()->user();

        //$user->user_roles = $user->getRoleNames();

        return response()->json([
            'status' => 'success',
            "data" => $user
        ],200);
    }

    public function refresh()
    {
        try {
            if ($token = auth('api')->refresh()) {
                return response()->json([
                    'message' => 'Successfully refresh token.',
                    'status' => 'success'
                ], 200)
                    ->header('Authorization', $token);
            }
            return response()->json([
                'message' => 'Error refresh token.',
                'status' => 'error'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error refresh token.',
                'status' => 'error'
            ], 401);
        }
    }
}
