<?php

use App\Http\Controllers\ControllerAirlanes;
use App\Http\Controllers\ControllerAirlanesGroups;
use App\Http\Controllers\ControllerGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerUsers;
use App\Http\Controllers\controllerGuards;
use App\Http\Controllers\ControllerUsersGuards;

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
    Route::post('/auth/register', [ControllerUsers::class, 'signup']);
    Route::post('/auth/logout', [ControllerUsers::class, 'logout']);
    //NOTE - USUARIOS
    Route::get('/user/{id}', [ControllerUsers::class, 'user']);

    Route::get('/users/{role?}', [ControllerUsers::class, 'index']);
    Route::get('/reportsUsers', [ControllerUsers::class, 'reportsUsers']);

    Route::post('/usersdestroy/{id}', [ControllerUsers::class, 'destroy']);
    Route::post('/usersupdate', [ControllerUsers::class, 'update']);
    //NOTE - PETICIONES PARA RESGUARDOS
    Route::post('/guards', [controllerGuards::class, 'create']);
    Route::get('/guards', [controllerGuards::class, 'index']);
    Route::post('/guardsdestroy/{id}', [controllerGuards::class, 'destroy']);
    Route::post('/guards/update', [controllerGuards::class, 'update']);
    //NOTE - PETICIONES PARA ADMIN REPORTES DE GUARDS
    Route::get('/guards/admin', [controllerGuards::class, 'indexall']);
        //NOTE - PETICIONES PARA RESGUARDAR

    Route::post('/usersguards/create', [ControllerUsersGuards::class, 'create']);
    Route::get('/usersguards/guardsUser/{id}', [ControllerUsersGuards::class, 'guardsUser']);
    Route::post('/usersguards/guardsdestroy/{id}', [ControllerUsersGuards::class, 'destroy']);
    Route::get('/guards/showOptions/{id}', [controllerGuards::class, 'showOptions']);
    Route::get('/guards/history/{id}', [ControllerUsersGuards::class, 'historyGuard']);
    Route::post('/auth/register', [ControllerUsers::class, 'signup']);

    Route::get('/usersguards/guardsgroup/{group}', [ControllerUsersGuards::class, 'group']);

    Route::get('/guards/history/{id}', [ControllerUsersGuards::class, 'historyGuard']);
    Route::get('/groups', [ControllerGroups::class, 'index']);
    Route::post('/groups/destroy/{id}', [ControllerGroups::class, 'destroy']);
    Route::post('/groups/update', [ControllerGroups::class, 'update']);

    Route::post('/groups', [ControllerGroups::class, 'create']);
    Route::get('/airlanes', [ControllerAirlanes::class, 'index']);
    Route::post('/airlanes', [ControllerAirlanes::class, 'create']);
    Route::post('/airlanes/destroy/{id}', [ControllerAirlanes::class, 'destroy']);
    Route::post('/airlanes/update', [ControllerAirlanes::class, 'update']);

    Route::post('/airlanesgroup', [ControllerAirlanesGroups::class, 'create']);
    Route::get('/airlanesgroup/{id}', [ControllerAirlanesGroups::class, 'index']);
    
});
Route::post('/airlanesgroup/destroy/{id}', [ControllerAirlanesGroups::class, 'destroy']);
Route::get('/guards/infoguard/{id}', [ControllerUsersGuards::class, 'infoGuard']);
    //NOTE - PETICIONES PARA SESIONES    

Route::post('/auth/login', [ControllerUsers::class, 'login']);
Route::get('/hola', function () {
    return '¡Hola, Laravel!';
});
