<?php

namespace App\Http\Controllers\Api;

use App\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class GroupController extends Controller
{
    public function index () {
        $groups = Group::all();

        return response()->json([
            "data" => $groups,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function get_event_group ($id) {
        $groups = Group::where('event_id', $id)->get()->all();

        return response()->json([
            "data" => $groups,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'event_id' => 'exists:App\Event,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $group = new Group();
        $group->fill($request->toArray());
        $group->save();

        return response()->json([
            "data" => $group,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function update ($id, Request $request)
    {
        $group = Group::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'event_id' => 'exists:App\Event,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $group->fill($request->toArray());
        $group->save();

        return response()->json([
            "data" => $group,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function delete ($id) {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
