<?php

namespace App\Http\Controllers;

use App\Models\Korima;
use App\Models\ObjResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ControllerKorima extends Controller
{
    public function create(Request $request)
    {
        // Inicializa la estructura de la respuesta
        $response = ObjResponse::DefaultResponse();

        try {
            // Instancia el modelo Korima
            $guard = new Korima();

            // Verifica si hay un archivo en la solicitud con el campo "picture"
            if ($request->hasFile("picture")) {
                $archivo = $request->file("picture");
                // Obtén el nombre original del archivo y genera un nuevo nombre
                $nombreArchivo = $archivo->getClientOriginalName();
                $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;

                // Mueve el archivo al directorio público
                // Mueve el archivo a la ruta deseada
                $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);

                // Guarda el valor de 'korima' en el objeto $guard
                
                // Construye la URL completa de la imagen
                // $guard->picture = "http://localhost:8000/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";

                // O si estás usando el dominio de producción
                $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";
                

                // Almacena la observación en el modelo
                

                // Guarda los datos en la base de datos
                
                // Respuesta correcta
                $response = ObjResponse::CorrectResponse();
                $response["message"] = 'Archivo subido exitosamente';
                $response["alert_text"] = "El archivo se ha subido correctamente";
            } 
            $guard->korima = $request->korima;
            $guard->observation = $request->observation;
            if ($request->hasFile("picture") || $request->observation) {
                $guard->save();
            }
            
            
            else {
                // Si no se proporciona el archivo
                $response = ObjResponse::CatchResponse("No se ha proporcionado ninguna imagen.");
            }
        } catch (\Exception $ex) {
            // Manejo de errores y excepciones
            $response = ObjResponse::CatchResponse($ex->getMessage());
        }

        // Devuelve la respuesta como JSON
        return response()->json($response, $response["status_code"]);
    }


    public function index(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            // $list = DB::select('SELECT * FROM users where active = 1');
            // User::on('mysql_gp_center')->get();
            $list = Korima::orderBy('id', 'desc')
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



            $affectedRows = Korima::where('id', $id)
                ->where(function ($query) use ($id) {
                    $query->whereNotExists(function ($subquery) use ($id) {
                        $subquery->select(DB::raw(1))
                            ->from('guards')
                            ->whereRaw('guards.state_id = Korima.id')
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
            $response->data["alert_text"] = 'resguardo baja';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function update(Request $request)
    {
        $response = ObjResponse::DefaultResponse();

        try {
            // Busca el registro existente en la base de datos
            $guard = Korima::find($request->id);
            if (!$guard) {
                // Si no se encuentra el registro, responde con un error
                $response = ObjResponse::CatchResponse("No se encontró el registro con el ID proporcionado.");
            } else {
                // Verifica si hay un archivo en la solicitud con el campo "picture"
                if ($request->hasFile("picture")) {
                    $archivo = $request->file("picture");

                    // Obtén el nombre original del archivo y genera un nuevo nombre
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;

                    // Mueve el archivo al directorio público
                    // Mueve el archivo a la ruta deseada
                    $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);

                    // Guarda el valor de 'korima' en el objeto $guard
                    $guard->korima = $request->korima;

                    // Construye la URL completa de la imagen
                    // $guard->picture = "http://localhost:8000/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";

                    // O si estás usando el dominio de producción
                    $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";

                }

                // Verifica y actualiza los campos 'korima' y 'observation' si están presentes en la solicitud
             
                $guard->korima = $request->korima;
                $guard->observation = $request->observation;
                if ($guard->picture != null || $guard->korima!= null) {
                    return $guard;
                    // Si al menos uno de los campos tiene un valor, actualiza el registro
                    $guard->update();                
                } 
                
                
                
                else {
                    // Si no se proporciona el archivo
                    $response = ObjResponse::CatchResponse("No se ha proporcionado ninguna imagen.");
                }
                // Guarda los cambios en la base de datos

                // Respuesta correcta
                $response = ObjResponse::CorrectResponse();
                $response["message"] = 'Datos actualizados exitosamente';
                $response["alert_text"] = "El registro se ha actualizado correctamente";
            }
        } catch (\Exception $ex) {
            // Manejo de errores y excepciones
            $response = ObjResponse::CatchResponse($ex->getMessage());
        }

        // Devuelve la respuesta como JSON
        return response()->json($response, $response["status_code"]);
    }
}
