<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Events\ChangedActiveEvent;
use App\Events\LogoutEvent;
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
            'embedHtml' => 'nullable|string',
            'started' => 'boolean',
            'finished' => 'boolean',
            'vote_time' => 'integer',
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

    public function show($id, Request $request)
    {
        $event = Event::findOrFail($id);

        return response()->json([
            "data" => $event,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function get_active_event_questions () {
        $event = Event::with('questions.votes')
            ->where("started", Event::EVENT_STARTED)->first();

        return response()->json([
            "data" => $event,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function update($id, Request $request)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'embedHtml' => 'nullable|string',
            'started' => 'boolean',
            'finished' => 'boolean',
            'vote_time' => 'integer',
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

        if ($event->finished) {
            event(new LogoutEvent());
        }

        event(new ChangedActiveEvent());

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
