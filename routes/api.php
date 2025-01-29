<?php

use App\Http\Controllers\ControllerAirlanes;
use App\Http\Controllers\ControllerAirlanesGroups;
use App\Http\Controllers\ControllerGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerUsers;
use App\Http\Controllers\controllerGuards;
use App\Http\Controllers\ControllerKorima;
use App\Http\Controllers\ControllerStates;
use App\Http\Controllers\ControllerTypes;
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
    Route::get('/user/nomina/{nomina}', [ControllerUsers::class, 'nomina']);

    Route::get('/users/{role?}', [ControllerUsers::class, 'index']);
    Route::get('/reportsUsers', [ControllerUsers::class, 'reportsUsers']);
    Route::get('/allUsers', [ControllerUsers::class, 'allUsers']);

    
    Route::post('/usersdestroy/{id}', [ControllerUsers::class, 'destroy']);
    Route::post('/usersupdate', [ControllerUsers::class, 'update']);
    Route::get('/changeEnlance/{usuarioAntiguoId}/{usuarioNuevoId}/{correonuevo}', [ControllerUsers::class, 'changeEnlance']);

    //NOTE - PETICIONES PARA RESGUARDOS
    Route::post('/guards', [controllerGuards::class, 'create']);
    Route::get('/guards', [controllerGuards::class, 'index']);
    Route::get('/usersgroup/{group}/{id}', [ControllerUsers::class, 'group']);

    Route::post('/guardsdestroy/{id}', [controllerGuards::class, 'destroy']);
    Route::get('/guardshabilited/{id}', [controllerGuards::class, 'habilited']);

    Route::post('/guards/update', [controllerGuards::class, 'update']);
    Route::post('/guards/expecting/{id}', [ControllerUsersGuards::class, 'expecting']);
    Route::post('/background', [ControllerUsersGuards::class, 'uploadImage']);

    //NOTE - PETICIONES PARA ADMIN REPORTES DE GUARDS
    Route::get('/guards/admin', [controllerGuards::class, 'indexall']);
        //NOTE - PETICIONES PARA RESGUARDAR
        
        Route::post('/usersguards/create', [ControllerUsersGuards::class, 'create']);
        Route::get('/usersguards/guardsUser/{id}', [ControllerUsersGuards::class, 'guardsUser']);
        Route::post('/usersguards/guardsdestroy/{id}', [ControllerUsersGuards::class, 'destroy']);
        Route::post('/guards/canceldestroy/{id}', [ControllerUsersGuards::class, 'canceldestroy']);
        Route::get('/guards/showOptions/{id}', [controllerGuards::class, 'showOptions']);
        Route::get('/guards/history/{id}', [ControllerUsersGuards::class, 'historyGuard']);
        Route::post('/auth/register', [ControllerUsers::class, 'signup']);
        
        Route::get('/usersguards/guardsgroup/{group}/{id}', [ControllerUsersGuards::class, 'group']);
        Route::get('/charts/types', [ControllerUsersGuards::class, 'TypesCharts']);
        Route::get('/charts/states', [ControllerUsersGuards::class, 'StatesCharts']);
        Route::get('/charts/groups', [ControllerUsersGuards::class, 'groupsCharts']);

        Route::get('/guards/history/{id}', [ControllerUsersGuards::class, 'historyGuard']);
        Route::get('/types', [ControllerTypes::class, 'index']);
        Route::post('/types/destroy/{id}', [ControllerTypes::class, 'destroy']);
        Route::post('/types/update', [ControllerTypes::class, 'update']);
        
        Route::post('/types', [ControllerTypes::class, 'create']);
        
        Route::get('/states', [ControllerStates::class, 'index']);
        Route::post('/states/destroy/{id}', [ControllerStates::class, 'destroy']);
        Route::post('/states/update', [ControllerStates::class, 'update']);
        
        Route::post('/states', [ControllerStates::class, 'create']);
        
    Route::get('/airlanes', [ControllerAirlanes::class, 'index']);
    Route::post('/airlanes', [ControllerAirlanes::class, 'create']);
    Route::post('/airlanes/destroy/{id}', [ControllerAirlanes::class, 'destroy']);
    Route::post('/airlanes/update', [ControllerAirlanes::class, 'update']);
    
    Route::post('/airlanesgroup', [ControllerAirlanesGroups::class, 'create']);
    Route::get('/airlanesgroup/{id}', [ControllerAirlanesGroups::class, 'index']);
    
    Route::get('/korima', [ControllerKorima::class, 'index']);
    Route::post('/korima', [ControllerKorima::class, 'create']);
    Route::post('/korima/update', [ControllerKorima::class, 'update']);
    Route::post('/korima/down', [ControllerKorima::class, 'down']);
    Route::post('/korima/autorized', [ControllerKorima::class, 'autorized']);

});
Route::post('/airlanesgroup/destroy/{id}', [ControllerAirlanesGroups::class, 'destroy']);
Route::get('/guards/infoguard/{id}', [ControllerUsersGuards::class, 'infoGuard']);
//NOTE - PETICIONES PARA SESIONES    

Route::post('/auth/login', [ControllerUsers::class, 'login']);
Route::get('/hola', function () {
    return '¡Hola, Laravel!';
});
Route::post('/emitirEvento', [ControllerUsers::class, 'emitirEvento']);
Route::post('/usersadminedit', [ControllerUsers::class, 'changeAdmin']);
// Route::get('/charts/types', [ControllerUsersGuards::class, 'TypesCharts']);
// Route::get('/charts/states', [ControllerUsersGuards::class, 'StatesCharts']);
// Route::get('/charts/groups', [ControllerUsersGuards::class, 'groupsCharts']);