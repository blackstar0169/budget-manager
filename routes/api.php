<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\SituationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('incomes', IncomeController::class);
Route::resource('situations', SituationController::class);
