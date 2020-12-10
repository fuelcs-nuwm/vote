<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class EventsController extends Controller
{
    public function index () {
        $events = Event::all();

        return response()->json([
            "data" => $events,
            "message" => "success",
            "status" => 200
        ],200);
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'started' => 'boolean',
            'finished' => 'boolean',
            'integer' => 'integer|min:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $event = new Event();
        $event->fill($request->toArray());
        $event->save();

        return response()->json([
            "data" => $event,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function show(Request $request)
    {

    }

    public function update($id, Request $request)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'started' => 'boolean',
            'finished' => 'boolean',
            'integer' => 'integer|min:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $event->fill($request->toArray());
        $event->save();

        return response()->json([
            "data" => $event,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function delete ($id) {
        $currency = Event::findOrFail($id);
        $currency->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
