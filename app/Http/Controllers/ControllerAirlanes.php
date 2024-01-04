<?php

namespace App\Http\Controllers;

use App\Models\ObjResponse;
use App\Models\Airlane;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerAirlanes extends Controller
{
    public function create(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $create = Airlane::create([
                'name' => $request->name,

            ]);
        
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | adscripcion registrado.';
            $response->data["alert_text"] = "Se ha creado correctamente el adscripcion";
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
          $list = Airlane::
          orderBy('airlanes.id', 'desc')
            ->where('active',1)
          ->get();
      
      
 
          $response->data = ObjResponse::CorrectResponse();
          $response->data["message"] = 'peticion satisfactoria | lista de adscripcion.';
          $response->data["alert_text"] = "usuarios adscripcion";
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
             
 
            Airlane::where('id', $id)
             ->update([
                'active' => DB::raw('NOT active'),
             ]);
             
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | adscripción baja.';
             $response->data["alert_text"] ='adscripción baja';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}
