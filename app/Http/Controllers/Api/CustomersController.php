<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class CustomersController extends Controller
{
    public function index () {
        $customers = Customer::all();

        return response()->json([
            "data" => $customers,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function get_event_customers ($id) {
        $model = Customer::with('groups')->where('event_id', $id)->get()->all();

        return response()->json([
            "data" => $model,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function store (Request $request)
    {
        DB::beginTransaction();
        try
        {
            $validator = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('customers')->where(function ($query) use($request) {
                        return $query->where('email', $request->email)
                            ->where('event_id', $request->event_id);
                    }),
                ],
                'event_id' => 'exists:App\Event,id',
                'group_ids' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "data" => [],
                    "message" => $validator->errors(),
                    "status" => 422
                ], 422);
            }

            $customer = new Customer();
            $customer->fill($request->except('group_ids'));
            $customer->save();
            $customer->groups()->sync($request->group_ids);

            DB::commit();

            return response()->json([
                "data" => $customer,
                "message" => "ok",
                "status" => 200
            ], 200);

        }
        catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "data" => [],
                "message" => $e->getMessage(),
                "status" => 500
            ],500);
        }
    }

    public function update ($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:App\Customer,email',
            'event_id' => 'exists:App\Event,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "data" => [],
                "message" => $validator->errors(),
                "status" => 422
            ], 200);
        }

        $customer->fill($request->toArray());
        $customer->save();

        return response()->json([
            "data" => $customer,
            "message" => "ok",
            "status" => 200
        ],200);
    }

    public function delete ($id) {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            "data" => [],
            "message" => "ok",
            "status" => 200
        ],200);
    }
}
