<?php

namespace App\Http\Middleware\ApiAuthMiddleware;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas de pruebas
Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('/prueba/{nombre?}', function ($nombre = null) {
    $saludo = '<h2> Este proyecto esta siendo desarrollado en Laravel Framework 8.83.27</h2>';
    $saludo .= '<br> <b>php artisan --version </b>-> usa este comado para conocer la version de laravel';
    $saludo .= '<br><br>' . $nombre . ' esta activo desarrollando en Laravel';
    return $saludo;
});*/

Route::get('/saludos/{nombre?}', function ($nombre = null) {
    $saludos = '<h2> Este proyecto esta siendo desarrollado en Laravel Framework 8.83.27</h2>';
    $saludos .= '<br> <b>php artisan --version </b>-> usa este comado para conocer la version de laravel';
    $saludos .= '<br><br>' . $nombre . ' esta activo desarrollando en Laravel';
    return view('saludos',array(
        'el_saludo' => $saludos
    ));
});



Route::get('/animales', 'App\Http\Controllers\AnimalesController@index');
Route::get('/testorm',  'App\Http\Controllers\AnimalesController@testOrm');  

// --------------------------------------------------------------------------------

// RUTAS DEL API 

    /* Metodos HTTP comunes
        
        * GET : Conseguir datos o recursos
        * POST : Guardar datos, recursos o hacer logica desde un formulario
        * PUT : Actualizar datos o recursos
        * DELETE : Eliminar datos o recursos

    */

    // Rutas de pruebas

    //Route::get('/usuarios/pruebas',  'App\Http\Controllers\UserController@pruebas');  
    //Route::get('/categoria/pruebas',  'App\Http\Controllers\CategoryController@pruebas');  
    //Route::get('/entrada/pruebas',  'App\Http\Controllers\PostController@pruebas');  

    // Rutas del contolador de usuarios

    Route::post('/api/register',  'App\Http\Controllers\UserController@register'); 
    Route::post('/api/login',  'App\Http\Controllers\UserController@login'); 
    Route::put('/api/user/update',  'App\Http\Controllers\UserController@update');
    Route::post('/api/user/upload', 'App\Http\Controllers\UserController@upload')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class); 
    Route::post('/api/user/avatar/{filename}', 'App\Http\Controllers\UserController@getImage'); 
    Route::get('/api/user/detail/{id}', 'App\Http\Controllers\UserController@detail'); 

    // Cotrolador de Categoria

    Route::resource('/api/category', 'App\Http\Controllers\CategoryController'); 

    // Cotrolador de Entradas

    Route::resource('/api/post', 'App\Http\Controllers\PostController'); 
    Route::post('/api/post/upload', 'App\Http\Controllers\PostController@upload'); 
    Route::get('/api/post/image/{filename}', 'App\Http\Controllers\PostController@getImage'); 
    Route::get('/api/post/category/{id}', 'App\Http\Controllers\PostController@getPostsByCategory'); 
    Route::get('/api/post/user/{id}', 'App\Http\Controllers\PostController@getPostsbyUser'); 