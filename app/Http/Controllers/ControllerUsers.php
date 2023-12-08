<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjResponse;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
  
           User::create([
               'email' => $request->email,
               'payroll' => $request->payroll,
               'name' => $request->name,
               'group' => $request->group,
               'role' => $request->role,

           ]);
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | usuario registrado.';
           $response->data["alert_text"] = "Se ha creado correctamente el usuario";
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }
     public function index(Response $response, $role = null)
{
    $response->data = ObjResponse::DefaultResponse();
    try {
      $query = User::select('users.*',
      DB::raw("CASE 
          WHEN users.role = 1 THEN 'Super Admin'
          WHEN users.role = 2 THEN 'Administrativo'
          WHEN users.role = 3 THEN 'Jefe de departamento'
          WHEN users.role = 4 THEN 'Empleado'
      END as type_role")
  );    
  if ($role == null) {

        switch(Auth::user()->role){
            case 1:
              
             break;
             case 2:
                $query->where(function($q) {
                    $q->where(function($q) {
                        $q->whereIn('role', [ 3, 4]);
                    })->orWhere(function($q) {
                        $q->where('role', 2)
                            ->where('id', Auth::user()->id);
                    });
                });
             break;
             case 3:
                $query->where(function($q) {
                    $q->where(function($q) {
                        $q->whereIn('role', [4])
                            ->where('group', Auth::user()->group);
                    })->orWhere(function($q) {
                        $q->where('role', 3)
                            ->where('id', Auth::user()->id);
                    });
                });
                
            break;
            case 4:
                $query->where('role', 5);

            break;
        }
    }
        if ($role !== null) {
           
            $query->where('role', $role);
        }

        $list = $query->orderBy('role')->get();

        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Petición satisfactoria | Lista de usuarios.';
        $response->data["alert_text"] = "Usuarios encontrados";
        $response->data["result"] = $list;
    } catch (\Exception $ex) {
        $response->data = ObjResponse::CatchResponse($ex->getMessage());
    }
    return response()->json($response, $response->data["status_code"]);
}
public function user(Response $response, $id = null)
{
    $response->data = ObjResponse::DefaultResponse();
    try {
        $list = User::find($id);

        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Petición satisfactoria | Lista de usuarios.';
        $response->data["alert_text"] = "Usuarios encontrados";
        $response->data["result"] = $list;
    } catch (\Exception $ex) {
        $response->data = ObjResponse::CatchResponse($ex->getMessage());
    }
    return response()->json($response, $response->data["status_code"]);
}
public function reportsUsers(Response $response, $role = null)
{
    $response->data = ObjResponse::DefaultResponse();
    try {
        $query = User::select('users.*',
            DB::raw("CASE 
                WHEN users.role = 1 THEN 'Super Admin'
                WHEN users.role = 2 THEN 'Administrativo'
                WHEN users.role = 3 THEN 'Jefe de departamento'
                WHEN users.role = 4 THEN 'Empleado'
                ELSE 'Otro'
            END as type_role")
        );    

        if ($role == null) {
            switch(Auth::user()->role){
                case 1:
                    $query->whereIn('role', [2, 3, 4]);
                    break;
                case 2:
                    $query->whereIn('role', [3, 4]);
                    break;
                case 3:
                    $query->whereIn('role', [ 4])
                    ->where('group', Auth::user()->group);
                    ;
                    break;
                case 4:
                    $query->where('role', 5);
                    break;
            }
        } else {
            $query->where('role', $role);
        }

        $list = $query->orderBy('role')->get();

        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Petición satisfactoria | Lista de usuarios.';
        $response->data["alert_text"] = "Usuarios encontrados";
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
            $user = User::find($request->id);
            if ($user) {
                if ($user->email !== $request->email) {
                    $user->email = $request->email;
                }
                
                if ($user->payroll !== intval($request->payroll)) {
                    return "entreee";
                    $user->payroll = intval($request->payroll);
                }
                
                if ($user->name !== $request->name) {
                    $user->name = $request->name;
                }
                
                if ($user->group !== $request->group) {
                    $user->group = $request->group;
                }
                
                if ($user->role !== $request->role) {
                    $user->role = $request->role;
                }
            
                $user->save();
            
                // Haz algo después de la actualización, si es necesario
            } else {
                // Manejo si el usuario no existe
            }
            
             $response->data = ObjResponse::CorrectResponse();
             $response->data["message"] = 'peticion satisfactoria | programa de ejes actualizada.';
             $response->data["alert_text"] = 'Programa de eje actualizado';
 
         } catch (\Exception $ex) {
             $response->data = ObjResponse::CatchResponse($ex->getMessage());
         }
         return response()->json($response, $response->data["status_code"]);
     }
}
