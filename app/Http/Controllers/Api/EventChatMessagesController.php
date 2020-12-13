<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventChatMessage;
use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class EventChatMessagesController extends Controller
{
    public function index () {
        $messages = EventChatMessage::with("replies.user",'user')->where('message_id', null)->get();

        return response()->json([
            "data" => $messages,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function get_event_messages () {
        $event = Event::where("started", Event::EVENT_STARTED)->first();
        if ($event) {
            $messages = EventChatMessage::with('replies.user', 'user')->where(['message_id' => null , 'event_id' => $event->id])->get();

            return response()->json([
                "data" => $messages,
                "message" => "ok",
                "status" => 200
            ],200);
        } else {
            return response()->json([
                "message" => "Немає активної події",
                "status" => 422
            ],422);
        }
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'message_id' => 'nullable|exists:App\EventChatMessage,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
                "status" => 422
            ], 422);
        }

        $parent_message = EventChatMessage::where('id', $request->message_id)->first();

        if ($parent_message && $parent_message->message_id) {
            return response()->json([
                "message" => "Помилка",
                "status" => 422
            ], 422);
        }

        $user = Auth::guard()->user();
        $event = Event::where("started", Event::EVENT_STARTED)->first();

        $message = new EventChatMessage();
        $message->user_id = $user->id;
        $message->event_id = $event->id;
        $message->message_id = $request->message_id;
        $message->message = $request->message;
        $message->date = Carbon::now()->format('Y-m-d H:i:s');;
        $message->save();

        $message = EventChatMessage::with("replies",'user')->where('message_id', null)->find($message->id);

        event(new NewMessageEvent());

        return response()->json([
            "data" => $message,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function update ($id, Request $request)
    {
        $message = EventChatMessage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:App\User,id',
            'event_id' => 'exists:App\Event,id',
            'message_id' => 'exists:App\Event,id',
            'message' => 'required|string',
            'date' => "date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
                "status" => 422
            ], 422);
        }

        $message->fill($request->toArray());
        $message->save();

        return response()->json([
            "data" => $message,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function delete ($id) {
        $message = EventChatMessage::findOrFail($id);
        $message->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
