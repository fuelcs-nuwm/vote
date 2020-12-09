<?php

namespace App\Http\Middleware;

use Exception;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JWTmiddleware extends BaseMiddleware {

    public function handle($request, \Closure $next) {

        try {

            if (!$this->auth->parseToken()->authenticate()) {
                return response()->json([
                    'error' => 'Unauthorized.'
                ], 401);
            }

            return $next($request);

        } catch (TokenExpiredException $t) {

            try {

                return response()->json([
                    'error' => 'Token expired.'
                ], 401);

            } catch (TokenBlacklistedException $e) {

                return response()->json([
                    'error' => 'Token blacklisted'
                ], 401);

            } catch (Exception $e) {

                return response()->json([
                    'error' => 'Something bad happened.'
                ], 409);
            }

        } catch (JWTException $e) {

            return response()->json([
                'error' => 'Unauthorized.'
            ], 401);

        } catch (Exception $e) {

            return response()->json([
                'error' => 'Something bad happened.'
            ], 409);

        }
    }




    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $this->guard()->factory()->getTTL() * 60
            ],
            'status' => 200
        ], 200);
    }

    public function guard()
    {
        return Auth::guard();
    }

}
