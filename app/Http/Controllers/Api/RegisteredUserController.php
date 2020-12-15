<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use App\RegisteredUser;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function get_event_users () {
        $event = Event::with('groups')->where("started", Event::EVENT_STARTED)->first();
            if ($event) {
                $users = RegisteredUser::with('user.groups')
                    ->where("event_id", $event->id)
                    ->get()
                    ->all();

                $group_count = [];

                foreach ($event->groups as $index=>$group) {
                    $count = RegisteredUser::with('user.groups')
                        ->whereHas('user.groups', function ($query) use ($group){
                            return $query->where('name',  $group->name);
                        })
                        ->where("event_id", $event->id)
                        ->count();

                    array_push ($group_count , [
                        'name' => $group->name,
                        'count' => $count,
                    ]);
                }

                return response()->json([
                    "data" => [
                        'users' => $users,
                        'groups' => $group_count,
                        ],
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
