<?php

namespace App\Http\Controllers;

use App\Models\Guards;
use Illuminate\Http\Request;
use App\Models\ObjResponse;
use App\Models\Users_guards;
use App\Models\User;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
class ControllerUsersGuards extends Controller
{
    public function create(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $create = Users_guards::create([
                'user_id' => $request->user_id,
                'guards_id' => $request->guard_id,
                'dateup' => date('Y-m-d'),

            ]);
        
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | usuario registrado.';
            $response->data["alert_text"] = "Se ha creado correctamente el usuario";
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        
        return response()->json($response, $response->data["status_code"]);
        
    }
    public function guardsUser(Response $response, int $id)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = User::select('users.*', 'user_guards.*', 'guards.*','user_guards.id as idguard', 'user_guards.active as used')
            ->join('user_guards', 'user_guards.user_id', '=', 'users.id')
            ->join('guards', 'user_guards.guards_id', '=', 'guards.id')
            ->where('users.id', $id) // Filtrar por el ID proporcionado
            ->orderBy('users.id', 'desc') // Ordenar por el campo ID de users (o el campo deseado)
            ->orderByRaw('user_guards.active DESC') // Ordena por 'active' de forma descendente (1 antes que 0)

            ->get();
        
    
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de usuarios.';
            $response->data["alert_text"] = "Usuarios encontrados";
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function destroy(int $id, Response $response,Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            

            Users_guards::where('id', $id)
            ->update([
                'observation' => $request->observation,
               'active' => DB::raw('NOT active'),
               'datedown' => date('Y-m-d'),
               //  'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | resguardo baja.';
            $response->data["alert_text"] ='resguardo baja';

        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function historyGuard(Response $response, int $id)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = User::select('*','user_guards.active as used')
            ->join('user_guards', 'user_guards.user_id', '=', 'users.id')
            ->join('guards', 'user_guards.guards_id', '=', 'guards.id')
    ->where('guards.id', $id) 
    ->orderByRaw('user_guards.active DESC') // Agrega 'DESC' para ordenar en descendente
    ->get();

        
    
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de usuarios.';
            $response->data["alert_text"] = "Usuarios encontrados";
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function group(Response $response, string $group)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = User::select('users.*', 'user_guards.*', 'guards.*','user_guards.id as idguard', 'user_guards.active as used')
            ->join('user_guards', 'user_guards.user_id', '=', 'users.id')
            ->join('guards', 'user_guards.guards_id', '=', 'guards.id')
            ->where('users.group', $group) // Filtrar por el ID proporcionado
            ->orderBy('users.id', 'desc') // Ordenar por el campo ID de users (o el campo deseado)

            ->get();

        
    
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de usuarios.';
            $response->data["alert_text"] = "Usuarios encontrados";
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}    
