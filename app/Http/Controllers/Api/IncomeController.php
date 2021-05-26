<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use DateTime;
use Illuminate\Http\Request;
use Validator;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Income::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'label' => ['required', 'min:3'],
                'date' => ['required', 'date_format:Y-m-d'],
                'recurrence' => ['required', 'in:'.implode(',', config('app.recurrences'))],
                'amount' => ['required', 'between:-999999999999.99,999999999999.99']
            ]
        );

        if ($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->messages()
            ];
        }

        $data = $request->only(['label', 'date', 'recurrence', 'amount', 'recurrenceNumber']);
        $income = Income::create($data);

        return [
            'status' => true,
            'income' => $income
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "label" => ["required", "min:3"],
                "date" => ["required", "date_format:Y-m-d"],
                "recurrence" => ["required", "in:".implode(',', config('app.recurrences'))],
                "amount" => ["required", "between:-999999999999.99,999999999999.99"]
            ]
        );

        if ($validator->fails()) {
            return [
                "status" => false,
                "errors" => $validator->messages()
            ];
        }

        $data = $request->only(['label', 'date', 'recurrence', 'amount', 'recurrenceNumber']);
        $income = Income::findOrFail($id);
        $income->update($data);

        return [
            "status" => true,
            "income" => $income
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return [
            "status" => Income::findOrFail($id)->delete(),
        ];
    }
}
