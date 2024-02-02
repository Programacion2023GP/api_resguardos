<?php

namespace App\Http\Controllers;
use App\Models\User;
use WebSocket\Client;

use Illuminate\Support\Str;

use App\Events\Events;
use App\Models\ObjResponse;
use App\Models\Groupextuser;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ControllerUsers extends Controller
{
   
    public function emitirEvento()
    {
            $socket = new Client("ws://localhost:3001");

            $socket->send(json_encode(['type' => 'join', 'group' => 'Luisao',  'project' => 'administrativos','client'=>'channel1']));
            $socket->send(json_encode(['type' => 'message', 'group' => 'Luisao',  'project' => 'administrativos','client'=>'channel1','message'=>'HOLA, COMO ESTAS 2']));

            $response = $socket->receive();
            $responseData = json_decode($response, true);

            $socket->close();
            return response()->json(['status' => 'Mensaje recibido en Laravel']);

       
    }


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
       $query = User::select('users.*',
       DB::raw("GROUP_CONCAT(DISTINCT groupsextuser.group) as departamentos")
       )->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id')
       ->where("users.id",$user->id)
       ->groupBy('users.id')->orderBy('role')->get();
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
       $response->data["result"]["user"]= $query;
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

            $existingUser = User::where('payroll', $request->payroll)->first(); 
            if ($existingUser) {
                throw new \Exception('Ya existe un usuario con este número de nomina o correo.');
            }
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                throw new \Exception('Ya existe un usuario con este número de nomina o correo.');
            }

            $User=User::create([
               'email' => $request->email,
               'payroll' => $request->payroll,
               'name' => $request->name,
               'group' => Auth::user()->role == 1 || Auth::user()->role == 2
               ?$request->group : Auth::user()->group,
               'role' => $request->role,
               'user_create' => Auth::user()->id,
           ]);
           if ($request->has('groups') && is_array($request->groups) && count($request->groups) > 0) {
            foreach ($request->groups as $item) {
                Groupextuser::create([
                    'user_id' => $User->id,
                    'group' => $item['departamento']
                ]);
            }
        }
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | usuario registrado.';
           $response->data["alert_text"] = "Se ha creado correctamente el usuario";
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }

     public function changeAdmin(Request $request, Response $response)
     {
        $response->data = ObjResponse::DefaultResponse();
        try {

            $user = User::find($request->id);

            if ($user) {
                $user->update([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
          
            }
           $response->data = ObjResponse::CorrectResponse();
           $response->data["message"] = 'peticion satisfactoria | admin actualizado.';
           $response->data["alert_text"] = "admin actualizado";
        } catch (\Exception $ex) {
           $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
     }

     public function index(Response $response, $role = null)
{
    $response->data = ObjResponse::DefaultResponse();
    try {
      $query = null;


  ;
  if ($role == null) {

        switch(Auth::user()->role){
            case 1:
              $query = User::select('users.*',
                DB::raw("CASE
                    WHEN users.role = 1 THEN 'Super Admin'
                    WHEN users.role = 2 THEN 'Administrativo'
                    WHEN users.role = 3 THEN 'Enlance'
                    WHEN users.role = 4 THEN 'Empleado'
                END as type_role")
                        );
                $query = $query->orderBy('role')->where('active', 1)->get();

             break;
             case 2:
                $query = User::select('users.*',
            DB::raw("CASE
                WHEN users.role = 1 THEN 'Super Admin'
                WHEN users.role = 2 THEN 'Administrativo'
                WHEN users.role = 3 THEN 'Enlance'
                WHEN users.role = 4 THEN 'Empleado'
            END as type_role")
                    );
                $query->where(function($q) {
                    $q->where(function($q) {
                        $q->whereIn('role', [3,4]);
                    })->orWhere(function($q) {
                        $q->where('role', 2)
                            ->where('id', Auth::user()->id);
                    });
                });
                $query = $query->orderBy('role')->where('active', 1)->get();
             break;
             case 3:
                $userAuth = User::select('users.*',
                DB::raw("CASE
                    WHEN users.role = 1 THEN 'Super Admin'
                    WHEN users.role = 2 THEN 'Administrativo'
                    WHEN users.role = 3 THEN 'Enlance'
                    WHEN users.role = 4 THEN 'Empleado'
                END as type_role")
                        )->orderBy('role')->where('active', 1)->where('users.id', Auth::user()->id)->get();






                $query = User::select('users.*','usemp.id as identificador','usemp.name as nombre','usemp.payroll as nomina','usemp.email as correo',
                'usemp.active as activo','usemp.group as grupo','usemp.role as rol',
            DB::raw("CASE
                WHEN users.role = 1 THEN 'Super Admin'
                WHEN users.role = 2 THEN 'Administrativo'
                WHEN users.role = 3 THEN 'Enlance'
                WHEN users.role = 4 THEN 'Empleado'
            END as type_role")
         
                    );


                $query->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id')
                ->leftjoin('users as usemp', 'usemp.group', '=', 'groupsextuser.group');
                $query->where(function($q) {
                    $q->where(function($q) {
                        $q->whereIn('users.role', [4])
                        // ->where('user_create', Auth::user()->id)
                        ->where('users.group', Auth::user()->group);
                    })->orWhere(function($q) {
                        $q->where('users.role', 3)
                            ->where('users.id', Auth::user()->id);
                    })->orWhere(function($q) {
                        $q->where('groupsextuser.group', 'usemp.group');

                    });                    ;
                    
                });
                $query = $query->orderBy('users.role')->where('users.active', 1)->get();
                $newResult = [];
                foreach ($userAuth as $value) {
                    $modifiedValue = [
                        'id' =>  $value->id,
                        'email' =>  $value->email,
                        'payroll' =>  $value->payroll ,
                        'name' =>  $value->name ,
                        'group' =>  $value->group ,
                        'role' => $value->role ,
                        'active' => $value->active ,
                        'type_role' => $value->type_role,
                    ];
                
                    $newResult[] = (object)$modifiedValue;
                }

                foreach ($query as $value) {
                    if ($value->rol !=null) {
                        if ($value->rol<4 ) {
                            continue;
                        }
                    }
                    if ( Auth::user()->id == $value->id && $value->nombre == null) {
                        continue;
                    }
                    $modifiedValue = [
                        'id' => $value->nombre == null ? $value->id : $value->identificador,
                        'email' => $value->nombre == null ? $value->email : $value->correo,
                        'payroll' => $value->nombre == null ? $value->payroll : $value->nomina,
                        'name' => $value->nombre == null ? $value->name : $value->nombre,
                        'group' => $value->nombre == null ? $value->group : $value->grupo,
                        'role' => $value->nombre == null ? $value->role : 4,
                        'active' => $value->nombre == null ? $value->active : $value->activo,
                        'type_role' => $value->nombre == null ? $value->type_role : 'Empleado',
                    ];
                
                    $newResult[] = (object)$modifiedValue;
                }
                
                

                $query = $newResult;
            break;
            case 4:
                $query = User::select('users.*',
                DB::raw("CASE
                    WHEN users.role = 1 THEN 'Super Admin'
                    WHEN users.role = 2 THEN 'Administrativo'
                    WHEN users.role = 3 THEN 'Enlance'
                    WHEN users.role = 4 THEN 'Empleado'
                END as type_role")
                    );
                $query->where('role', 5);
                $query = $query->orderBy('role')->where('active', 1)->get();
            
            break;
        }
    }
        if ($role !== null) {

            $query->where('role', $role);
        }


        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Petición satisfactoria | Lista de usuarios.';
        $response->data["alert_text"] = "Usuarios encontrados";
        $response->data["result"] = $query;
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
public function group(Response $response, $group = null, $id = null)
{
    $response->data = ObjResponse::DefaultResponse();
    try {
        $list = User::where("group", $group)->where("role",4)
        ->whereNotIn("id", [$id])
        ->get();
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
        $list = null;




        if ($role == null) {
            switch(Auth::user()->role){
                case 1:
                    $query = User::select('users.*',

                    DB::raw("CASE
                        WHEN users.role = 1 THEN 'Super Admin'
                        WHEN users.role = 2 THEN 'Administrativo'
                        WHEN users.role = 3 THEN 'Enlace'
                        WHEN users.role = 4 THEN 'Empleado'
                        ELSE 'Otro'
                    END as type_role"),
                    DB::raw("GROUP_CONCAT(DISTINCT groupsextuser.group) as departamentos")
                    )->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id');
                    $query->where(function($q) {
                        $q->whereIn('role', [2,3, 4]);
                    });
                    $list = $query->groupBy('users.id')->orderBy('role')->get();

                    break;
                case 2:
                    $query = User::select('users.*',
                    DB::raw("CASE
                        WHEN users.role = 1 THEN 'Super Admin'
                        WHEN users.role = 2 THEN 'Administrativo'
                        WHEN users.role = 3 THEN 'Enlace'
                        WHEN users.role = 4 THEN 'Empleado'
                        ELSE 'Otro'
                    END as type_role"),
                    DB::raw("GROUP_CONCAT(DISTINCT groupsextuser.group) as departamentos")
                    )->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id');
                    $query->where(function($q) {
                        $q->whereIn('role', [3, 4]);
                    });
                    $list = $query->groupBy('users.id')->orderBy('role')->get();

                    break;
                case 3:
                    $query = User::select('users.*','usemp.id as identificador','usemp.name as nombre','usemp.payroll as nomina','usemp.email as correo',
                    'usemp.active as activo','usemp.group as grupo','usemp.role as rol',
                DB::raw("CASE
                    WHEN users.role = 1 THEN 'Super Admin'
                    WHEN users.role = 2 THEN 'Administrativo'
                    WHEN users.role = 3 THEN 'Enlance'
                    WHEN users.role = 4 THEN 'Empleado'
                END as type_role")
             
                        );
    
    
                    $query->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id')
                    ->leftjoin('users as usemp', 'usemp.group', '=', 'groupsextuser.group');
                    $query->where(function($q) {
                        $q->where(function($q) {
                            $q->whereIn('users.role', [4])
                            // ->where('user_create', Auth::user()->id)
                            ->where('users.group', Auth::user()->group);
                        })->orWhere(function($q) {
                            $q->where('users.role', 3)
                                ->where('users.id', Auth::user()->id);
                        })->orWhere(function($q) {
                            $q->where('groupsextuser.group', 'usemp.group')
                            
                            ;
    
                        });                    ;
                        
                    });
                    $query = $query->orderBy('users.role')->get();
                    $newResult = [];
                 
                    foreach ($query as $value) {
                        if ($value->rol !=null) {
                            if ($value->rol<4) {
                             continue;
                            }
                        }
                        if ( Auth::user()->id == $value->id && $value->nombre == null) {
                            continue;
                        }
                        $modifiedValue = [
                            'id' => $value->nombre == null ? $value->id : $value->identificador,
                            'email' => $value->nombre == null ? $value->email : $value->correo,
                            'payroll' => $value->nombre == null ? $value->payroll : $value->nomina,
                            'name' => $value->nombre == null ? $value->name : $value->nombre,
                            'group' => $value->nombre == null ? $value->group : $value->grupo,
                            'role' => $value->nombre == null ? $value->role : 4,
                            'active' => $value->nombre == null ? $value->active : $value->activo,
                            'type_role' => $value->nombre == null ? $value->type_role : 'Empleado',
                        ];
                    
                        $newResult[] = (object)$modifiedValue;
                    }
                    
                    
    
                    $list = $newResult;
                    break;
                case 4:
                    $query = User::select('users.*',
                    DB::raw("CASE
                        WHEN users.role = 1 THEN 'Super Admin'
                        WHEN users.role = 2 THEN 'Administrativo'
                        WHEN users.role = 3 THEN 'Enlace'
                        WHEN users.role = 4 THEN 'Empleado'
                        ELSE 'Otro'
                    END as type_role"),
                    DB::raw("GROUP_CONCAT(DISTINCT groupsextuser.group) as departamentos")
                    )->leftjoin('groupsextuser', 'groupsextuser.user_id', '=', 'users.id');
                    $query->where('role', 5);
                    $list = $query->groupBy('users.id')->orderBy('role')->get();

                    break;
            }
        } else {
            $query->where('role', $role);
        }

   
        $response->data = ObjResponse::CorrectResponse();
        $response->data["message"] = 'Petición satisfactoria | Lista de usuarios.';
        $response->data["alert_text"] = "Usuarios encontrados";
        $response->data["result"] = $list;
    } catch (\Exception $ex) {
        $response->data = ObjResponse::CatchResponse($ex->getMessage());
    }
    return response()->json($response, $response->data["status_code"]);
}

public function changeEnlance($usuarioAntiguoId, $usuarioNuevoId,$correonuevo)
{
    $usuarioAntiguo = User::find($usuarioAntiguoId);
    $usuarioNuevo = User::find($usuarioNuevoId);
    $usuarioAntiguoemail=$usuarioAntiguo->email;
    $usuarioNuevoemail=$usuarioNuevo->email;
    $usuarioAntiguorole=$usuarioAntiguo->role;
    $usuarioNuevorole=$usuarioNuevo->role;

    if ($usuarioAntiguo && $usuarioNuevo) {
        $correoTemporalNuevo = "tpm" . '@example.com';
        $correoTemporalAntiguo = "new". '@gmail.com';

        $usuarioNuevo->email = $correoTemporalNuevo;

        $usuarioAntiguo->email = $correoTemporalAntiguo;
       
        $usuarioNuevo->save();
        $usuarioAntiguo->save();

        $usuarioNuevo->email = $usuarioAntiguoemail;
        $usuarioAntiguo->email = $correonuevo;
        $usuarioNuevo->role = $usuarioAntiguorole;
        $usuarioAntiguo->role = $usuarioNuevorole;

        $usuarioNuevo->save();
        $usuarioAntiguo->save();

        return response()->json(['mensaje' => 'Correos y roles intercambiados con éxito'], 200);
    } else {
        return response()->json(['mensaje' => 'Usuarios no encontrados'], 404);
    }
}


     public function destroy(int $id, Response $response)
     {
         $response->data = ObjResponse::DefaultResponse();
         try {
            $affectedRows = User::where('id', $id)
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from('user_guards')
                    ->whereColumn('user_guards.user_id', 'users.id')
                    ->where('user_guards.active', 1)
                    ->orWhere('user_guards.expecting', 1)
                    ->whereNull('user_guards.deleted_at');
            })
           
            ->update([
                'active' => DB::raw('NOT active'),
            ]);

        if ($affectedRows === 0) {
            throw new \Exception('No se puede desactivar el resguardo asociado a un usuario activo.');
        }


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
                    $user->payroll = intval($request->payroll);
                }

                if ($user->name !== $request->name) {
                    $user->name = $request->name;
                }

                    $user->group =   Auth::user()->role == 1 || Auth::user()->role == 2
                    ?$request->group : Auth::user()->group;
                

                if ($user->role !== $request->role && $request->role) {
                    $user->role = $request->role;
                }
                Groupextuser::where("user_id",$request->id)->delete();
                if ($request->has('groups') && is_array($request->groups) && count($request->groups) > 0) {
                    foreach ($request->groups as $item) {
                        Groupextuser::create([
                            'user_id' => $request->id,
                            'group' => $item['departamento']
                        ]);
                    }
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
