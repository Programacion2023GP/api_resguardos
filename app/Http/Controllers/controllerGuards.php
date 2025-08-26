<?php

namespace App\Http\Controllers;

use App\Models\ObjResponse;
use App\Models\Guards;
use App\Models\User;
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
                // $archivo->move(public_path("Resguardos"), $nuevoNombreArchivo);
                $archivo->storeAs("public/Resguardos", $nuevoNombreArchivo);
                $guard->picture = url("storage/Resguardos/{$nuevoNombreArchivo}");
                // $guard->picture = asset("Resguardos/" . $nuevoNombreArchivo);
                // $guard->picture = url("storage/Resguardos/{$nuevoNombreArchivo}");
                // $archivo->move(public_path("Resguardos/"), $nuevoNombreArchivo);
                // $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public" . "/Resguardos/" . $nuevoNombreArchivo;
                // $guard->picture = "https://api-imm.gomezconnect.com"."/Resguardos/".$nuevoNombreArchivo;

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
            $guard->quantity = $request->{"quantity"};


            $guard->type_id = $request->{"type_id"};
            $guard->number_korima = $request->{"number_korima"};

            $guard->description = $request->{"description"};
            $guard->brand = $request->{"brand"};
            $guard->state_id = $request->{"state_id"};
            $guard->serial = $request->{"serial"};
            if (Auth::user()->role == 3) {
                $guard->group = Auth::user()->group;

            } else {
                $guard->aproved =1;
                $guard->group = $request->{"group"};
            }

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
                // $archivo->move(public_path("Resguardos/"), $nuevoNombreArchivo);
                $archivo->storeAs("public/Resguardos", $nuevoNombreArchivo);
                $guard->picture = url("storage/Resguardos/{$nuevoNombreArchivo}");

                // $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public" . "/Resguardos/" . $nuevoNombreArchivo;

                // $guard->picture = "https://api-imm.gomezconnect.com"."/Resguardos/".$nuevoNombreArchivo;
            }



            $guard->number_korima = $request->{"number_korima"};

            $guard->type_id = $request->{"type_id"};
            $guard->quantity = $request->{"quantity"};

            $guard->description = $request->{"description"};
            $guard->brand = $request->{"brand"};
            $guard->state_id = $request->{"state_id"};
            $guard->serial = $request->{"serial"};
            if (Auth::user()->role == 3) {
                $guard->group = Auth::user()->group;
            } else {

                $guard->group = $request->{"group"};
            }
            if ($request->{"observations"}) {
                $guard->observations = $request->{"observations"};
            }
            $guard->update();


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
            $list = Guards::select(
                    'guards.*',
                    'types.name as Tipo',
                    'states.name as Estado',
                    DB::raw('MAX(user_guards.active) as resguard'),
                    DB::raw('MAX(user_guards.expecting) as expecting')
                )
                ->orderBy('guards.id', 'desc')
                ->leftJoin('types', 'types.id', '=', 'guards.type_id')
                ->leftJoin('states', 'states.id', '=', 'guards.state_id')
                ->leftJoin('user_guards', 'user_guards.guards_id', '=', 'guards.id')
                ->groupBy('guards.id', 'types.name', 'states.name');
    
            // Filtrado por rol
            switch (Auth::user()->role) {
                case 1:
                    // Admin - sin restricciones
                    break;
                case 2:
                    // Otro rol - sin restricciones adicionales
                    break;
                case 3:
                    // Rol 3 - filtra por grupo
                    $list->where('guards.group', Auth::user()->group);
                    break;
            }
    
            // Rol 3 ve todo de su grupo, otros solo aprobados
            if (Auth::user()->role == 3) {
                $list = $list->get();
            } else {
                $list = $list->where('guards.aproved', 1)->get();
            }
    
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de resguardos.';
            $response->data["alert_text"] = "Resguardos encontrados";
            $response->data["result"] = $list;
    
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
    
        return response()->json($response, $response->data["status_code"]);
    }
    
    public function showProccessAproved(Response $response){
        // return "ddd";
        $response->data = ObjResponse::DefaultResponse();

        try {
            $list = Guards::select(
                'guards.*',
                'types.name as Tipo',
                'states.name as Estado',
            )->orderBy('guards.id', 'desc')
                ->leftjoin('types', 'types.id', '=', 'guards.type_id')
                ->leftjoin('states', 'states.id', '=', 'guards.state_id')
                ->groupBy('guards.id', 'types.name', 'states.name')
                ->where('guards.aproved','<>',1)
                ->get();
                $response->data = ObjResponse::CorrectResponse();
                $response->data["message"] = 'peticion satisfactoria | lista de resguardos.';
                $response->data["alert_text"] = "resguardos encontrados";
                $response->data["result"] = $list;

        }catch(\Exception $ex){
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
            return response()->json($response, $response->data["status_code"]);
        }
        return response()->json($response, $response->data["status_code"]);

    }
    public function aproved(Response $response,Request $request){
        $response->data = ObjResponse::DefaultResponse();

        try {
            $guard = Guards::find($request->id);
            if (!$guard) {
                throw new \Exception("Resguardo no encontrado.");
            }
            if ($request->aproved ==0) {
                $guard->delete();
                # code...
            }
            else{

                $guard->aproved = $request->aproved;
                $guard->update();
            }

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Resguardo aprobado exitosamente.';
            $response->data["alert_text"] = "Resguardo aprobado";

        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }

        return response()->json($response, $response->data["status_code"]);
    }

    public function showOptions(Response $response, int $id)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $user = User::find($id);

            if (!$user) {
                throw new \Exception("Usuario no encontrado.");
            }

            $list = DB::table('guards')
                ->select('guards.id', DB::raw('CONCAT(guards.stock_number, " ", guards.description) AS text'));

            $userRole = Auth::user()->role;
            if ($userRole != 1 && $userRole != 2) {
                $list = $list->where('guards.group', $user->group)->where('guards.aproved',1);
            }

            $list = $list->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_guards')
                    ->whereColumn('user_guards.guards_id', 'guards.id')
                    ->where('user_guards.active', '=', 1);
            })->get();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de usuarios.';
            $response->data["alert_text"] = "Usuarios encontrados";
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
            $list = Guards::select(
                'users_guards.*',
                'users.email',
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




    public function destroy(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            // Primero, obtenemos el estado actual del guardia
            $currentActiveStatus = DB::table('guards')->where('id', $id)->value('active');

            // Si el estado actual es 0, simplemente lo cambiamos a 1
            if ($currentActiveStatus === 0) {
                $affectedRows = DB::table('guards')
                    ->where('id', $id)
                    ->update(['active' => 1]);

                if ($affectedRows === 0) {
                    throw new \Exception('No se pudo activar el resguardo.');
                }

                $response->data = ObjResponse::CorrectResponse();
                $response->data["message"] = 'Petición satisfactoria | resguardo activado.';
                $response->data["alert_text"] = 'Resguardo activado';
            } else {
                // Si el estado actual es 1, realizamos la lógica original
                $affectedRows = DB::affectingStatement("
                UPDATE guards
                SET guards.active = CASE WHEN guards.active = 1 THEN 0 ELSE 1 END
                WHERE guards.id = ?
                AND NOT EXISTS (
                    SELECT 1
                    FROM user_guards
                    WHERE user_guards.guards_id = guards.id 
                        AND (user_guards.active = 1 OR user_guards.expecting = 1)
                )
            ", [$id]);

                if ($affectedRows === 0) {
                    throw new \Exception('No se puede desactivar el resguardo asociado a un usuario activo.');
                }

                $response->data = ObjResponse::CorrectResponse();
                $response->data["message"] = 'Petición satisfactoria | resguardo desactivado.';
                $response->data["alert_text"] = 'Resguardo desactivado';
            }
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function transfer(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            // Primero, obtenemos el estado actual del guardia
            // $currentActiveStatus = DB::table('guards')->where('id', $request->id)->value('active');
            $user = DB::table('users')->where('id', $id)->first();
            // Si el estado actual es 0, simplemente lo cambiamos a 1
            $affectedRows = DB::table('guards')
                ->where('id', $request->id)
                ->update(['motive' => 'transferencia de resguardo a ' . $user->name]);
            $affectedRows = DB::table('user_guards')
                ->where('guards_id', $request->id)
                ->update(['expecting' => 1,]);
            if ($affectedRows === 0) {
                throw new \Exception('No se pudo transferir el resguardo.');
            }

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | resguardo activado.';
            $response->data["alert_text"] = 'Resguardo activado';

            // Si el estado actual es 1, realizamos la lógica original


        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function habilited(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            // Primero, obtenemos el estado actual del guardia

            // Si el estado actual es 0, simplemente lo cambiamos a 1
            DB::table('user_guards')
                ->where('user_guards.guards_id', $id)
                ->update([
                    'user_guards.active' => 0,
                    'user_guards.expecting' => 0
                ]);

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | resguardo regresado al stock.';
            $response->data["alert_text"] = 'Resguardo regresado al stock';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}
