<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request)
    {
        if (!$this->validarEmail($request->email)) {
            return ['estado' => 'failed', 'mensaje' => 'El email ingresado no se encuentra en nuestra base de datos.'];
        }
        $this->send($request->email);
        return ['estado' => 'success', 'mensaje' => 'Se ha enviado el email, revise su correo.'];
    }
    public function send($email)
    {
        $token = $this->crearToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }
    public function crearToken($email)
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if ($oldToken) {
            return $oldToken->token;
        }
        $token = Str::random(60);
        $this->saveToken($token, $email);
        return $token;
    }
    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
        'email' => $email,
        'token' => $token,
        'created_at' => Carbon::now()
        ]);
    }
    public function validarEmail($email)
    {
        return !!User::where('email', $email)->first();
    }
    //SETEO DE LA NUEVA PASS DESPUES DE RECIBIR EL CORREO
    public function process(Request $request)
    {
        return $this->getPasswordResetTableRow($request)->count()>0 ? $this->changePassword($request) : $this->tokenNotFoundResponse();
    }
    private function getPasswordResetTableRow($request)
    {
        return DB::table('password_resets')->where(['email' => $request->email, 'token'=> $request->resetToken]);
    }
    private function tokenNotFoundResponse()
    {
        return ['estado' => 'failed','mensaje' => 'Token o email incorrecto'];
    }
    private function changePassword($request)
    {
        $user = User::where('email', $request->email)->first();
        $user->update(['password'=>bcrypt($request->password)]);
        $this->getPasswordResetTableRow($request)->delete();
        return ['estado' => 'success','mensaje'=>'Contraseña Cambiada Correctamente'];
    }
}
