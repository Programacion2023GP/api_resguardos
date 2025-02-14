<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;

use App\Http\Controllers;
use App\Models\ObjResponse;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControllerStock extends Controller
{
    public function create(Request $request, Response $response)
{
    $data = $request->all();
    $response->data = ObjResponse::DefaultResponse();

    try {
        $guard = new Stock();

        if ($request->hasFile("picture")) {
            $archivo = $request->file("picture");

            // Verifica que el archivo sea válido
            if (!$archivo->isValid()) {
                return response()->json(["error" => "El archivo no es válido"], 400);
            }

            // Asegura que la carpeta Stock/ existe
            $path = 'public/Stock'; // El directorio dentro de storage/app/public
            if (!file_exists(storage_path($path))) {
                mkdir(storage_path($path), 0777, true);
            }
            
            // Guarda el archivo con un nombre único utilizando storeAs
            $nombreArchivo = date('Y-m-d_H-i-s') . '_' . $archivo->getClientOriginalName();
            $archivo->storeAs($path, $nombreArchivo);
            
            // Guarda la URL accesible en la base de datos usando 'storage/'
            $guard->picture = url("storage/Stock/{$nombreArchivo}");
            
        }

        // Obtener el último stock_number y generar el nuevo
        $ultimoGuard = Stock::orderBy('id', 'desc')->first();
        $nuevoStockNumber = $ultimoGuard ? 'S-' . (intval(substr($ultimoGuard->stock_number, 2)) + 1) : 'S-1';

        $guard->stock_number = $nuevoStockNumber;
        $guard->type_id = $request->input("type_id");
        $guard->number_korima = $request->input("number_korima");
        $guard->description = $request->input("description");
        $guard->brand = $request->input("brand");
        $guard->state_id = $request->input("state_id");
        $guard->serial = $request->input("serial");
        $guard->group = Auth::user()->role == 3 ? Auth::user()->group : $request->input("group");
        $guard->observations = $request->input("observations", null);

        $guard->save();

        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Archivos subidos exitosamente';
        $response->data["alert_text"] = "Archivos subidos";
    } catch (\Exception $ex) {
        $response->data = ObjResponse::CatchResponse($ex->getMessage());
    }

    return response()->json($response, $response->data["status_code"]);
}

    public function index(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = Stock::select(
                'stock.*',
                'types.name as Tipo',
                'states.name as Estado',
            
            )->orderBy('stock.id', 'desc')
                ->leftjoin('types', 'types.id', '=', 'stock.type_id')
                ->leftjoin('states', 'states.id', '=', 'stock.state_id')
                ->groupBy('stock.id', 'types.name', 'states.name');
            switch (Auth::user()->role) {
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    $list->where('stock.group', Auth::user()->group);

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
    public function update(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $guard = Stock::find($request->id);
            if ($request->file("picture")) {

                $archivo = $request->file("picture");

                // Genera el nombre único para el archivo
                $nombreArchivo = $archivo->getClientOriginalName();
                $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;
                
                // Usa storeAs() para guardar el archivo en storage/app/public
                $archivo->storeAs('public/Resguardos', $nuevoNombreArchivo);
                
                // Genera la URL accesible usando storage
                $guard->picture = url("storage/Resguardos/{$nuevoNombreArchivo}");
                
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
    public function destroy(int $id, Response $response, Request $request)
    {
        $response->data = ObjResponse::DefaultResponse();
        
        try {
            // Obtener el estado actual del resguardo
            $currentStock = DB::table('stock')->where('id', $id)->first();
            // Verificar si el registro existe
            if (!$currentStock) {
                throw new \Exception('El resguardo no existe.');
            }
    
            // Alternar el estado de `active` (0 -> 1, 1 -> 0)
            $newActiveStatus = $currentStock->active == 0 ? 1 : 0;
    
            $affectedRows = DB::table('stock')
                ->where('id', $id)
                ->update(['active' => $newActiveStatus]);
    
            if ($affectedRows === 0) {
                throw new \Exception('No se pudo actualizar el estado del resguardo.');
            }
    
            // Definir el mensaje basado en el nuevo estado
            $message = $newActiveStatus == 1 ? 'Resguardo activado.' : 'Resguardo desactivado.';
            
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = "Petición satisfactoria | " . $message;
            $response->data["alert_text"] = $message;
    
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
    
        return response()->json($response, $response->data["status_code"]);
    }
    
}
