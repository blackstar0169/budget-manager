<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index() {
        $config = [
            'recurrences' => config('app.recurrences'),
            'start_date' => config('app.start_date'),
            'start_money' => config('app.start_money'),
            'baseURL' => route('index')
        ];
        return view('index', ['config' => $config]);
    }
}
