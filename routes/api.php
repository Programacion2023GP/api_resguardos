<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerUsers;
use App\Http\Controllers\controllerGuards;
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

Route::middleware('auth:sanctum')->group(function(){   
    Route::post('/auth/logout', [ControllerUsers::class, 'logout']);
    Route::get('/users', [ControllerUsers::class, 'index']);
    Route::delete('/users/{id}', [ControllerUsers::class, 'destroy']);
    Route::post('/users/{id}', [ControllerUsers::class, 'update']);
    Route::post('/guards', [controllerGuards::class, 'create']);
    Route::get('/guards', [controllerGuards::class, 'index']);
    Route::get('/guards/admin', [controllerGuards::class, 'indexall']);

    Route::delete('/guards/{id}', [controllerGuards::class, 'destroy']);
    Route::post('/guards/update', [controllerGuards::class, 'update']);

    

});
Route::post('/auth/login', [ControllerUsers::class, 'login']);
Route::post('/auth/register', [ControllerUsers::class, 'signup']);
