<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// rutas categoria
Route::post('registro_categoria', 'CategoriaController@registro_categoria');
Route::get('traer_categorias', 'CategoriaController@traer_categorias');
Route::post('modificar_campo_categoria', 'CategoriaController@modificar_campo_categoria');
Route::get('buscar_categoria/{categoria}', 'CategoriaController@buscar_categoria');
Route::get('cantidad_categoria', 'CategoriaController@cantidad_categoria');
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


// Route::get('traer_categorias', 'CategoriaController@traer_categorias');
// Route::post('modificar_campo_categoria', 'CategoriaController@modificar_campo_categoria');
// Route::get('buscar_categoria/{categoria}', 'CategoriaController@buscar_categoria');
// Route::get('cantidad_categoria', 'CategoriaController@cantidad_categoria');

