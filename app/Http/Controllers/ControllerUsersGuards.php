<?php

namespace App\Http\Controllers;

use App\Models\Guards;
use App\Models\Korima;
use Illuminate\Http\Request;
use App\Models\ObjResponse;
use App\Models\Users_guards;
use App\Models\User;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Auth;

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
            $list = User::select(
                'users.*',
                'user_guards.*',
                'guards.*',
                'user_guards.id as idguard',
                'user_guards.active as used',
                'states.name as Estado',
                'types.name as Tipo'
            )
                ->join('user_guards', 'user_guards.user_id', '=', 'users.id')
                ->join('guards', 'user_guards.guards_id', '=', 'guards.id')
                ->leftjoin('types', 'types.id', '=', 'guards.type_id')
                ->leftjoin('states', 'states.id', '=', 'guards.state_id')

                ->where('users.id', $id) // Filtrar por el ID proporcionado
                ->orderBy('guards.stock_number', 'desc') // Ordenar por el campo ID de users (o el campo deseado)
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
    public function destroyguard(int $id, Response $response,Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $affectedRows = Users_guards::where('id', $id)->first();
            
            $affectedRows ->update([
                    'expecting' => 1,

                ]);;
            $guard = Guards::where('id', $affectedRows->guards_id)->update([
                'motive' => $request->observation,

            ]);;

                // ->whereNotExists(function ($query) use ($id) {
                //     $query->select(DB::raw(1))
                //         ->from('user_guards')
                //         ->whereColumn('user_guards.guards_id', 'guards.id')
                //         ->where('user_guards.active', 1)
                //         ->whereNull('user_guards.deleted_at');
                // })
                // ->update([
                //     'active' => DB::raw('NOT active'),
                // ]);

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
    public function destroy(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {


            $user = Korima::where('id', $id)->first();
            $user->update([
                'observation' => $request->observation,
                'expecting' => 1,
                'datedown' => date('Y-m-d'),
                //  'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            Guards::where('id', $user->guards_id)
                ->update([
                    'motive' => $request->observation,

                    //  'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | resguardo baja.';
            $response->data["alert_text"] = 'resguardo baja';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function canceldestroy(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {


            $user = Guards::where('id', $id)->first();
            $user->update([
                'motive' => null,

                //  'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            $as = Users_guards::where('guards_id', $id)->update([
                    'observation' => null,
                    'expecting' => 0,
                    'datedown' => null,
                    //  'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | cancelación de la baja.';
            $response->data["alert_text"] = 'cancelación de la baja';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function historyGuard(Response $response, int $id)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = User::select('*', 'user_guards.active as used', 'user_guards.expecting')
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
    public function group(Response $response, string $group, int $id)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = User::select('users.*', 'user_guards.*', 'guards.*', 'user_guards.id as idguard', 'user_guards.active as used')
                ->join('user_guards', 'user_guards.user_id', '=', 'users.id')
                ->join('guards', 'user_guards.guards_id', '=', 'guards.id')
                ->where('users.group', $group)->where('users.role', 4)
                ->orWhere('users.group', $group)->where('users.id', $id)

                // Filtrar por el ID proporcionado
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
    // public function info(Response $response)
    // {

    //         // Contenido a imprimir
    //         $printerName = "BrotherPT-P950NW"; // Intentemos con este formato
    //         $connector = new WindowsPrintConnector($printerName);
    //         $printer = new Printer($connector);

    //         $currentDateTime = date('d/m/Y H:i:s');

    //         $printer->text($currentDateTime . "\n");
    //         $printer->text(''. "\n");



    //         $printer->setJustification(Printer::JUSTIFY_CENTER);
    //         $printer->text('' . "\n");
    //         $printer->setTextSize(2, 2);
    //         $printer->text('cellphone' . "\n");
    //         $printer->text('address' . "\n");
    //         $printer->setTextSize(1, 2);



    //         $printer->feed(3);
    //         $printer->cut();
    //         $printer->close();


    // }



    public function expecting(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $affectedRows = Users_guards::where('guards_id', $id)
                ->where('active', 1)
                ->where('expecting', 1)
                ->update([
                    'expecting' => 0,
                    'active' => 0,

                ]);

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
    public function infoGuard(int $id, Response $response)
    {
        try {
            $info = Guards::where('guards.id', $id)
                ->select('guards.*', 'users.name', 'users.payroll')
                ->leftJoin('user_guards', 'guards.id', '=', 'user_guards.guards_id')
                ->leftJoin('users', 'user_guards.user_id', '=', 'users.id')
                ->where(function ($query) {
                    $query->where('user_guards.active', 1)
                        ->orWhereNull('user_guards.guards_id');
                })
                ->get();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Petición satisfactoria | lista de usuarios.';
            $response->data["alert_text"] = "Usuarios encontrados";
            $response->data["result"] = $info;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $fileName = 'bg.jpg';
            $storagePath = public_path('background');

            // Crea el directorio si no existe
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            // Mueve la imagen al directorio de almacenamiento
            $image->move($storagePath, $fileName);

            return response()->json(['message' => 'Imagen guardada exitosamente'], 200);
        }

        return response()->json(['message' => 'No se proporcionó ninguna imagen'], 400);
    }
    public function TypesCharts(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $resultados = DB::select("
           select types.name as nombre,count(guards.type_id) as total from guards 
           inner join types on guards.type_id = types.id
           group by guards.type_id;
       
       ");; // Cambiado a Map::all() para obtener todos los registros de la tabla Map 
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | lista de sitios.';
            $response->data["alert_text"] = "sitios encontrados";
            $response->data["result"] = $resultados;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function StatesCharts(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $resultados = DB::select("
           select states.name as nombre,count(guards.state_id) as total from guards 
           inner join states on states.id = guards.state_id
           group by guards.state_id;
       
       ");; // Cambiado a Map::all() para obtener todos los registros de la tabla Map 
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | lista de sitios.';
            $response->data["alert_text"] = "sitios encontrados";
            $response->data["result"] = $resultados;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function groupsCharts(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $resultados = DB::select("
           SELECT 
           users.group AS nombre,
           COUNT(guards.group) AS total
       FROM 
           users
       LEFT JOIN 
           guards
       ON 
           users.group = guards.group
       GROUP BY 
           users.group;
       
       
       ");; // Cambiado a Map::all() para obtener todos los registros de la tabla Map 
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | lista de sitios.';
            $response->data["alert_text"] = "sitios encontrados";
            $response->data["result"] = $resultados;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}
