<?php

use App\Http\Controllers\Api\ClientsController;
use App\Http\Controllers\Api\CoachesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\ProgramsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('coaches',[CoachesController::class,'index']);
Route::get('coaches/{id}',[CoachesController::class,'coaches']);
Route::post('coaches',[CoachesController::class,'store']);
Route::post('coaches/edit',[CoachesController::class,'update']);
Route::get('coaches/spesialis/{id}',[CoachesController::class,'spesialis']);
Route::post('coaches/spesialis',[CoachesController::class,'storeSpesialis']);
Route::delete('coaches/spesialis/{id}', [CoachesController::class, 'destroySpesialis']);
Route::get('coaches/certification/{id}',[CoachesController::class,'certi']);
Route::post('coaches/certification',[CoachesController::class,'storeCert']);
Route::delete('coaches/certification/{id}', [CoachesController::class, 'destroyCerti']);

Route::get('clients',[ClientsController::class,'index']);
Route::get('clients/{id}',[ClientsController::class,'clients']);
Route::post('clients',[ClientsController::class,'store']);
Route::post('clients/edit',[ClientsController::class,'update']);

Route::get('programs',[ProgramsController::class,'index']);
Route::get('programs/{id}',[ProgramsController::class,'programs']);
Route::post('programs',[ProgramsController::class,'store']);
Route::post('programs/edit',[ProgramsController::class,'update']);

Route::get('users',[UsersController::class,'index']);
Route::post('users',[UsersController::class,'store']);
Route::get('/users/{id}', [UsersController::class, 'users']);
Route::post('users/edit', [UsersController::class, 'update']);

