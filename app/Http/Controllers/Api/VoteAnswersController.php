<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Vote;
use App\VoteAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class VoteAnswersController extends Controller
{
    public function getActiveVote () {
        $active_vote = Vote::with('question')->where('is_active', true)->first();

        return response()->json([
            "data" => $active_vote,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answer' => 'required|min:1|max:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $active_vote = Vote::where('is_active', true)->first();

        if ($active_vote) {

            $user = Auth::guard()->user();

            $vote_answer = new VoteAnswer();
            $vote_answer->vote_id = $active_vote->id;
            $vote_answer->user_id = $user->id;
            $vote_answer->answer = $request->answer;
            $vote_answer->date = Carbon::now()->format('Y-m-d H:i:s');
            $vote_answer->save();

            return response()->json([
                "data" => [],
                "message" => "ok",
                "status" => 200
            ], 200);

        } else {
            return response()->json([
                "message" => "Немає активного голосування!",
                "status" => 422
            ], 422);
        }
    }
}
