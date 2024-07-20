<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'auth'], function() {
    Route::controller(\App\Http\Controllers\API\AuthController::class)->group(function() {
        Route::post('register','register');
        Route::post('login','login');
        Route::get('send-mail', 'testMail');
        Route::post('forget-password-request', 'forgetPasswordRequest');
        Route::post('forget-password', 'verifyAndChangePassword');
    });
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::controller(\App\Http\Controllers\API\AuthController::class)->group(function() {
            Route::get('logout', 'logout');
            Route::get('get-profile', 'getProfile');
            Route::post('change-password', 'changePassword');
            Route::post('update-profile', 'updateProfile');
        });
    });
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::group(['prefix' => 'xyz'], function(){

        Route::controller(\App\Http\Controllers\API\CoachesController::class)->group(function(){
            Route::get('coaches','index');
            Route::get('coaches/{id}','coaches');
            Route::post('coaches','store');
            Route::post('coaches/edit','update');
            Route::get('coaches/spesialis/{id}','spesialis');
            Route::post('coaches/spesialis','storeSpesialis');
            Route::delete('coaches/spesialis/{id}',  'destroySpesialis');
            Route::get('coaches/certification/{id}','certi');
            Route::post('coaches/certification','storeCert');
            Route::delete('coaches/certification/{id}',  'destroyCerti');
        });  

        Route::controller(\App\Http\Controllers\API\ClientsController::class)->group(function(){
            Route::get('clients','index');
            Route::get('clients/{id}','clients');
            Route::post('clients','store');
            Route::post('clients/edit','update');
        }); 

        Route::controller(\App\Http\Controllers\API\ProgramsController::class)->group(function(){
            Route::get('programs','index');
            Route::get('programs/{id}','programs');
            Route::post('programs','store');
            Route::post('programs/edit','update');

        }); 

        Route::controller(\App\Http\Controllers\API\ScheduleController::class)->group(function(){
            Route::get('schedule','trainings');
            Route::get('schedule/{id}','schedule');
            Route::post('schedule','store');
            Route::post('schedule/edit','update');
            Route::get('schedule/member','index');
            Route::get('schedule/member/{id}','members');
            Route::post('schedule/member','storeMember');
            Route::post('schedule/member/edit','update');

        }); 

        Route::controller(\App\Http\Controllers\API\UsersController::class)->group(function(){
            Route::get('users','index');
            Route::post('users','store');
            Route::get('/users/{id}', 'users');
            Route::post('users/edit', 'update');

        }); 

        Route::controller(\App\Http\Controllers\API\ProgressController::class)->group(function(){
            Route::get('progress/trainer/{id}','trainerClient');

        }); 


    });
});
