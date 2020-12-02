<?php
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

use Illuminate\Http\Request;

Route::post('auth/login', 'APIController@login');
Route::post("json", function(){
    return "kalida";
});
Route::post('register', 'APIController@register');
//Rutas para el reinicio de password
Route::post('sendEmail','ResetPasswordController@sendEmail');
Route::post('resetPassword','ResetPasswordController@process');


Route::get('users/autocomplete/{q}','ProductoController@filter');

Route::group(['middleware' => 'auth.jwt'], function () {

    //login y user
    Route::get('auth/user', 'APIController@user');
    Route::post('auth/logout', 'APIController@logout');
    Route::get('allUser', 'APIController@allUser');
    Route::post('modificar_perfil', 'APIController@modificar_perfil');
    Route::post('cambiar_password', 'APIController@cambiar_password');
    Route::post('crear_usuario', 'APIController@crear_usuario');
    Route::get('traer_usuarios', 'APIController@traer_usuarios');
    Route::post('delete_usuario', 'APIController@delete_usuario');

    // rutas categoria
    Route::post('registro_categoria', 'CategoriaController@registro_categoria');
    Route::get('traer_categorias', 'CategoriaController@traer_categorias');
    Route::post('modificar_campo_categoria', 'CategoriaController@modificar_campo_categoria');
    Route::get('buscar_categoria/{categoria}', 'CategoriaController@buscar_categoria');
    Route::get('cantidad_categoria', 'CategoriaController@cantidad_categoria');
    Route::get('cantidad_productos_categoria', 'CategoriaController@cantidad_productos_categoria');
    Route::get('productos_menos_categoria', 'CategoriaController@productos_menos_categoria');

    // rutas producto
    Route::post('registro_producto', 'ProductoController@registro_producto');
    Route::get('traer_productos', 'ProductoController@traer_productos');
    Route::post('modificar_campo_producto', 'ProductoController@modificar_campo_producto');
    Route::get('cantidad_productos', 'ProductoController@cantidad_productos');
    Route::get('buscar_producto/{producto}', 'ProductoController@buscar_producto');
    Route::get('inhabilitar_producto/{producto}', 'ProductoController@inhabilitar_producto');

    Route::post('subir_imagen','ProductoController@subir_imagen');

    // rutas ventas
    Route::post('registro_venta', 'VentasController@registro_venta');
    Route::get('traer_ventas', 'VentasController@traer_ventas');
    Route::get('buscar_venta_producto/{producto}', 'VentasController@buscar_venta_producto');
    Route::get('total_ventas', 'VentasController@total_ventas');
    Route::get('ultimas_ventas', 'VentasController@ultimas_ventas');
    Route::get('mas_vendidos', 'VentasController@mas_vendidos');
    Route::get('buscar_producto_carro/{producto}', 'VentasController@buscar_producto_carro');
    Route::get('traer_detalle_venta/{idVenta}', 'VentasController@traer_detalle_venta');
    Route::get('mas_vendidos_grafico', 'VentasController@mas_vendidos_grafico');
    Route::get('ultimas_ventas_grafico', 'VentasController@ultimas_ventas_grafico');
    Route::get('menos_vendidos_grafico', 'VentasController@menos_vendidos_grafico');
    Route::get('reporte_ventas/{desde?}/{hasta?}', 'VentasController@reporte_ventas');
    Route::get('periodico_ventas_grafico/{anio}', 'VentasController@periodico_ventas_grafico');

    Route::get('cambiar_tipo_precio/{tipo_precio}', 'VentasController@cambiar_tipo_precio');
    Route::get('generar_un_xml','VentasController@generar_un_xml');

    //rutas cliente
    Route::post('guardar_cliente','ClientesController@guardar');
    Route::get('listar_clientes','ClientesController@listar_clientes');
    Route::post('actualizar_cliente','ClientesController@actualizar_cliente');
    Route::get('inhabilitar_cliente/{cliente}','ClientesController@inhabilitar_cliente');
    Route::get('select_clientes','ClientesController@select_clientes');


    // rutas configuraciones
    Route::post('registro_configuraciones', 'ConfiguracionesController@registro_configuraciones');
    Route::post('modificar_campo_configuraciones', 'ConfiguracionesController@modificar_campo_configuraciones');
    Route::get('traer_configuraciones', 'ConfiguracionesController@traer_configuraciones');

    //caja
    Route::post('ingresar_caja','ConfiguracionesController@ingresar_caja');
    Route::get('traer_cajas','ConfiguracionesController@traer_cajas');
    Route::post('editar_caja', 'ConfiguracionesController@editar_caja');
    Route::get('ver_usuarios_en_caja/{caja_id}', 'ConfiguracionesController@ver_usuarios_en_caja');
    Route::post('asignar_usuario_a_caja', 'ConfiguracionesController@asignar_usuario_a_caja');


    // PERIODO DE CAJA Y CAJAS
    Route::get('verifica_existe_periodo','PeriodoController@verifica_existe_periodo');
    Route::post('abrir_periodo_caja','PeriodoController@abrir_periodo_caja');
    Route::post('abrir_solo_caja','PeriodoController@abrir_solo_caja');
    Route::get('cargar_datos_caja_y_o_periodo/{caja_id}','PeriodoController@cargar_datos_caja_y_o_periodo');
    Route::get('cargar_datos_periodo','PeriodoController@cargar_datos_periodo');
    Route::get('captura_monto_cierre/{reg_caja_venta_id}','PeriodoController@captura_monto_cierre');

    Route::post('cerrar_solo_caja','PeriodoController@cerrar_solo_caja');
    Route::get('cerrar_periodo/{periodo_caja_id}/{estado_caja}','PeriodoController@cerrar_periodo');

    Route::get('all_cajas', 'PeriodoController@all_cajas');


    //mis ventas reportes de todo
    Route::post('mis_ventas', 'PeriodoController@mis_ventas');
    Route::get('mis_ventas_id/{r_c_v_id}/{mi_monto_inicio}', 'PeriodoController@mis_ventas_id');

    Route::post('reporte_cajas','PeriodoController@reporte_por_cajas');
    Route::post('reporte_periodo','PeriodoController@reporte_periodo');

    Route::get('cajas_periodo/{periodo_id}', 'PeriodoController@cajas_periodo');




});


Route::post('codificar_xml', 'ConfiguracionesController@codificar_xml');

Route::post('ejemplo_erik','EjemploDteController@ejemploDte');
