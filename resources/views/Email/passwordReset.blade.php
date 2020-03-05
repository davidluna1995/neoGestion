@component('mail::message')
# Solicitud de cambio de contraseña

Si usted no ha solicitado el cambio de contraseña puede omitir este mensaje.

@component('mail::button', ['url' => 'http://127.0.0.1:8000/#/resetearPassword/'.$token])
Reiniciar Contraseña
@endcomponent

Gracias,<br>
 Equipo de Neofox. 
@endcomponent
