<?php

use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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


Route::post('register', [AuthController::class,'register']);
Route::post('resend/code', [AuthController::class,'resendCode']);
Route::post('validate/code', [AuthController::class,'validateCode']);
Route::post('login', [AuthController::class,'login']);
Route::get('logout', [AuthController::class,'logout'])->middleware('auth:api');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::as('items.')->prefix('items')->middleware(['auth:api','verified'])->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/store', [TaskController::class, 'store']);
    Route::get('/show/{id}', [TaskController::class, 'show']);
    Route::get('/edit/{id}', [TaskController::class, 'edit']);
    Route::post('/update/{id}', [TaskController::class, 'update']);
    Route::delete('/destroy/{id}', [TaskController::class, 'destroy']);
});