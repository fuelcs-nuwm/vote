<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AdminsController extends Controller
{
    public function index () {
        $users = User::where('role', User::ROLE_ADMIN)->get()->all();

        return response()->json([
            "data" => $users,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:App\User,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 422);
        }

        $user = new User();
        $user->email = $request->email;
        $user->role = User::ROLE_ADMIN;
        $user->save();

        return response()->json([
            "data" => $user,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function update ($id, Request $request)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:App\User,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 422);
        }

        $user->email = $request->email;
        $user->save();

        return response()->json([
            "data" => $user,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function delete ($id) {
        $current_user = Auth::guard()->user();
        $user = User::findOrFail($id);

        if ($current_user->id == $user->id) {
            return response()->json([
                "data" => [],
                "message" => "Ви не можете видалити себе!",
                "status" => 200
            ],422);
        }

        $user->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
