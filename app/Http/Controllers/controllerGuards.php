<?php

namespace App\Http\Controllers;
use App\Models\ObjResponse;
use App\Models\Guards;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;

class controllerGuards extends Controller
{
    public function create(Request $request, Response $response)
    {
        $data = $request->all();
        $response->data = ObjResponse::DefaultResponse();
        try {
            foreach ($data as $key => $value) {
                if (strpos($key, 'picture') === 0) {
                    // Obtén el número de la imagen desde la clave
                    $i = substr($key, strlen('picture'));
                    $archivo = $request->file("picture$i");
         
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;
                    $archivo->move(public_path("Resguardos/"),$nuevoNombreArchivo);
                    $guard = new Guards();

                    $guard->picture = "http://127.0.0.1:8000"."/Resguardos/".$nuevoNombreArchivo;
                    if ($request->{"facture$i"}) {
                        $guard->facture = $request->{"facture$i"};
                    }
                    if ($request->{"emisor$i"}) {
                        $guard->emisor = $request->{"emisor$i"};
                    }
                    $guard->description = $request->{"description$i"};
                    $guard->type = $request->{"type$i"};
                    $guard->value = $request->{"value$i"};
                    $guard->name = $request->{"name$i"};
                    $guard->group = $request->{"group$i"};
                    $guard->numberconsecutive = $request->{"numberconsecutive$i"};
                    $guard->label = $request->{"label$i"};
                    $guard->payroll = $request->{"payroll$i"};
                    $guard->user_id = Auth::id();
                    $guard->save();
                   
                }
                
            }// Puedes guardar la información de cada archivo en la base de datos si es necesario.
                
            
    
                $response->data = ObjResponse::CorrectResponse();
                $response->data["message"] = 'Archivos subidos exitosamente';
                $response->data["alert_text"] = "Archivos subidos";
            
               
            
    
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
    
        return response()->json($response, $response->data["status_code"]);
    }
    public function update(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $guard = Guards::find($request->id);
            if ($request->file("picture")) {

                    $archivo = $request->file("picture");
         
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;
                    $archivo->move(public_path("Resguardos/"),$nuevoNombreArchivo);

                    $guard->picture = "http://127.0.0.1:8000"."/Resguardos/".$nuevoNombreArchivo;
            }
                    if ($request->{"facture"}) {
                        $guard->facture = $request->{"facture"};
                    }
                    if ($request->{"emisor"}) {
                        $guard->emisor = $request->{"emisor"};
                    }
                    $guard->description = $request->{"description"};
                    $guard->type = $request->{"type"};
                    $guard->value = $request->{"value"};
                    $guard->name = $request->{"name"};
                    $guard->group = $request->{"group"};
                    $guard->numberconsecutive = $request->{"numberconsecutive"};
                    $guard->label = $request->{"label"};
                    $guard->payroll = $request->{"payroll"};
                    // $guard->user_id = Auth::id();
                    $guard->save();
                   
                
                    // Puedes guardar la información de cada archivo en la base de datos si es necesario.
                
            
    
                $response->data = ObjResponse::CorrectResponse();
                $response->data["message"] = 'Archivos Actualizado exitosamente';
                $response->data["alert_text"] = "Archivos Actualizado";
            
               
            
    
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
           $list = Guards::
             where('user_id',Auth::id())->where('active',1)
            ->orderBy('users_guards.id', 'desc')
              ->get();
  
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | lista de usuarios.';
           $response->data["alert_text"] = "usuarios encontrados";
           $response->data["result"] = $list;
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }
     public function indexall(Response $response)
     {
        $response->data = ObjResponse::DefaultResponse();
        try {
           // $list = DB::select('SELECT * FROM users where active = 1');
           // User::on('mysql_gp_center')->get();
           $list = Guards::select('users_guards.*', 'users.email')
           ->join('users', 'users.id', '=', 'users_guards.user_id')
           ->orderByDesc('users_guards.id')
       ->orderByDesc('users_guards.payroll')
           ->get();
       
  
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | lista de usuarios.';
           $response->data["alert_text"] = "usuarios encontrados";
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
            Guards::where('id', $id)
             ->update([
                'active' => DB::raw('NOT active'),
                //  'deleted_at' => date('Y-m-d H:i:s'),
             ]);
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | resguardo desactivado.';
             $response->data["alert_text"] ='Resguardo desactivado';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}
