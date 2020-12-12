<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Events\NewVoteEvent;
use App\Events\VoteEndedEvent;
use App\Http\Controllers\Controller;
use App\Question;
use App\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Spatie\Async\Pool;

class VoteController extends Controller
{
    public function startNewVote (Request $request) {

        $validator = Validator::make($request->all(), [
            'question_id' => 'exists:App\Question,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
                "status" => 422
            ], 422);
        }

        $is_active_vote = Vote::where('is_active', true)->first();

        if ($is_active_vote) {
            return response()->json([
                "message" => "Вже є активне голосування!",
                "status" => 422
            ], 422);
        } else {
            $question_id = $request->question_id;

            $vote = new Vote();
            $vote->is_active = true;
            $vote->question_id = $question_id;
            $vote->started_at = Carbon::now()->format('Y-m-d H:i:s');
            $vote->save();

            $vote = Vote::with('question')->find($vote->id);

            $event = Event::where("started", Event::EVENT_STARTED)->first();

            if ($vote) {
                event(new NewVoteEvent($vote));

                sleep($event->vote_time);
                $vote->is_active = false;
                $vote->finished_at = Carbon::now()->format('Y-m-d H:i:s');
                $vote->save();
                event(new VoteEndedEvent());
            }
        }
    }

    public function getActiveVote () {
        $active_vote = Vote::with('question')->where('is_active', true)->first();

        return response()->json([
            "data" => $active_vote,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

}
