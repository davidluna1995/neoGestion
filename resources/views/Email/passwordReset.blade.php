@component('mail::message')
# Solicitud de cambio de contraseña

Si usted no ha solicitado el cambio de contraseña puede omitir este mensaje.

@component('mail::button', ['url' => 'http://gestion.neodev.cl//#/resetearPassword/'.$token])
Reiniciar Contraseña
@endcomponent

Gracias,<br>
 Equipo de Neofox. 
@endcomponent
