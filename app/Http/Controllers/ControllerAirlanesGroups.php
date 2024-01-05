<?php

namespace App\Http\Controllers;

use App\Models\ObjResponse;
use App\Models\Airlane_Group;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ControllerAirlanesGroups extends Controller
{
    public function create(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            foreach ($request->groups_id as $groupId) {
                $create = Airlane_Group::create([
                    'airlanes_id' => $request->airlanes_id,
                    'groups_id' => $groupId,
    
                ]);
            
            }
           
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | grupo registrado.';
            $response->data["alert_text"] = "Se ha creado correctamente el grupo";
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        
        return response()->json($response, $response->data["status_code"]);
        
    }
    public function index(Response $response,int $id)
     {
        $response->data = ObjResponse::DefaultResponse();
        try {
           // $list = DB::select('SELECT * FROM users where active = 1');
           // User::on('mysql_gp_center')->get();
           $list = Airlane_Group::
           select('airlanes_groups.id','groups.name')
           ->orderBy('groups_id', 'desc')
           ->join('groups', 'groups.id', '=', 'airlanes_groups.groups_id')
           ->where('airlanes_id',$id)
           ->get();
       
       
  
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | lista de grupos.';
           $response->data["alert_text"] = "grupos encontrados";
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
             
 
           $airlaneGroup =  Airlane_Group::find($id);
           $airlaneGroup->delete();
             
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | resguardo baja.';
             $response->data["alert_text"] ='resguardo baja';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}
