<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use App\RegisteredUser;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function get_event_users () {
        $event = Event::where("started", Event::EVENT_STARTED)->first();
            if ($event) {
                $users = RegisteredUser::with('user')->get()->all();

                return response()->json([
                    "data" => $users,
                    "message" => "ok",
                    "status" => 200
                ],200);
            } else {
                return response()->json([
                    "message" => "Немає активної події",
                    "status" => 422
                ], 422);
            }
    }
}
