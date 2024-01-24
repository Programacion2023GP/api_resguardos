<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjResponse;
use App\Models\States;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
class ControllerStates extends Controller
{
    public function create(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $create = States::create([
                'name' => $request->name,

            ]);
        
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | estado registrado.';
            $response->data["alert_text"] = "Se ha creado correctamente el estado";
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        
        return response()->json($response, $response->data["status_code"]);
        
    }
    public function index(Response $response)
     {
        $response->data = ObjResponse::DefaultResponse();
        try {
           // $list = DB::select('SELECT * FROM users where active = 1');
           // User::on('mysql_gp_center')->get();
           $list = States::orderBy('id', 'desc')
           ->where('active', 1)
          
           ->get();
       
       
       
  
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | lista de estado.';
           $response->data["alert_text"] = "estado encontrados";
           $response->data["result"] = $list;
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }
     public function destroy(int $id, Response $response)
     {
         $response->data = ObjResponse::DefaultResponse();
         try {
             
 
           
            $affectedRows = States::where('id', $id)
            ->where(function ($query) use ($id) {
                $query->whereNotExists(function ($subquery) use ($id) {
                    $subquery->select(DB::raw(1))
                        ->from('guards')
                        ->whereRaw('guards.state_id = states.id')
                        ->where('state_id', $id);
                });
            })
            ->update([
                'active' => DB::raw('NOT active'),
            ]);
        
        if ($affectedRows === 0) {
            throw new \Exception('No se puede eliminar tiene resguardos de este estado fisico.');
        }


             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | resguardo baja.';
             $response->data["alert_text"] ='resguardo baja';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
     public function update(Request $request, Response $response)
     {
         $response->data = ObjResponse::DefaultResponse();
         try {
            $group = States::find($request->id);
            if ($group) {
                $group->name = $request->name;
                $group->save();

            }

             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | estado actualizada.';
             $response->data["alert_text"] = 'estado actualizado';

         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}