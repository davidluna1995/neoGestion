<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterFormRequest;

class APIController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->save();
        return response([
            'status' => 'success',
            'data' => $user
           ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (! $token = JWTAuth::attempt($credentials)) {
            return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'msg' => 'Error, Correo y/o contraseña incorrecto.'
                ], 400);
        }
        return response([
                'status' => 'success',
            ])
            ->header('Authorization', $token);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        return response([
                'status' => 'success',
                'data' => $user
            ]);
    }
    public function refresh()
    {
        return response([
                'status' => 'success'
            ]);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response([
                'status' => 'success',
                'msg' => 'Logged out Successfully.'
            ], 200);
    }

    public function allUser()
    {
        $listar = DB::table('users')->count();
        if ($listar > 0) {
            return $listar;
        } else {
            return 0;
        }
    }

    protected function validar_perfil($request)
    {
        switch ($request->campo) {
        case 'name':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required',
            ],
              [
              'input.required' => 'Debe ingresar un nombre de usuario.',
            ]
          );
          break;
        case 'email':
          $validator = Validator::make(
              $request->all(),
              [
              'input' => 'required|email',
            ],
              [
              'input.required' => 'Debe ingresar un correo electronico.',
              'input.email' => 'Debe ingresar un correo electronico que sea valido.',
            ]
          );
          break;

        default:
        return null;
          break;
      }
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function modificar_perfil(Request $request)
    {
        $validarDatos = $this->validar_perfil($request);
       
        
        if ($validarDatos['estado'] == 'success') {
            $idUser = Auth::user()->id;
            $modificar = User::find($idUser);

            if (!is_null($modificar)) {
                switch ($request->campo) {

                case 'name':

                  $validar = strtolower($request->input);

                        $modificar->name = $validar;

                        if ($modificar->save()) {
                            return ['estado'=>'success', 'mensaje'=>'Nombre de usuario actualizado, por seguridad la sesión cerrará automaticamente.'];
                        } else {
                            return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                        }
                    
                  break;
                  
                case 'email':

                  $validar = strtolower($request->input);
                  $verificarEmail = User::select([
                    'email',
                ])
                                        ->where('email', $validar)
                                        ->get();
                    if (count($verificarEmail) > 0) {
                        return ['estado'=>'failed', 'mensaje'=>'El correo ya existe en la base de datos.'];
                    }

                        $modificar->email = $validar;

                        if ($modificar->save()) {
                            return ['estado'=>'success', 'mensaje'=>'Correo electronico actualizado, por seguridad la sesión cerrará automaticamente.'];
                        } else {
                            return ['estado'=>'failed', 'mensaje'=>'A ocurrido un error al igreso de datos.'];
                        }
                    
                  break;
     
                default:
                  return null;
                  break;
              }
            }
            return ['estado'=>'failed', 'mensaje'=>'El item que intentas modificar no existe.'];
        }
        return $validarDatos;
    }
    
    protected function cambiar_password(Request $request)
    {
        $idUser = Auth::user()->id;
        $user = User::find($idUser);

        if (!is_null($user)) {
            if (Hash::check($request->password_actual, $user->password)) {
                $user->password = bcrypt($request->password_nueva);

                if ($user->save()) {
                    return ['estado' => 'success', 'mensaje' => 'Contraseña actualizada correctamente, por seguridad la sesión cerrará automaticamente.'];
                } else {
                    return ['estado' => 'failed', 'mensaje' => 'Se ha producido un error al actualizar la contraseña.'];
                }
            } else {
                return ['estado' => 'failed', 'mensaje' => 'Contraseña actual ingresada incorrectamente.'];
            }
        }
    }

    public function validar_usuario($datos)
    {
        $validator = Validator::make(
            $datos->all(),
            [
                'email' => 'required|unique:users',
                'name' => 'required',
                'password' => 'required|min:6',
                'rol' => 'required',
            ],
            [
                'email.required' => 'El correo a ingresar es necesario',
                'email.unique' => 'El correo ingresado ya existe en nuestros registros.',
                'name.required' => 'El nombre de usuario a ingresar es necesario.',
                'password.required' => 'La contraseña a ingresar es necesaria.',
                'password.min' => 'La contraseña debe tener un minmo de 6 caracteres.',
                'rol.required' => 'Debe seleccionar un tipo de usuario.',
            ]
        );

 
        if ($validator->fails()) {
            return ['estado' => 'failed_v', 'mensaje' => $validator->errors()];
        }
        return ['estado' => 'success', 'mensaje' => 'success'];
    }

    protected function crear_usuario(Request $request)
    {
        $validarDatos = $this->validar_usuario($request);
        if ($validarDatos['estado'] == 'success') {
            if ($request->password == $request->passRepetir) {
                $user = new User;
                $user->email = $request->email;
                $user->name = $request->name;
                $user->rol = $request->rol;
                $user->password = bcrypt($request->password);
            } else {
                return ['estado' => 'errorPassword', 'mensaje' => 'Las contraseñas no coinciden.'];
            }
        
            if ($user->save()) {
                return ['estado' => 'success', 'mensaje' => 'Usuario creado correctamente.'];
            } else {
                return ['estado' => 'failed', 'mensaje' => 'Se ha producido un error al crear el usuario.'];
            }
        }
        return $validarDatos;
    }

    protected function traer_usuarios()
    {
        return User::withTrashed()->get();
    }

    public function delete_usuario(Request $request)
    {
        if ($request->id != Auth::user()->id) {
            if ($request->metodo == 'true') {
                $evento = User::find($request->id)->delete();
                if ($evento) {
                    return ['estado' => 'success', 'mensaje' => 'Usuario Bloqueado Correctamente.'];
                } else {
                    return ['estado' => 'failed', 'mensaje' => 'Se ha producido un error al bloquear al usuario.'];
                }
            } else {
                $evento = User::withTrashed()
                    ->where('id', $request->id)
                    ->restore();
                if ($evento) {
                    return ['estado' => 'success', 'mensaje' => 'Usuario Desbloqueado Correctamente.'];
                } else {
                    return ['estado' => 'failed', 'mensaje' => 'Se ha producido un error al Desbloquear al usuario.'];
                }
            }
        } else {
            return ['estado' => 'failed', 'mensaje' => 'No se puede bloquear a si mismo.'];
        }
    }
        
}
