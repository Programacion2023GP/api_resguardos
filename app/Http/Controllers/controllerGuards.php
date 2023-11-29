<?php

namespace App\Http\Controllers;
use App\Models\ObjResponse;
use App\Models\Guards;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;

class controllerGuards extends Controller
{
    public function create(Request $request, Response $response)
    {
        $data = $request->all();
        $response->data = ObjResponse::DefaultResponse();
        try {
                    // Obtén el número de la imagen desde la clave
                    $guard = new Guards();
                    if ($request->hasFile("picture")) { // Check if the request contains the "picture" file

                    $archivo = $request->file("picture");
                        
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;
                    $archivo->move(public_path("Resguardos/"),$nuevoNombreArchivo);
                    $guard->picture = "https://api-imm.gomezconnect.com"."/Resguardos/".$nuevoNombreArchivo;
                }
                    $ultimoGuard = Guards::orderBy('id', 'desc')->first();
                    if ($ultimoGuard) {
                        // Obtener el número después del prefijo 'C-'
                        $numeroActual = intval(substr($ultimoGuard->stock_number, 2));
                    
                        // Incrementar el número
                        $nuevoNumero = $numeroActual + 1;
                    
                        // Crear el nuevo stock_number con el prefijo y el nuevo número
                        $nuevoStockNumber = 'C-' . $nuevoNumero;
                    } else {
                        // Si no hay registros, empezar con C-1
                        $nuevoStockNumber = 'C-1';
                    }
                    
                    $guard->stock_number = $nuevoStockNumber;

                    if ($request->{"type"}) {
                        $guard->type = $request->{"type"};
                    }
                   
                    $guard->description = $request->{"description"};
                    $guard->brand = $request->{"brand"};
                    $guard->state = $request->{"state"};
                    $guard->serial = $request->{"serial"};
                    $guard->airlne = $request->{"airlne"};
                    $guard->payroll = $request->{"payroll"};
                    $guard->employeed = $request->{"employeed"};
                    $guard->group = $request->{"group"};

                    $guard->user_id = Auth::id();

                    $guard->date = $request->{"date"};

                    if ($request->{"observations"}) {
                        $guard->observations = $request->{"observations"};
                    }
                    $guard->save();
                   
                
                
            // Puedes guardar la información de cada archivo en la base de datos si es necesario.
                
            
    
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

            if ($request->{"type"}) {
                $guard->type = $request->{"type"};
            }
           
            $guard->description = $request->{"description"};
            $guard->brand = $request->{"brand"};
            $guard->state = $request->{"state"};
            $guard->serial = $request->{"serial"};
            $guard->airlne = $request->{"airlne"};
            $guard->payroll = $request->{"payroll"};
            $guard->employeed = $request->{"employeed"};
            $guard->group = $request->{"group"};

            $guard->user_id = Auth::id();

            $guard->date = $request->{"date"};

            if ($request->{"observations"}) {
                $guard->observations = $request->{"observations"};
            }
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
           $list = Guards::select('users_guards.*', 'users.email',
           DB::raw("IF(users_guards.active = 0, 'Inactivo', 'Activo') as exist"),

           )
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
