<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Situation;
use Illuminate\Http\Request;
use Validator;

class SituationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Situation::all();
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
                'date' => ['date', 'date_format:Y-m-d'],
                'amount' => ['required', 'between:-999999999999.99,999999999999.99'],
                'expected' => ['required', 'between:-999999999999.99,999999999999.99']
            ]
        );

        if ($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->messages()
            ];
        }

        $data = $request->only(['date', 'expected', 'amount']);
        $situation = Situation::create($data);

        return [
            'status' => true,
            'situation' => $situation
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
                'amount' => ['required', 'between:-999999999999.99,999999999999.99'],
            ]
        );

        if ($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->messages()
            ];
        }

        $data = $request->only(['amount']);
        $situation = Situation::findOrFail($id);
        $situation->update($data);

        return [
            'status' => true,
            'situation' => $situation
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
            "status" => Situation::findOrFail($id)->delete(),
        ];
    }
}
