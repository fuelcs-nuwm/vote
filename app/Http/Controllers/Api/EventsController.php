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
            'embedHtml' => 'nullable|string',
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
        $event = Event::with('questions')
            ->where("started", Event::EVENT_STARTED)->first();

        return response()->json([
            "data" => $event,
            "message" => "ok",
            "status" => 200
        ],200);

        foreach ($model as $question) {
            array_push($questions , [
                'id' => $question->id,
                'title' => $question->title,
            ]);
        }

        return response()->json([
            "data" => $questions,
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
