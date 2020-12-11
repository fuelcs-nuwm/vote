<?php

namespace App\Http\Controllers\Auth;

use App\Event;
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
                'message' => 'Помилка авторизації. Спробуйте ще раз пізніше.',
                'status' => 'error'
            ], 401);
        }

        //only allow people with @company.com to login
        if(explode("@", $user->email)[1] !== 'nuwm.edu.ua'){
            return response()->json([
                'message' => 'Ви не з nuwm.edu.ua',
                'status' => 'error'
            ], 401);
        }
        // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();

        if($existingUser){
            // log them in
            $existingUser->name            = $user->name;
            $existingUser->avatar          = $user->avatar;
            $existingUser->avatar_original = $user->avatar_original;
            $existingUser->save();

            if ($existingUser->role === User::ROLE_ADMIN) {
                return $this->login($existingUser);
            } else {
                $event = Event::where("started", Event::EVENT_STARTED)->first();

                if (!$event) {
                    return response()->json([
                        'message' => 'Немає активної події',
                        'status' => 'error'
                    ], 401);
                }

                $event = Event::with('customers')
                    ->whereHas('customers', function ($query) use ($user){
                        return $query->where('email',  $user->email);
                    })
                    ->where("started", Event::EVENT_STARTED)->first();

                if ($event) {
                    return $this->login($existingUser);
                } else {
                    return response()->json([
                        'message' => 'Ви не є учасником події',
                        'status' => 'error'
                    ], 401);
                }
            }

        } else {
            // create a new user
            $newUser                  = new User();
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->avatar          = $user->avatar;
            $newUser->avatar_original = $user->avatar_original;
            $newUser->save();

            $event = Event::where("started", Event::EVENT_STARTED)->first();

            if (!$event) {
                return response()->json([
                    'message' => 'Немає активної події',
                    'status' => 'error'
                ], 401);
            }

            $event = Event::with('customers')
                ->whereHas('customers', function ($query) use ($user){
                    return $query->where('email',  $user->email);
                })
                ->where("started", Event::EVENT_STARTED)->first();

            if ($event) {
                return $this->login($newUser);
            } else {
                return response()->json([
                    'message' => 'Ви не є учасником події',
                    'status' => 'error'
                ], 401);
            }
        }
    }

    private function login($user)
    {

        if (!$user) {
            return response()->json([
                'message' => 'Помилка авторизації. Спробуйте ще раз пізніше.',
                'status' => 'error'
            ], 401);
        }

        if ($token = auth('api')->claims(['user_role' => "admin"])->fromUser($user)) {
            return response()->json([
                'message' => 'Successfully logged in.',
                'status' => 'success'
            ], 200)->header('Authorization', $token);
        }

        return response()->json([
            'message' => 'Помилка авторизації. Спробуйте ще раз пізніше.',
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

        $user->user_role = "admin";

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
