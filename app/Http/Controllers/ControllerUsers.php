<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjResponse;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class ControllerUsers extends Controller
{
    public function login(Request $request, Response $response)
    {
       $field = 'username';
       $value = $request->username;
       if ($request->email) {
          $field = 'email';
          $value = $request->email;
       }
 
       $request->validate([
          $field => 'required',
          'password' => 'required'
       ]);
       $user = User::where("$field", "$value")->where("active",1)->first();
 
 
       if (!$user || !Hash::check($request->password, $user->password)) {
 
          throw ValidationException::withMessages([
             'message' => 'Credenciales incorrectas',
             'alert_title' => 'Credenciales incorrectas',
             'alert_text' => 'Credenciales incorrectas',
             'alert_icon' => 'error',
          ]);
       }
       $token = $user->createToken($user->email)->plainTextToken;
       $response->data = ObjResponse::CorrectResponse();
       $response->data["message"] = 'peticion satisfactoria | usuario logeado.';
       $response->data["result"]["token"] = $token;
       $response->data["result"]["user"]= $user;
       return response()->json($response, $response->data["status_code"]);
    }
    public function logout( Response $response)
    {
        try {
          //  DB::table('personal_access_tokens')->where('tokenable_id', $id)->delete();
          auth()->user()->tokens()->delete();
  
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | sesión cerrada.';
           $response->data["alert_title"] = "Bye!";
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }
     public function signup(Request $request, Response $response)
     {
        $response->data = ObjResponse::DefaultResponse();
        try {
  
           // if (!$this->validateAvailability('username',$request->username)->status) return;
  
           $new_user = User::create([
              'email' => $request->email,
              'group' => $request->group,
              'password' => Hash::make($request->password),
              'role' => $request->role,

           ]);
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | usuario registrado.';
           $response->data["alert_text"] = "¡Felicidades! ya eres parte de la familia";
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
           $list = User::
             where('role',1)
            ->orderBy('users.id', 'desc')
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
             User::where('id', $id)
             ->update([
                'active' => DB::raw('NOT active'),
                //  'deleted_at' => date('Y-m-d H:i:s'),
             ]);
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | usuario desactivado.';
             $response->data["alert_text"] ='Usuario desactivado';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
     public function update(Request $request, Response $response)
     {
         $response->data = ObjResponse::DefaultResponse();
         try {
               User::where('id', $request->id)
             ->update([
                 'email'=> $request->email,
                 'group' => $request->group,
                ]);
 
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | programa de ejes actualizada.';
             $response->data["alert_text"] = 'Programa de eje actualizado';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}
