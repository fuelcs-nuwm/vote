<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Events\NewVoteEvent;
use App\Events\VoteEndedEvent;
use App\Http\Controllers\Controller;
use App\Question;
use App\RegisteredUser;
use App\Vote;
use App\VoteAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Spatie\Async\Pool;

class VoteController extends Controller
{
    public function startNewVote (Request $request) {

        $event = Event::where("started", Event::EVENT_STARTED)->first();

        if ($event) {


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
                $vote->vote_time = $event->vote_time;
                $vote->save();

                $vote = Vote::with('question')->find($vote->id);

                if ($vote) {
                    event(new NewVoteEvent($vote));

                    sleep($event->vote_time);

                    $regirtered_user = RegisteredUser::where('event_id', $event->id)->get()->count();
                    $answer_yes = VoteAnswer::where(['vote_id'=> $vote->id ,'answer' => 1])->get()->count();
                    $answer_no =  VoteAnswer::where(['vote_id'=> $vote->id ,'answer' => 2])->get()->count();
                    $answer_abstained = VoteAnswer::where(['vote_id'=> $vote->id ,'answer' => 3])->get()->count();
                    $didnt_vote = abs($regirtered_user - $answer_yes - $answer_no - $answer_abstained);
                    $vote->is_active = false;
                    $vote->answer_yes = $answer_yes;
                    $vote->answer_no = $answer_no;
                    $vote->answer_abstained = $answer_abstained;
                    $vote->didnt_vote = $didnt_vote;
                    $vote->finished_at = Carbon::now()->format('Y-m-d H:i:s');
                    $vote->save();
                    event(new VoteEndedEvent());
                }
            }
        } else {
            return response()->json([
                "message" => "Немає активної події!і",
                "status" => 422
            ], 200);
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
