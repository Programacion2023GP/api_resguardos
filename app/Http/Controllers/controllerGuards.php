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
                $archivo->move(public_path("Resguardos/"), $nuevoNombreArchivo);
                $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public" . "/Resguardos/" . $nuevoNombreArchivo;
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


            $guard->type_id = $request->{"type_id"};
            $guard->number_korima = $request->{"number_korima"};

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
                $archivo->move(public_path("Resguardos/"), $nuevoNombreArchivo);
                $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public" . "/Resguardos/" . $nuevoNombreArchivo;

                // $guard->picture = "https://api-imm.gomezconnect.com"."/Resguardos/".$nuevoNombreArchivo;
            }



            $guard->number_korima = $request->{"number_korima"};

            $guard->type_id = $request->{"type_id"};
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
                DB::raw('MAX(user_guards.expecting) as expecting')
            )->orderBy('guards.id', 'desc')
                ->leftjoin('types', 'types.id', '=', 'guards.type_id')
                ->leftjoin('states', 'states.id', '=', 'guards.state_id')
                ->leftjoin('user_guards', 'user_guards.guards_id', '=', 'guards.id')
                ->groupBy('guards.id', 'types.name', 'states.name');
            switch (Auth::user()->role) {
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    $list->where('guards.group', Auth::user()->group);

                    break;
            }
            $list = $list->get();




            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | lista de resguardos.';
            $response->data["alert_text"] = "resguardos encontrados";
            $response->data["result"] = $list;
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


            $list = DB::table('guards')
                ->select('guards.id', DB::raw('CONCAT(guards.stock_number, " ", guards.description) AS text'));

            if (Auth::user()->role != 1 || Auth::user()->role != 2) {
                $list = $list->where('guards.group', $user->group);
            }
            $list = $list->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_guards')
                    ->whereColumn('user_guards.guards_id', 'guards.id')
                    ->where('user_guards.active', '=', 1);
            })
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
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}
