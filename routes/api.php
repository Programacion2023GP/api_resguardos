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
    //NOTE - PETICIONES PARA CERRAR SESION    
    Route::post('/auth/logout', [ControllerUsers::class, 'logout']);
    //NOTE - USUARIOS   
    Route::get('/users', [ControllerUsers::class, 'index']);
    Route::post('/usersdestroy/{id}', [ControllerUsers::class, 'destroy']);
    Route::post('/users/{id}', [ControllerUsers::class, 'update']);
    //NOTE - PETICIONES PARA RESGUARDOS    
    Route::post('/guards', [controllerGuards::class, 'create']);
    Route::get('/guards', [controllerGuards::class, 'index']);
    Route::post('/guardsdestroy/{id}', [controllerGuards::class, 'destroy']);
    Route::post('/guards/update', [controllerGuards::class, 'update']);
    //NOTE - PETICIONES PARA ADMIN REPORTES DE GUARDS   
    Route::get('/guards/admin', [controllerGuards::class, 'indexall']);
   

});
    //NOTE - PETICIONES PARA SESIONES    

Route::post('/auth/login', [ControllerUsers::class, 'login']);
Route::post('/auth/register', [ControllerUsers::class, 'signup']);
