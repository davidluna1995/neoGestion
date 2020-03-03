<?php
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

use Illuminate\Http\Request;

Route::post('auth/login', 'ApiController@login');
Route::post('register', 'ApiController@register');

Route::group(['middleware' => 'auth.jwt'], function () {

    //login y user
    Route::get('auth/user', 'ApiController@user');
    Route::post('auth/logout', 'ApiController@logout');
    Route::get('allUser', 'ApiController@allUser');
    Route::post('modificar_perfil', 'ApiController@modificar_perfil');
    Route::post('cambiar_password', 'ApiController@cambiar_password');
    Route::post('crear_usuario', 'ApiController@crear_usuario');
    Route::get('traer_usuarios', 'ApiController@traer_usuarios');
    Route::post('delete_usuario', 'ApiController@delete_usuario');
    
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


});
