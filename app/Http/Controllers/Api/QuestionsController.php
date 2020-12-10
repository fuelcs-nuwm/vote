<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Http\Request;
use Validator;

class QuestionsController extends Controller
{
    public function index () {
        $questions = Question::all();

        return response()->json([
            "data" => $questions,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function get_event_questions ($id) {
        $model = Question::all()->where('event_id', $id);
        $questions = [];

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

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'event_id' => 'exists:App\Event,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $question = new Question();
        $question->fill($request->toArray());
        $question->save();

        return response()->json([
            "data" => $question,
            "message" => "ok",
            "status" => 200
        ], 200);
    }

    public function update ($id, Request $request)
    {
        $question = Question::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'event_id' => 'exists:App\Event,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $question->fill($request->toArray());
        $question->save();

        return response()->json([
            "data" => $question,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function delete ($id) {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
