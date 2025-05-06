<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', fn() => response()->json([
    'message' => 'Hello, World!'
]));

Route::get('/reservations', [ReservationController::class, 'all']);
Route::post('/reservations', [ReservationController::class, 'create']);
Route::post('/reservations/{pin}/confirm', [ReservationController::class, 'confirm']);
