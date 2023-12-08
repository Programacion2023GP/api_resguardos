<?php

namespace App\Http\Controllers;

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
            $list = User::select('*')
                ->Join('user_guards', 'user_guards.user_id', '=', 'users.id')
                ->Join('guards', 'user_guards.guards_id', '=', 'guards.id')

                ->where('users.id', $id) // Filtrar por el ID proporcionado
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
