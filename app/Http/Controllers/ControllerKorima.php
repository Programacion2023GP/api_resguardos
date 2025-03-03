<?php

namespace App\Http\Controllers;

use App\Models\Korima;
use App\Models\ObjResponse;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                $archivo->storeAs("public/Korima/{$request->payroll}/{$request->korima}", $nuevoNombreArchivo);

                // Genera la URL correcta con `storage/`
                $guard->picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");

                // $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);

                // // $guard->picture = asset("Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
                // $guard->picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
                // Mueve el archivo al directorio público
                // Mueve el archivo a la ruta deseada
                //                 $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);
                //  // $guard->picture = "http://localhost:8000/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";
                // Guarda el valor de 'korima' en el objeto $guard

                // Construye la URL completa de la imagen
                // $guard->picture = "http://localhost:8000/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";

                // O si estás usando el dominio de producción
                // $guard->picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";


                // Almacena la observación en el modelo


                // Guarda los datos en la base de datos

                // Respuesta correcta

            }
            if ($request->hasFile("tag_picture")) {
                $archivo = $request->file("tag_picture");

                // Obtén el nombre original del archivo y genera un nuevo nombre
                $nombreArchivo = $archivo->getClientOriginalName();
                $nuevoNombreArchivo = date('Y-m-d_H-i-s') . '_' . $nombreArchivo;

                // Mueve el archivo al directorio público
                // Mueve el archivo a la ruta deseada
                // $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);
                // $guard->tag_picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
                $archivo->storeAs("public/Korima/{$request->payroll}/{$request->korima}", $nuevoNombreArchivo);

                // Genera la URL correcta con `storage/`
                $guard->tag_picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");

                // Guarda la URL completa de la imagen en el objeto $guard
                // $guard->tag_picture = asset("Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
                // $archivo->move(public_path("Korima/{$request->payroll}/{$request->korima}"), $nuevoNombreArchivo);

                // // Guarda el valor de 'korima' en el objeto $guard

                // // Construye la URL completa de la imagen
                // // $guard->tag_picture = "http://localhost:8000/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";

                // O si estás usando el dominio de producción
                // $guard->tag_picture = "https://api.resguardosinternos.gomezpalacio.gob.mx/public/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}";
            }

            $guard->korima = $request->korima;
            $guard->motivetransfer = Null;

            $guard->observation = $request->observation ?? $guard->observation;
            if ($request->hasFile("picture") || $request->hasFile("tag_picture") || $request->observation) {
                $guard->save();
                $response = ObjResponse::CorrectResponse();
                $response["message"] = 'Archivo subido exitosamente';
                $response["alert_text"] = "El archivo se ha subido correctamente";
            } else {
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
            ->leftjoin('users', 'users.id', 'korima.user_id')            //     ->where('payroll', $request->payroll)
                ->where('active', 1)
                ->select('korima.*','users.name','users.group')

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

    public function update(Request $request)
    {
        $response = ObjResponse::DefaultResponse();

        try {
            // Busca el registro existente en la base de datos
            $guard = Korima::find($request->id);
            if (!$guard) {
                return response()->json(ObjResponse::CatchResponse("No se encontró el registro con el ID proporcionado."), 404);
            }

            // Verifica si hay un archivo en la solicitud con el campo "picture"
            if ($request->hasFile("picture")) {
                $archivo = $request->file("picture");

                // Obtén el nombre original del archivo y genera un nuevo nombre
                $nombreArchivo = $archivo->getClientOriginalName();
                $nuevoNombreArchivo = now()->format('Y-m-d_H-i-s') . '_' . $nombreArchivo;

                // Almacena el archivo en `storage/app/public/Korima/...`
                $archivo->storeAs("public/Korima/{$request->payroll}/{$request->korima}", $nuevoNombreArchivo);

                // Genera la URL pública
                $guard->picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
            }

            // Verifica si hay un archivo en la solicitud con el campo "tag_picture"
            if ($request->hasFile("tag_picture")) {
                $archivo = $request->file("tag_picture");

                // Obtén el nombre original del archivo y genera un nuevo nombre
                $nombreArchivo = $archivo->getClientOriginalName();
                $nuevoNombreArchivo = now()->format('Y-m-d_H-i-s') . '_' . $nombreArchivo;

                // Almacena el archivo en `storage/app/public/Korima/...`
                $archivo->storeAs("public/Korima/{$request->payroll}/{$request->korima}", $nuevoNombreArchivo);

                // Genera la URL pública
                $guard->tag_picture = url("storage/Korima/{$request->payroll}/{$request->korima}/{$nuevoNombreArchivo}");
            }

            // Verifica y actualiza los campos 'korima' y 'observation' si están presentes en la solicitud
            if ($request->has("korima")) {
                $guard->korima = $request->korima;
            }

            if ($request->has("observation")) {
                $guard->observation = $request->observation;
            }

            // Verifica si al menos un campo se ha modificado antes de actualizar
            if ($request->hasFile("picture") || $request->hasFile("tag_picture") || $request->has("korima") || $request->has("observation")) {
                $guard->save();
                $response = ObjResponse::CorrectResponse();
                $response["message"] = 'Datos actualizados exitosamente';
                $response["alert_text"] = "El registro se ha actualizado correctamente";
            } else {
                $response = ObjResponse::CatchResponse("No se ha proporcionado ninguna imagen ni cambios en los datos.");
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
            // Manejo de errores y excepciones
            Log::info('Error en update: ' . $ex->getMessage(), [
                'line' => $ex->getLine(),
                'file' => $ex->getFile(),
                'trace' => $ex->getTraceAsString()
            ]);
            $response = ObjResponse::CatchResponse("Error al actualizar el registro. " . $ex->getMessage());
        }

        // Devuelve la respuesta como JSON
        return response()->json($response, $response["status_code"]);
    }

    public function down(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $korima = Korima::find($request->id);
            if ($korima) {
                $korima->motive_down = $request->motive_down;
                $korima->update();
            } else {
                $korima = new Korima();
                $korima->korima = $request->korima;
                $korima->motive_down = $request->motive_down;
                $korima->save();
            }

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | resguardo activo dado de baja.';
            $response->data["alert_text"] = 'resguardo activo dado de baja';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function transfer(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        $user = User::where('id', $request->value)->first();
        try {
            $korima = Korima::find($request->id);
            if ($korima) {
                $korima->trauser_id = $user->id;
                $korima->motive_down = 'transferencia de resguardo a ' . $user->name;
                $korima->user_id = $request->id;

                $korima->motivetransfer = $request->motivetransfer;


                $korima->update();
            } else {
                $korima = new Korima();
                $korima->korima = $request->NumeroEconomicoKorima;
                $korima->trauser_id = $user->id;
                $korima->motive_down = 'transferencia de resguardo a ' . $user->name;
                $korima->user_id = $request->id;
                $korima->motivetransfer = $request->motivetransfer;

                $korima->save();
            }

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | transferencia.';
            $response->data["alert_text"] = 'transferencia';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    public function autorized(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $korima = Korima::find($request->id);
            $admin = Auth::user()->role == 1 || Auth::user()->role == 2 ? true : false;
            $korima->archivist = $admin ? 1 : null;
            if ($korima) {
                if (!$admin) {
                    $korima->autorized = $request->option == 1 ? true : null;
                }
                if ($request->option == 0) {
                    $korima->archivist = $admin ? 0 : null;
                    if (!$admin) {
                        # code...
                        $korima->motive_down = null;
                        $korima->trauser_id = null;
                        $korima->motivetransfer = null;
                    }
                    $korima->motivearchivist = $admin ? $request->motivearchivist : null;
                }
                if ($admin) {
                    $korima->timestamps = false; // 🔥 Evita que se actualice updated_at
                }
    
                $korima->save();
            }

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | resguardo activo dado de baja.';
            $response->data["alert_text"] = 'resguardo activo dado de baja';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}
