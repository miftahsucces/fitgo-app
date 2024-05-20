<?php

use App\Http\Controllers\Api\CoachesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('coaches',[CoachesController::class,'index']);
Route::post('coaches',[CoachesController::class,'store']);

Route::get('users',[UsersController::class,'index']);
Route::post('users',[UsersController::class,'store']);